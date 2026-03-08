<?php
/**
 * EduWrite AI - AI Orchestration Service
 *
 * Central AI integration layer handling prompt assembly, policy enforcement,
 * provider communication (Abacus.AI), response processing, rate limiting,
 * session management, and usage tracking.
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../helpers/database.php';
require_once __DIR__ . '/../helpers/security.php';

class AIService
{
    private const PROVIDER_ENDPOINT = 'https://api.abacus.ai/api/v0/predict';

    private const SUPPORTED_MODES = [
        'brainstorm', 'outline', 'draft_coach', 'revision',
        'grammar', 'reflection', 'analysis', 'planning',
        'interpret', 'redirect',
    ];

    private const MODE_DESCRIPTIONS = [
        'brainstorm'  => 'Generate ideas and explore topics to help you get started with your writing.',
        'outline'     => 'Create a structured outline for your essay or paper with key points.',
        'draft_coach' => 'Receive guidance and coaching while drafting your work, without writing it for you.',
        'revision'    => 'Get feedback on your draft with suggestions for improvement.',
        'grammar'     => 'Check your writing for grammar, spelling, and punctuation issues.',
        'reflection'  => 'Reflect on your writing process and what you have learned.',
        'analysis'    => 'Analyze texts, arguments, and rhetorical strategies for deeper understanding.',
        'planning'    => 'Plan your writing project with timelines, research strategies, and structure.',
        'interpret'   => 'Interpret assignment prompts, rubrics, and expectations.',
        'redirect'    => 'Redirect requests that violate policy toward appropriate educational alternatives.',
    ];

    /**
     * Main orchestration method: validate, check policy, build prompt, call provider, log, return.
     */
    public function sendRequest(int $userId, string $requestType, string $prompt, array $context = []): array
    {
        if (empty(trim($prompt))) {
            throw new InvalidArgumentException('Prompt cannot be empty.');
        }
        if (!in_array($requestType, self::SUPPORTED_MODES, true) && $requestType !== 'other') {
            throw new InvalidArgumentException('Unsupported request type: ' . $requestType);
        }

        $rateLimitOk = $this->checkRateLimit($userId);
        if (!$rateLimitOk) {
            throw new RuntimeException('Rate limit exceeded. Please wait before sending another request.');
        }

        $documentId = $context['document_id'] ?? null;
        $assignmentId = $context['assignment_id'] ?? null;
        $sessionId = $context['session_id'] ?? null;

        $policyResult = $this->checkPolicy($userId, $documentId, $assignmentId, $requestType, $prompt);

        $requestId = $this->logRequest([
            'session_id'          => $sessionId,
            'user_id'             => $userId,
            'document_id'         => $documentId,
            'request_type'        => $requestType,
            'prompt'              => $prompt,
            'selected_text'       => $context['selected_text'] ?? null,
            'context_summary'     => $context['context_summary'] ?? null,
            'status'              => 'processing',
            'policy_check_result' => $policyResult['result'],
            'risk_score'          => $policyResult['risk_score'],
        ]);

        if ($policyResult['result'] === 'denied') {
            $refusal = $this->handleRefusal($policyResult['reason'], $userId, $context);
            $this->logResponse($requestId, [
                'content'       => $refusal['message'],
                'response_type' => 'refusal',
                'metadata'      => json_encode(['violation_type' => $policyResult['reason']]),
            ]);
            update('ai_requests', ['status' => 'refused'], 'id = :id', [':id' => $requestId]);

            return [
                'request_id'    => $requestId,
                'status'        => 'refused',
                'response'      => $refusal['message'],
                'response_type' => 'refusal',
                'suggestion'    => $refusal['suggestion'] ?? null,
            ];
        }

        if ($policyResult['result'] === 'redirected') {
            $redirect = $this->generateRedirect($requestType, $context);
            $this->logResponse($requestId, [
                'content'       => $redirect['message'],
                'response_type' => 'redirect',
                'metadata'      => json_encode(['redirect_mode' => $redirect['suggested_mode'] ?? null]),
            ]);
            update('ai_requests', ['status' => 'redirected'], 'id = :id', [':id' => $requestId]);

            return [
                'request_id'     => $requestId,
                'status'         => 'redirected',
                'response'       => $redirect['message'],
                'response_type'  => 'redirect',
                'suggested_mode' => $redirect['suggested_mode'] ?? null,
            ];
        }

        $user = fetch("SELECT school_id FROM users WHERE id = :id", [':id' => $userId]);
        $schoolId = $user ? (int) $user['school_id'] : null;

        $model = $this->selectModel($requestType, $schoolId);
        $messages = $this->buildPrompt($requestType, $prompt, $context);
        $providerConfig = $this->getProviderConfig($schoolId);

        $payload = $this->buildProviderPayload($model, $messages, [
            'max_tokens'  => $providerConfig['max_tokens'] ?? AI_MAX_TOKENS,
            'temperature' => $providerConfig['temperature'] ?? AI_DEFAULT_TEMPERATURE,
        ]);

        $startTime = microtime(true);

        try {
            $rawResponse = $this->sendToProvider($payload);
            $parsed = $this->parseProviderResponse($rawResponse);
        } catch (Exception $e) {
            try {
                $rawResponse = $this->retryWithFallback($payload, 1);
                $parsed = $this->parseProviderResponse($rawResponse);
            } catch (Exception $retryEx) {
                $this->logResponse($requestId, [
                    'content'       => 'The AI assistant is temporarily unavailable. Please try again shortly.',
                    'response_type' => 'error',
                    'metadata'      => json_encode(['error' => $retryEx->getMessage()]),
                ]);
                update('ai_requests', ['status' => 'failed'], 'id = :id', [':id' => $requestId]);

                return [
                    'request_id'    => $requestId,
                    'status'        => 'failed',
                    'response'      => 'The AI assistant is temporarily unavailable. Please try again shortly.',
                    'response_type' => 'error',
                ];
            }
        }

        $elapsedMs = (int) ((microtime(true) - $startTime) * 1000);
        $processedContent = $this->postProcessResponse($parsed['content'], $requestType);

        $responseType = $this->mapRequestToResponseType($requestType);

        $this->logResponse($requestId, [
            'content'       => $processedContent,
            'response_type' => $responseType,
            'metadata'      => json_encode([
                'model'       => $model,
                'tokens_used' => $parsed['tokens_used'] ?? null,
                'finish_reason' => $parsed['finish_reason'] ?? null,
            ]),
        ]);

        update('ai_requests', [
            'status'           => 'completed',
            'model_used'       => $model,
            'provider'         => 'abacus',
            'tokens_used'      => $parsed['tokens_used'] ?? null,
            'response_time_ms' => $elapsedMs,
        ], 'id = :id', [':id' => $requestId]);

        if ($sessionId) {
            query("UPDATE ai_sessions SET request_count = request_count + 1 WHERE id = :sid", [':sid' => $sessionId]);
        }

        return [
            'request_id'    => $requestId,
            'status'        => 'completed',
            'response'      => $processedContent,
            'response_type' => $responseType,
            'model'         => $model,
            'tokens_used'   => $parsed['tokens_used'] ?? null,
            'response_time' => $elapsedMs,
        ];
    }

    /**
     * Classify user intent from prompt text.
     */
    public function classifyRequestType(string $prompt): string
    {
        $prompt = strtolower(trim($prompt));

        $patterns = [
            'brainstorm'  => ['/brainstorm/', '/ideas?\s+(for|about)/', '/think\s+of/', '/come\s+up\s+with/', '/suggest\s+topics?/'],
            'outline'     => ['/outline/', '/structure/', '/organize\s+(my|the)/', '/table\s+of\s+contents/', '/create\s+a\s+plan/'],
            'draft_coach' => ['/help\s+me\s+(write|draft|start)/', '/how\s+(do|should|can)\s+i\s+(write|begin|start)/', '/coaching/', '/guide\s+me/'],
            'revision'    => ['/revis(e|ion)/', '/feedback/', '/improve\s+(my|this)/', '/strengthen/', '/make\s+(it|this)\s+better/'],
            'grammar'     => ['/grammar/', '/spell(ing)?/', '/punctuation/', '/proofread/', '/typo/', '/correct\s+(my|the)/'],
            'reflection'  => ['/reflect/', '/what\s+did\s+i\s+learn/', '/writing\s+process/', '/self[- ]assess/', '/growth/'],
            'analysis'    => ['/analy[sz]e/', '/rhetorical/', '/argument/', '/evidence/', '/evaluate\s+(the|this)/'],
            'planning'    => ['/plan(ning)?/, /schedule/, /timeline/, /research\s+strateg/', '/how\s+long/'],
            'interpret'   => ['/what\s+does\s+(this|the)\s+(prompt|assignment|rubric)/', '/interpret/', '/explain\s+(the|this)\s+(assignment|prompt)/', '/what\s+is\s+expected/'],
        ];

        foreach ($patterns as $type => $regexes) {
            foreach ($regexes as $regex) {
                if (preg_match($regex, $prompt)) {
                    return $type;
                }
            }
        }

        return 'draft_coach';
    }

    /**
     * Build the prompt messages array from template and context.
     */
    public function buildPrompt(string $requestType, string $userPrompt, array $context = []): array
    {
        $template = $this->getPromptTemplate($requestType);
        $messages = [];

        $messages[] = ['role' => 'system', 'content' => $template['system']];

        if (!empty($context['document_id'])) {
            $docContext = $this->extractDocumentContext((int) $context['document_id']);
            if ($docContext) {
                $messages[] = [
                    'role'    => 'system',
                    'content' => "The student's current document is titled \"{$docContext['title']}\" "
                        . "and has {$docContext['word_count']} words. "
                        . "Document status: {$docContext['status']}."
                ];
            }
        }

        if (!empty($context['assignment_id'])) {
            $assignContext = $this->extractAssignmentContext((int) $context['assignment_id']);
            if ($assignContext) {
                $messages[] = [
                    'role'    => 'system',
                    'content' => "Assignment: \"{$assignContext['title']}\". "
                        . "Type: {$assignContext['assignment_type']}. "
                        . (!empty($assignContext['instructions']) ? "Instructions: {$assignContext['instructions']}. " : '')
                        . (!empty($assignContext['due_date']) ? "Due: {$assignContext['due_date']}." : '')
                ];
            }
        }

        if (!empty($context['selected_text'])) {
            $formatted = $this->formatSelectedText($context['selected_text'], $context['full_content'] ?? '');
            $messages[] = ['role' => 'user', 'content' => "Selected text from my document:\n\"{$formatted}\""];
        }

        if (!empty($context['history'])) {
            foreach (array_slice($context['history'], -6) as $msg) {
                $messages[] = [
                    'role'    => $msg['role'] ?? 'user',
                    'content' => $msg['content'] ?? '',
                ];
            }
        }

        $userContent = str_replace('{user_prompt}', $userPrompt, $template['user']);
        $messages[] = ['role' => 'user', 'content' => $userContent];

        return $messages;
    }

    /**
     * Get the prompt template for a given mode.
     */
    public function getPromptTemplate(string $requestType): array
    {
        $templates = [
            'brainstorm' => [
                'system' => "You are a creative writing coach for students. Your role is to help students brainstorm ideas, explore topics, and generate creative angles for their writing. Never write content for the student. Instead, ask probing questions, suggest perspectives they haven't considered, and help them discover their own ideas. Present ideas as possibilities, not finished thoughts. Encourage the student to evaluate which ideas resonate with them and why.",
                'user'   => "I need help brainstorming. {user_prompt}",
            ],
            'outline' => [
                'system' => "You are a writing structure coach for students. Help students organize their thoughts into a logical outline. Suggest possible structures (chronological, compare/contrast, cause/effect, etc.) and explain why each might work for their topic. Guide them to create their own outline by asking about their main argument, key supporting points, and evidence. Do not write the outline for them—help them build it step by step.",
                'user'   => "I need help creating an outline. {user_prompt}",
            ],
            'draft_coach' => [
                'system' => "You are a supportive writing coach for students. Your job is to guide students through the drafting process without writing content for them. Ask questions that help them clarify their thinking. Suggest techniques for overcoming writer's block. Offer encouragement and remind them that first drafts don't need to be perfect. If they share a passage, comment on what's working and ask questions about what they want to develop further. Never produce paragraphs or essays for the student.",
                'user'   => "{user_prompt}",
            ],
            'revision' => [
                'system' => "You are a revision mentor for students. When reviewing student writing, provide specific, actionable feedback organized by priority. Focus on: 1) Argument clarity and thesis strength, 2) Organization and flow, 3) Evidence and support, 4) Voice and style. Point out both strengths and areas for improvement. Suggest revision strategies but do not rewrite passages. Ask the student what they think could be improved before offering your analysis.",
                'user'   => "Please review my writing and suggest improvements. {user_prompt}",
            ],
            'grammar' => [
                'system' => "You are a grammar and mechanics tutor for students. When checking writing, identify specific grammatical errors, spelling mistakes, and punctuation issues. Explain the rule behind each correction so the student learns. Group errors by type when there are patterns. Suggest the correction but also explain why the original is incorrect. Focus on teaching grammar rules, not just fixing text.",
                'user'   => "Please check my writing for grammar and mechanics issues. {user_prompt}",
            ],
            'reflection' => [
                'system' => "You are a reflective writing guide for students. Help students think critically about their own writing process, growth, and learning. Ask questions that promote metacognition: What strategies worked? What was challenging? What would they do differently? Help them connect their writing experience to broader learning goals. Encourage honest self-assessment and goal-setting for future work.",
                'user'   => "I want to reflect on my writing. {user_prompt}",
            ],
            'analysis' => [
                'system' => "You are a critical analysis coach for students. Help students analyze texts, arguments, and rhetorical strategies. Guide them through identifying claims, evidence, assumptions, and rhetorical devices. Ask questions that deepen their analysis rather than providing conclusions. Help them develop their own interpretations by pointing out elements they might examine more closely. Do not write the analysis for them.",
                'user'   => "Help me analyze this. {user_prompt}",
            ],
            'planning' => [
                'system' => "You are a writing project planner for students. Help students break down writing assignments into manageable steps. Assist with creating timelines, identifying research needs, and planning revision rounds. Ask about their available time, the assignment requirements, and their writing process preferences. Help them set realistic goals and milestones. Do not do the research or writing for them.",
                'user'   => "Help me plan my writing project. {user_prompt}",
            ],
            'interpret' => [
                'system' => "You are an assignment interpretation guide for students. Help students understand what is being asked of them in assignment prompts, rubrics, and instructions. Break down complex prompts into specific tasks. Clarify academic terminology and expectations. Help students identify the key requirements and evaluation criteria. Do not tell them what to write—help them understand what is expected.",
                'user'   => "Help me understand this assignment. {user_prompt}",
            ],
            'redirect' => [
                'system' => "You are an educational integrity guide. When a student's request cannot be fulfilled because it would undermine their learning, explain why in a supportive, non-judgmental way. Suggest alternative approaches that would help them learn while still making progress on their work. Frame the redirect as an opportunity for growth, not a punishment.",
                'user'   => "{user_prompt}",
            ],
        ];

        return $templates[$requestType] ?? $templates['draft_coach'];
    }

    /**
     * Check AI policy for a request.
     */
    public function checkPolicy(int $userId, ?int $documentId, ?int $assignmentId, string $requestType, string $prompt = ''): array
    {
        $riskScore = $this->evaluateRiskScore($prompt, $requestType);
        $suspicious = $this->detectSuspiciousIntent($prompt);

        if ($suspicious['detected']) {
            $this->recordViolation($userId, [
                'document_id'    => $documentId,
                'assignment_id'  => $assignmentId,
                'violation_type' => $suspicious['type'],
                'severity'       => $suspicious['severity'],
                'description'    => $suspicious['description'],
                'evidence'       => $prompt,
            ]);

            return [
                'result'     => 'denied',
                'risk_score' => $riskScore,
                'reason'     => $suspicious['type'],
                'message'    => $suspicious['description'],
            ];
        }

        if ($assignmentId) {
            $assignment = fetch(
                "SELECT a.id, a.ai_policy_id, a.class_id, c.school_id
                 FROM assignments a JOIN classes c ON c.id = a.class_id
                 WHERE a.id = :id",
                [':id' => $assignmentId]
            );

            if ($assignment) {
                $rules = fetchAll(
                    "SELECT * FROM ai_policy_rules
                     WHERE is_active = 1
                       AND (assignment_id = :aid OR class_id = :cid
                            OR (school_id = :sid AND class_id IS NULL AND assignment_id IS NULL))
                     ORDER BY
                         CASE WHEN assignment_id IS NOT NULL THEN 1
                              WHEN class_id IS NOT NULL THEN 2
                              ELSE 3 END,
                         priority DESC",
                    [':aid' => $assignmentId, ':cid' => $assignment['class_id'], ':sid' => $assignment['school_id']]
                );

                foreach ($rules as $rule) {
                    if ($rule['rule_type'] === 'deny') {
                        $categoryMatch = $this->matchesModeToCategory($requestType, $rule['category']);
                        if ($categoryMatch) {
                            return [
                                'result'     => 'denied',
                                'risk_score' => $riskScore,
                                'reason'     => 'policy_rule',
                                'message'    => $rule['message'] ?? 'This type of AI assistance is not allowed for this assignment.',
                            ];
                        }
                    }

                    if ($rule['rule_type'] === 'redirect') {
                        $categoryMatch = $this->matchesModeToCategory($requestType, $rule['category']);
                        if ($categoryMatch) {
                            return [
                                'result'     => 'redirected',
                                'risk_score' => $riskScore,
                                'reason'     => 'policy_redirect',
                                'message'    => $rule['message'] ?? 'This request has been redirected to a more appropriate assistance mode.',
                            ];
                        }
                    }
                }
            }
        }

        if ($riskScore >= 0.8) {
            return [
                'result'     => 'flagged',
                'risk_score' => $riskScore,
                'reason'     => 'high_risk_score',
                'message'    => 'Request flagged for elevated risk.',
            ];
        }

        return [
            'result'     => 'allowed',
            'risk_score' => $riskScore,
            'reason'     => null,
            'message'    => null,
        ];
    }

    /**
     * Evaluate risk score from 0.0 to 1.0.
     */
    public function evaluateRiskScore(string $prompt, string $requestType): float
    {
        $score = 0.0;
        $lower = strtolower($prompt);

        $directAnswerKeywords = [
            'write my essay' => 0.9, 'write this for me' => 0.9,
            'give me the answer' => 0.85, 'do my homework' => 0.85,
            'write the whole' => 0.8, 'complete this assignment' => 0.8,
            'generate an essay' => 0.7, 'write a paper on' => 0.7,
            'create a full' => 0.6, 'write a paragraph about' => 0.5,
        ];

        foreach ($directAnswerKeywords as $keyword => $weight) {
            if (strpos($lower, $keyword) !== false) {
                $score = max($score, $weight);
            }
        }

        $launderingKeywords = [
            'make it undetectable' => 0.95, 'bypass detection' => 0.95,
            'sound like me' => 0.6, 'rewrite to avoid' => 0.85,
            'make it look like i wrote' => 0.9, 'humanize this' => 0.5,
            'paraphrase to hide' => 0.85, 'disguise the source' => 0.9,
        ];

        foreach ($launderingKeywords as $keyword => $weight) {
            if (strpos($lower, $keyword) !== false) {
                $score = max($score, $weight);
            }
        }

        if (in_array($requestType, ['brainstorm', 'planning', 'interpret', 'reflection'], true)) {
            $score *= 0.6;
        }
        if (in_array($requestType, ['grammar'], true)) {
            $score *= 0.7;
        }

        return round(min(1.0, $score), 2);
    }

    /**
     * Detect suspicious intent patterns.
     */
    public function detectSuspiciousIntent(string $prompt): array
    {
        $lower = strtolower(trim($prompt));

        $directAnswerPatterns = [
            '/write\s+(my|the|an?|this)\s+(entire|whole|full|complete)?\s*(essay|paper|assignment|report|thesis)/i',
            '/^(just\s+)?(give|tell|show)\s+me\s+the\s+answer/i',
            '/do\s+(my|this|the)\s+(homework|assignment|work|project)\s+(for me)?/i',
            '/^write\s+(about|on)\s+.{10,}$/i',
            '/generate\s+a\s+(complete|full|whole)\s+(essay|paper|report)/i',
        ];

        foreach ($directAnswerPatterns as $pattern) {
            if (preg_match($pattern, $lower)) {
                return [
                    'detected'    => true,
                    'type'        => 'direct_answer',
                    'severity'    => 'high',
                    'description' => 'Request appears to ask the AI to produce complete work rather than provide guidance.',
                ];
            }
        }

        $launderingPatterns = [
            '/make\s+(it|this)\s+(sound\s+like|look\s+like)\s+(i|a\s+student)\s+wrote/i',
            '/rewrite.{0,30}(undetectable|avoid\s+detection|bypass)/i',
            '/humanize.{0,20}(ai|generated|text)/i',
            '/paraphrase.{0,20}(hide|mask|conceal|disguise)/i',
            '/(remove|strip|eliminate).{0,20}(ai|detection|traces)/i',
        ];

        foreach ($launderingPatterns as $pattern) {
            if (preg_match($pattern, $lower)) {
                return [
                    'detected'    => true,
                    'type'        => 'laundering',
                    'severity'    => 'critical',
                    'description' => 'Request appears to attempt to disguise AI-generated content as original student work.',
                ];
            }
        }

        $rewritePatterns = [
            '/^(just\s+)?rewrite\s+(this|the|my)\s+(entire|whole|full)/i',
            '/rewrite\s+.{0,20}from\s+scratch/i',
            '/replace\s+(all|everything)\s+(with|using)/i',
        ];

        foreach ($rewritePatterns as $pattern) {
            if (preg_match($pattern, $lower)) {
                return [
                    'detected'    => true,
                    'type'        => 'rewrite_detection',
                    'severity'    => 'medium',
                    'description' => 'Request appears to ask for complete content rewriting rather than guided revision.',
                ];
            }
        }

        return ['detected' => false, 'type' => null, 'severity' => null, 'description' => null];
    }

    /**
     * Extract document content for prompt context.
     */
    public function extractDocumentContext(int $documentId): ?array
    {
        return fetch(
            "SELECT id, title, content, word_count, status FROM documents WHERE id = :id",
            [':id' => $documentId]
        );
    }

    /**
     * Extract assignment details for prompt context.
     */
    public function extractAssignmentContext(int $assignmentId): ?array
    {
        return fetch(
            "SELECT id, title, description, instructions, assignment_type, due_date,
                    min_words, max_words
             FROM assignments WHERE id = :id",
            [':id' => $assignmentId]
        );
    }

    /**
     * Format selected text with surrounding context.
     */
    public function formatSelectedText(string $selectedText, string $fullContent): string
    {
        $selected = trim($selectedText);
        if (mb_strlen($selected) > 2000) {
            $selected = mb_substr($selected, 0, 2000) . '…';
        }
        return $selected;
    }

    /**
     * Send a request to the Abacus.AI provider.
     */
    public function sendToProvider(array $payload): array
    {
        $apiKey = defined('AI_API_KEY') ? AI_API_KEY : '';
        $endpoint = self::PROVIDER_ENDPOINT;
        $timeout = defined('AI_REQUEST_TIMEOUT') ? AI_REQUEST_TIMEOUT : 30;

        $jsonPayload = json_encode($payload);

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $jsonPayload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
                'Accept: application/json',
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false || !empty($error)) {
            throw new RuntimeException('AI provider request failed: ' . ($error ?: 'Unknown error'));
        }

        if ($httpCode >= 400) {
            $body = json_decode($response, true);
            $errorMsg = $body['error']['message'] ?? $body['message'] ?? 'HTTP ' . $httpCode;
            throw new RuntimeException('AI provider returned error: ' . $errorMsg);
        }

        $decoded = json_decode($response, true);
        if ($decoded === null) {
            throw new RuntimeException('Invalid JSON response from AI provider.');
        }

        return $decoded;
    }

    /**
     * Build the provider API payload.
     */
    public function buildProviderPayload(string $model, array $messages, array $params = []): array
    {
        return [
            'model'       => $model,
            'messages'    => $messages,
            'max_tokens'  => $params['max_tokens'] ?? AI_MAX_TOKENS,
            'temperature' => $params['temperature'] ?? AI_DEFAULT_TEMPERATURE,
            'stream'      => false,
        ];
    }

    /**
     * Parse provider response into normalized format.
     */
    public function parseProviderResponse(array $response): array
    {
        $content = '';
        $tokensUsed = null;
        $finishReason = null;

        if (isset($response['choices'][0]['message']['content'])) {
            $content = $response['choices'][0]['message']['content'];
        } elseif (isset($response['result'])) {
            $content = is_string($response['result']) ? $response['result'] : json_encode($response['result']);
        } elseif (isset($response['output'])) {
            $content = is_string($response['output']) ? $response['output'] : json_encode($response['output']);
        } elseif (isset($response['response'])) {
            $content = is_string($response['response']) ? $response['response'] : json_encode($response['response']);
        }

        if (isset($response['usage']['total_tokens'])) {
            $tokensUsed = (int) $response['usage']['total_tokens'];
        } elseif (isset($response['usage']['completion_tokens'], $response['usage']['prompt_tokens'])) {
            $tokensUsed = (int) $response['usage']['completion_tokens'] + (int) $response['usage']['prompt_tokens'];
        }

        if (isset($response['choices'][0]['finish_reason'])) {
            $finishReason = $response['choices'][0]['finish_reason'];
        }

        return [
            'content'       => $content,
            'tokens_used'   => $tokensUsed,
            'finish_reason' => $finishReason,
        ];
    }

    /**
     * Get AI provider configuration for a school.
     */
    public function getProviderConfig(?int $schoolId): array
    {
        if ($schoolId) {
            $config = fetch(
                "SELECT * FROM ai_provider_config WHERE school_id = :sid AND is_enabled = 1 AND is_default = 1 LIMIT 1",
                [':sid' => $schoolId]
            );
            if ($config) {
                return $config;
            }
        }

        $config = fetch(
            "SELECT * FROM ai_provider_config WHERE school_id IS NULL AND is_enabled = 1 AND is_default = 1 LIMIT 1"
        );

        return $config ?: [
            'provider_name'  => AI_DEFAULT_PROVIDER,
            'api_endpoint'   => AI_API_ENDPOINT,
            'model_name'     => AI_DEFAULT_MODEL,
            'max_tokens'     => AI_MAX_TOKENS,
            'temperature'    => AI_DEFAULT_TEMPERATURE,
        ];
    }

    /**
     * Select the appropriate model for a request type and school.
     */
    public function selectModel(string $requestType, ?int $schoolId): string
    {
        if ($schoolId) {
            $config = fetch(
                "SELECT model_name FROM ai_provider_config WHERE school_id = :sid AND is_enabled = 1 AND is_default = 1 LIMIT 1",
                [':sid' => $schoolId]
            );
            if ($config) {
                return $config['model_name'];
            }
        }

        $complexModes = ['analysis', 'revision', 'draft_coach'];
        if (in_array($requestType, $complexModes, true)) {
            $allowed = defined('AI_ALLOWED_MODELS') ? AI_ALLOWED_MODELS : [];
            foreach (['abacus-gpt4', 'abacus-claude'] as $preferred) {
                if (in_array($preferred, $allowed, true)) {
                    return $preferred;
                }
            }
        }

        return defined('AI_DEFAULT_MODEL') ? AI_DEFAULT_MODEL : 'abacus-gpt35';
    }

    /**
     * Handle a refusal by generating an educational message.
     */
    public function handleRefusal(string $reason, int $userId, array $context = []): array
    {
        $messages = [
            'direct_answer' => [
                'message'    => "I can't write your work for you — that would bypass the learning that comes from writing itself. But I can definitely help you get started! Would you like me to help you brainstorm ideas, create an outline, or talk through your approach?",
                'suggestion' => 'Try using the Brainstorm or Outline mode to develop your own ideas with guidance.',
            ],
            'laundering' => [
                'message'    => "I'm not able to help disguise AI-generated text as your own work. Academic integrity is essential to your growth as a writer. Instead, I can help you develop your own writing skills through coaching, feedback on your drafts, or help with grammar and style.",
                'suggestion' => 'Use the Revision mode to get constructive feedback on your own writing.',
            ],
            'rewrite_detection' => [
                'message'    => "I can't rewrite your entire document for you, but I can help you improve it section by section. Try selecting specific paragraphs for revision feedback, or ask me about specific aspects you want to strengthen.",
                'suggestion' => 'Select specific sections and use the Revision mode for targeted improvement suggestions.',
            ],
            'policy_rule' => [
                'message'    => "This type of AI assistance isn't available for this assignment based on your teacher's settings. This is designed to help you develop specific skills on your own. Check the assignment guidelines for what types of help are allowed.",
                'suggestion' => 'Review the assignment instructions or ask your teacher about allowed AI assistance.',
            ],
            'exam_violation' => [
                'message'    => "AI assistance is not available during exams or tests. This policy helps ensure a fair assessment of your knowledge and skills.",
                'suggestion' => 'Focus on applying what you have learned. You can use AI assistance for practice and study before exams.',
            ],
            'excessive_use' => [
                'message'    => "You've been using AI assistance quite frequently on this document. Take some time to write independently — your own voice and ideas are what make your writing unique. You can come back for help later.",
                'suggestion' => 'Try writing the next section on your own, then come back for feedback.',
            ],
        ];

        return $messages[$reason] ?? [
            'message'    => "This request cannot be processed at this time. Please try a different approach or ask your teacher for guidance.",
            'suggestion' => 'Consider using a different AI assistance mode.',
        ];
    }

    /**
     * Generate a redirect to an appropriate educational alternative.
     */
    public function generateRedirect(string $requestType, array $context = []): array
    {
        $redirectMap = [
            'content_generation' => ['suggested_mode' => 'brainstorm', 'message' => "Instead of generating content directly, let's brainstorm ideas together. I'll ask questions to help you develop your own thoughts and find your unique angle on this topic."],
            'rewrite'            => ['suggested_mode' => 'revision', 'message' => "Rather than rewriting your text, let me give you specific feedback on what's working and what could be strengthened. That way you can make improvements in your own voice."],
            'other'              => ['suggested_mode' => 'draft_coach', 'message' => "Let me help you work through this as a writing coach. I'll guide you with questions and suggestions while you develop your own ideas."],
        ];

        $key = $this->mapModeToRedirectKey($requestType);
        return $redirectMap[$key] ?? $redirectMap['other'];
    }

    /**
     * Post-process AI response to clean up content.
     */
    public function postProcessResponse(string $response, string $requestType): string
    {
        $response = trim($response);
        $response = preg_replace('/^(As an AI|I\'m an AI|As a language model)[^.]*\.\s*/i', '', $response);
        $response = preg_replace('/\n{3,}/', "\n\n", $response);

        if (in_array($requestType, ['grammar', 'revision'], true)) {
            $response = preg_replace('/Here\'s the corrected version:?\s*\n/i', '', $response);
        }

        return trim($response);
    }

    /**
     * Log an AI request to the database.
     */
    public function logRequest(array $data): int
    {
        $requestId = insert('ai_requests', [
            'session_id'          => $data['session_id'],
            'user_id'             => $data['user_id'],
            'document_id'         => $data['document_id'] ?? null,
            'request_type'        => $data['request_type'],
            'prompt'              => $data['prompt'],
            'selected_text'       => $data['selected_text'] ?? null,
            'context_summary'     => $data['context_summary'] ?? null,
            'status'              => $data['status'] ?? 'pending',
            'policy_check_result' => $data['policy_check_result'] ?? null,
            'risk_score'          => $data['risk_score'] ?? null,
        ]);

        return (int) $requestId;
    }

    /**
     * Log an AI response to the database.
     */
    public function logResponse(int $requestId, array $data): int
    {
        $responseId = insert('ai_responses', [
            'request_id'    => $requestId,
            'content'       => $data['content'],
            'response_type' => $data['response_type'],
            'metadata'      => $data['metadata'] ?? null,
        ]);

        return (int) $responseId;
    }

    /**
     * Record a policy violation.
     */
    public function recordViolation(int $userId, array $data): int
    {
        $violationId = insert('policy_violations', [
            'user_id'        => $userId,
            'document_id'    => $data['document_id'] ?? null,
            'assignment_id'  => $data['assignment_id'] ?? null,
            'ai_request_id'  => $data['ai_request_id'] ?? null,
            'violation_type' => $data['violation_type'],
            'severity'       => $data['severity'] ?? 'low',
            'description'    => $data['description'] ?? null,
            'evidence'       => $data['evidence'] ?? null,
            'status'         => 'pending',
        ]);

        logSecurityEvent('policy_violation', [
            'user_id'        => $userId,
            'violation_type' => $data['violation_type'],
            'severity'       => $data['severity'] ?? 'low',
        ]);

        return (int) $violationId;
    }

    /**
     * Check per-user rate limiting.
     */
    public function checkRateLimit(int $userId): bool
    {
        $perMinute = defined('AI_RATE_LIMIT_PER_MINUTE') ? AI_RATE_LIMIT_PER_MINUTE : 10;
        $perHour = defined('AI_RATE_LIMIT_PER_HOUR') ? AI_RATE_LIMIT_PER_HOUR : 100;
        $perDay = defined('AI_RATE_LIMIT_PER_DAY') ? AI_RATE_LIMIT_PER_DAY : 500;

        $minuteCount = fetch(
            "SELECT COUNT(*) AS cnt FROM ai_requests WHERE user_id = :uid AND created_at >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)",
            [':uid' => $userId]
        );
        if ((int) ($minuteCount['cnt'] ?? 0) >= $perMinute) {
            return false;
        }

        $hourCount = fetch(
            "SELECT COUNT(*) AS cnt FROM ai_requests WHERE user_id = :uid AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)",
            [':uid' => $userId]
        );
        if ((int) ($hourCount['cnt'] ?? 0) >= $perHour) {
            return false;
        }

        $dayCount = fetch(
            "SELECT COUNT(*) AS cnt FROM ai_requests WHERE user_id = :uid AND created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)",
            [':uid' => $userId]
        );
        if ((int) ($dayCount['cnt'] ?? 0) >= $perDay) {
            return false;
        }

        return true;
    }

    /**
     * Get AI usage statistics for a user over a period.
     */
    public function getUsageStats(int $userId, string $period = '30d'): array
    {
        $interval = $this->periodToInterval($period);

        $stats = fetch(
            "SELECT COUNT(*) AS total_requests,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed,
                    SUM(CASE WHEN status = 'refused' THEN 1 ELSE 0 END) AS refused,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) AS failed,
                    COALESCE(SUM(tokens_used), 0) AS total_tokens,
                    COALESCE(AVG(response_time_ms), 0) AS avg_response_time
             FROM ai_requests
             WHERE user_id = :uid AND created_at >= DATE_SUB(NOW(), INTERVAL {$interval})",
            [':uid' => $userId]
        );

        $byType = fetchAll(
            "SELECT request_type, COUNT(*) AS count
             FROM ai_requests
             WHERE user_id = :uid AND created_at >= DATE_SUB(NOW(), INTERVAL {$interval})
             GROUP BY request_type
             ORDER BY count DESC",
            [':uid' => $userId]
        );

        $daily = fetchAll(
            "SELECT DATE(created_at) AS date, COUNT(*) AS count
             FROM ai_requests
             WHERE user_id = :uid AND created_at >= DATE_SUB(NOW(), INTERVAL {$interval})
             GROUP BY DATE(created_at)
             ORDER BY date ASC",
            [':uid' => $userId]
        );

        return [
            'total_requests'    => (int) ($stats['total_requests'] ?? 0),
            'completed'         => (int) ($stats['completed'] ?? 0),
            'refused'           => (int) ($stats['refused'] ?? 0),
            'failed'            => (int) ($stats['failed'] ?? 0),
            'total_tokens'      => (int) ($stats['total_tokens'] ?? 0),
            'avg_response_time' => round((float) ($stats['avg_response_time'] ?? 0)),
            'by_type'           => $byType,
            'daily'             => $daily,
        ];
    }

    /**
     * Get conversation history for a session.
     */
    public function getSessionHistory(int $userId, int $sessionId): array
    {
        $session = fetch(
            "SELECT * FROM ai_sessions WHERE id = :id AND user_id = :uid",
            [':id' => $sessionId, ':uid' => $userId]
        );
        if (!$session) {
            throw new RuntimeException('Session not found.');
        }

        $requests = fetchAll(
            "SELECT ar.id, ar.request_type, ar.prompt, ar.status, ar.created_at,
                    resp.content AS response_content, resp.response_type
             FROM ai_requests ar
             LEFT JOIN ai_responses resp ON resp.request_id = ar.id
             WHERE ar.session_id = :sid
             ORDER BY ar.created_at ASC",
            [':sid' => $sessionId]
        );

        return [
            'session'  => $session,
            'messages' => $requests,
        ];
    }

    /**
     * Create a new AI session.
     */
    public function createSession(int $userId, ?int $documentId, string $mode): array
    {
        if (!in_array($mode, self::SUPPORTED_MODES, true)) {
            throw new InvalidArgumentException('Unsupported AI mode: ' . $mode);
        }

        $assignmentId = null;
        if ($documentId) {
            $doc = fetch("SELECT assignment_id FROM documents WHERE id = :id AND user_id = :uid", [':id' => $documentId, ':uid' => $userId]);
            if ($doc) {
                $assignmentId = $doc['assignment_id'];
            }
        }

        $sessionToken = bin2hex(random_bytes(32));

        $sessionId = insert('ai_sessions', [
            'user_id'       => $userId,
            'document_id'   => $documentId,
            'assignment_id' => $assignmentId,
            'session_token' => $sessionToken,
            'mode'          => $mode,
            'request_count' => 0,
        ]);

        return [
            'session_id'    => (int) $sessionId,
            'session_token' => $sessionToken,
            'mode'          => $mode,
            'document_id'   => $documentId,
            'assignment_id' => $assignmentId,
        ];
    }

    /**
     * End an AI session.
     */
    public function endSession(int $sessionId): bool
    {
        $session = fetch("SELECT id, ended_at FROM ai_sessions WHERE id = :id", [':id' => $sessionId]);
        if (!$session) {
            throw new RuntimeException('Session not found.');
        }
        if ($session['ended_at'] !== null) {
            return true;
        }

        update('ai_sessions', ['ended_at' => date('Y-m-d H:i:s')], 'id = :id', [':id' => $sessionId]);
        return true;
    }

    /**
     * Retry a failed request with a fallback model.
     */
    public function retryWithFallback(array $payload, int $attempt): array
    {
        $maxRetries = 2;
        if ($attempt > $maxRetries) {
            throw new RuntimeException('All retry attempts exhausted.');
        }

        $fallbackModels = defined('AI_ALLOWED_MODELS') ? AI_ALLOWED_MODELS : ['abacus-gpt35'];
        $currentModel = $payload['model'] ?? '';

        $fallback = null;
        foreach ($fallbackModels as $model) {
            if ($model !== $currentModel) {
                $fallback = $model;
                break;
            }
        }

        if (!$fallback) {
            $fallback = $currentModel;
        }

        $payload['model'] = $fallback;
        $payload['max_tokens'] = min($payload['max_tokens'] ?? 2048, 1024);

        usleep(500000 * $attempt);

        return $this->sendToProvider($payload);
    }

    /**
     * Get list of supported AI modes.
     */
    public function getSupportedModes(): array
    {
        $modes = [];
        foreach (self::SUPPORTED_MODES as $mode) {
            $modes[] = [
                'id'          => $mode,
                'name'        => ucwords(str_replace('_', ' ', $mode)),
                'description' => self::MODE_DESCRIPTIONS[$mode] ?? '',
            ];
        }
        return $modes;
    }

    /**
     * Get description for a specific mode.
     */
    public function getModeDescription(string $mode): string
    {
        return self::MODE_DESCRIPTIONS[$mode] ?? 'Unknown mode.';
    }

    /**
     * Map a request type to a response type enum value.
     */
    private function mapRequestToResponseType(string $requestType): string
    {
        $map = [
            'brainstorm'  => 'suggestion',
            'outline'     => 'outline',
            'draft_coach' => 'feedback',
            'revision'    => 'feedback',
            'grammar'     => 'suggestion',
            'reflection'  => 'reflection',
            'analysis'    => 'analysis',
            'planning'    => 'suggestion',
            'interpret'   => 'feedback',
            'redirect'    => 'redirect',
        ];
        return $map[$requestType] ?? 'suggestion';
    }

    /**
     * Check if a mode matches a policy rule category.
     */
    private function matchesModeToCategory(string $mode, string $category): bool
    {
        $modeToCategories = [
            'brainstorm'  => ['brainstorm'],
            'outline'     => ['outline'],
            'draft_coach' => ['content_generation'],
            'revision'    => ['rewrite'],
            'grammar'     => ['grammar'],
            'reflection'  => ['reflection'],
            'analysis'    => ['analysis'],
            'planning'    => ['other'],
            'interpret'   => ['other'],
            'redirect'    => ['other'],
        ];

        $categories = $modeToCategories[$mode] ?? ['other'];
        return in_array($category, $categories, true);
    }

    /**
     * Map a mode to redirect key.
     */
    private function mapModeToRedirectKey(string $mode): string
    {
        if (in_array($mode, ['draft_coach'], true)) {
            return 'content_generation';
        }
        if (in_array($mode, ['revision', 'grammar'], true)) {
            return 'rewrite';
        }
        return 'other';
    }

    /**
     * Convert a period string to a SQL interval.
     */
    private function periodToInterval(string $period): string
    {
        $map = [
            '24h' => '24 HOUR', '7d' => '7 DAY', '30d' => '30 DAY',
            '90d' => '90 DAY', '1y' => '1 YEAR',
        ];
        return $map[$period] ?? '30 DAY';
    }
}
