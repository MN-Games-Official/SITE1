<?php
/**
 * EduWrite AI - Policy Enforcement Service
 *
 * Enforces AI usage policies and detects academic dishonesty patterns.
 * Manages cascading policy rules (school → class → assignment),
 * violation recording, and integrity flags.
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../helpers/database.php';
require_once __DIR__ . '/../helpers/security.php';

class PolicyService
{
    private const DIRECT_ANSWER_KEYWORDS = [
        'write my essay', 'write the essay for me', 'write my paper',
        'do my homework', 'do my assignment', 'complete my assignment',
        'give me the answer', 'tell me the answer', 'what is the answer',
        'just give me', 'just write it', 'write it for me',
        'generate my essay', 'create my paper', 'produce my assignment',
        'write my report', 'finish my paper', 'compose my essay',
        'write a full essay', 'write the whole thing', 'do this for me',
        'write my thesis', 'complete this for me', 'solve this for me',
        'write the introduction and conclusion', 'fill in the blanks',
        'write my response', 'give me a completed', 'hand me the answer',
    ];

    private const LAUNDERING_KEYWORDS = [
        'make it undetectable', 'make this undetectable',
        'bypass detection', 'bypass ai detection', 'avoid detection',
        'evade detection', 'fool the detector', 'trick the detector',
        'rewrite to sound like me', 'make it sound like i wrote it',
        'make it look like my writing', 'disguise this as my own',
        'humanize the text', 'humanize this ai text',
        'remove ai traces', 'remove ai fingerprints',
        'make it pass turnitin', 'beat turnitin', 'beat gptzero',
        'paraphrase to hide', 'reword to conceal',
        'make it original', 'make it seem original',
        'change enough to not get caught', 'rewrite so no one knows',
        'alter to appear human-written', 'mask the ai',
    ];

    private const REWRITE_KEYWORDS = [
        'rewrite my entire', 'rewrite the whole', 'rewrite everything',
        'replace all my writing', 'redo my entire', 'start over for me',
        'rewrite from scratch', 'completely rewrite', 'totally rewrite',
        'rewrite all of it', 'redo the whole thing',
    ];

    private const EXAM_KEYWORDS = [
        'exam answer', 'test answer', 'quiz answer',
        'final exam', 'midterm answer', 'exam question',
        'test question', 'answer this exam', 'help me cheat',
        'during my exam', 'during my test', 'while taking exam',
        'currently taking', 'right now on my test',
    ];

    /**
     * Main policy check: evaluate a request against all applicable rules.
     */
    public function checkRequest(int $userId, ?int $documentId, ?int $assignmentId, string $requestType, string $prompt): array
    {
        $indicators = [];

        $directAnswer = $this->detectDirectAnswerRequest($prompt);
        if ($directAnswer['detected']) {
            $indicators[] = ['type' => 'direct_answer', 'weight' => $directAnswer['confidence'], 'detail' => $directAnswer['match']];
        }

        $laundering = $this->detectLaunderingAttempt($prompt);
        if ($laundering['detected']) {
            $indicators[] = ['type' => 'laundering', 'weight' => $laundering['confidence'], 'detail' => $laundering['match']];
        }

        $rewrite = $this->detectRewriteRequest($prompt);
        if ($rewrite['detected']) {
            $indicators[] = ['type' => 'rewrite', 'weight' => $rewrite['confidence'], 'detail' => $rewrite['match']];
        }

        $context = ['document_id' => $documentId, 'assignment_id' => $assignmentId];
        $examViolation = $this->detectExamViolation($prompt, $context);
        if ($examViolation['detected']) {
            $indicators[] = ['type' => 'exam_violation', 'weight' => $examViolation['confidence'], 'detail' => $examViolation['match']];
        }

        $excessiveUse = $this->detectExcessiveUse($userId, $documentId);
        if ($excessiveUse['detected']) {
            $indicators[] = ['type' => 'excessive_use', 'weight' => $excessiveUse['severity_weight'], 'detail' => $excessiveUse['detail']];
        }

        $riskScore = $this->calculateRiskScore($prompt, $indicators);

        $schoolId = null;
        $classId = null;
        if ($assignmentId) {
            $assignment = fetch(
                "SELECT a.class_id, c.school_id FROM assignments a JOIN classes c ON c.id = a.class_id WHERE a.id = :id",
                [':id' => $assignmentId]
            );
            if ($assignment) {
                $classId = (int) $assignment['class_id'];
                $schoolId = (int) $assignment['school_id'];
            }
        } elseif ($documentId) {
            $doc = fetch(
                "SELECT d.assignment_id FROM documents d WHERE d.id = :id",
                [':id' => $documentId]
            );
            if ($doc && $doc['assignment_id']) {
                $assignment = fetch(
                    "SELECT a.class_id, c.school_id FROM assignments a JOIN classes c ON c.id = a.class_id WHERE a.id = :id",
                    [':id' => $doc['assignment_id']]
                );
                if ($assignment) {
                    $classId = (int) $assignment['class_id'];
                    $schoolId = (int) $assignment['school_id'];
                    $assignmentId = (int) $doc['assignment_id'];
                }
            }
        }

        $rules = $this->getEffectiveRules($schoolId, $classId, $assignmentId);
        $matchedRule = $this->matchRules($rules, $requestType, null);

        foreach ($indicators as $indicator) {
            if ($indicator['weight'] >= 0.7) {
                $violationType = $indicator['type'];
                $severity = $indicator['weight'] >= 0.9 ? 'critical' : ($indicator['weight'] >= 0.7 ? 'high' : 'medium');

                $this->recordViolation($userId, $violationType, $severity, [
                    'document_id'   => $documentId,
                    'assignment_id' => $assignmentId,
                    'description'   => $indicator['detail'],
                    'evidence'      => mb_substr($prompt, 0, 1000),
                    'risk_score'    => $riskScore,
                ]);

                $refusalMessage = $this->generateRefusalMessage($violationType, $context);

                return [
                    'allowed'    => false,
                    'result'     => 'denied',
                    'risk_score' => $riskScore,
                    'reason'     => $violationType,
                    'message'    => $refusalMessage,
                    'indicators' => $indicators,
                ];
            }
        }

        if ($matchedRule && $matchedRule['rule_type'] === 'deny') {
            return [
                'allowed'    => false,
                'result'     => 'denied',
                'risk_score' => $riskScore,
                'reason'     => 'policy_rule',
                'message'    => $matchedRule['message'] ?? 'This type of AI assistance is not allowed for this assignment.',
                'rule_id'    => $matchedRule['id'],
                'indicators' => $indicators,
            ];
        }

        if ($matchedRule && $matchedRule['rule_type'] === 'redirect') {
            $suggestion = $this->generateRedirectSuggestion($requestType, $context);
            return [
                'allowed'    => false,
                'result'     => 'redirected',
                'risk_score' => $riskScore,
                'reason'     => 'policy_redirect',
                'message'    => $matchedRule['message'] ?? $suggestion,
                'rule_id'    => $matchedRule['id'],
                'indicators' => $indicators,
            ];
        }

        if ($riskScore >= 0.6) {
            if ($documentId) {
                $this->createFlag($documentId, $userId, 'suspicious_pattern', 'warning', [
                    'risk_score' => $riskScore,
                    'indicators' => $indicators,
                ]);
            }
        }

        return [
            'allowed'    => true,
            'result'     => $riskScore >= 0.4 ? 'flagged' : 'allowed',
            'risk_score' => $riskScore,
            'reason'     => null,
            'message'    => null,
            'indicators' => $indicators,
        ];
    }

    /**
     * Get effective rules by cascading: assignment → class → school.
     */
    public function getEffectiveRules(?int $schoolId, ?int $classId, ?int $assignmentId): array
    {
        $rules = [];

        if ($assignmentId) {
            $assignmentRules = fetchAll(
                "SELECT * FROM ai_policy_rules WHERE assignment_id = :aid AND is_active = 1 ORDER BY priority DESC",
                [':aid' => $assignmentId]
            );
            $rules = array_merge($rules, $assignmentRules);
        }

        if ($classId) {
            $classRules = fetchAll(
                "SELECT * FROM ai_policy_rules WHERE class_id = :cid AND assignment_id IS NULL AND is_active = 1 ORDER BY priority DESC",
                [':cid' => $classId]
            );
            $rules = array_merge($rules, $classRules);
        }

        if ($schoolId) {
            $schoolRules = fetchAll(
                "SELECT * FROM ai_policy_rules WHERE school_id = :sid AND class_id IS NULL AND assignment_id IS NULL AND is_active = 1 ORDER BY priority DESC",
                [':sid' => $schoolId]
            );
            $rules = array_merge($rules, $schoolRules);
        }

        return $rules;
    }

    /**
     * Find matching rules for a request type and category.
     */
    public function matchRules(array $rules, string $requestType, ?string $category): ?array
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

        $matchCategories = $modeToCategories[$requestType] ?? ['other'];
        if ($category) {
            $matchCategories[] = $category;
        }

        foreach ($rules as $rule) {
            if (in_array($rule['category'], $matchCategories, true)) {
                if ($rule['rule_type'] === 'deny' || $rule['rule_type'] === 'redirect') {
                    return $rule;
                }
            }
        }

        return null;
    }

    /**
     * Evaluate a single rule against context.
     */
    public function evaluateRule(array $rule, array $context): bool
    {
        if (!$rule['is_active']) {
            return false;
        }

        if (!empty($rule['conditions'])) {
            $conditions = is_string($rule['conditions']) ? json_decode($rule['conditions'], true) : $rule['conditions'];
            if ($conditions) {
                if (isset($conditions['time_restricted'])) {
                    $now = date('H:i');
                    $start = $conditions['time_restricted']['start'] ?? '00:00';
                    $end = $conditions['time_restricted']['end'] ?? '23:59';
                    if ($now < $start || $now > $end) {
                        return false;
                    }
                }
                if (isset($conditions['max_requests_per_session']) && isset($context['session_request_count'])) {
                    if ((int) $context['session_request_count'] >= (int) $conditions['max_requests_per_session']) {
                        return true;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Calculate risk score from 0.0 to 1.0.
     */
    public function calculateRiskScore(string $prompt, array $indicators): float
    {
        if (empty($indicators)) {
            return 0.0;
        }

        $maxWeight = 0.0;
        $totalWeight = 0.0;
        foreach ($indicators as $indicator) {
            $weight = $indicator['weight'] ?? 0.0;
            $maxWeight = max($maxWeight, $weight);
            $totalWeight += $weight;
        }

        $count = count($indicators);
        $avgWeight = $totalWeight / $count;
        $combinedScore = ($maxWeight * 0.7) + ($avgWeight * 0.2) + (min($count / 5, 1.0) * 0.1);

        return round(min(1.0, $combinedScore), 2);
    }

    /**
     * Detect direct-answer seeking requests.
     */
    public function detectDirectAnswerRequest(string $prompt): array
    {
        $lower = strtolower(trim($prompt));

        foreach (self::DIRECT_ANSWER_KEYWORDS as $keyword) {
            if (strpos($lower, $keyword) !== false) {
                return ['detected' => true, 'confidence' => 0.85, 'match' => "Matched keyword: \"{$keyword}\""];
            }
        }

        $patterns = [
            '/^(please\s+)?(just\s+)?(write|compose|create|produce|generate)\s+(me\s+)?(a|an|the|my)\s+(entire|whole|full|complete)?\s*(essay|paper|report|assignment|thesis|response|paragraph)/i' => 0.9,
            '/^(can\s+you\s+)?(just\s+)?(give|tell|show)\s+me\s+(the\s+)?answer/i' => 0.85,
            '/(do|complete|finish|handle)\s+(my|this|the)\s+(homework|assignment|project|work)\s*(for\s+me)?/i' => 0.8,
            '/^write\s+about\s+.{20,}/i' => 0.6,
        ];

        foreach ($patterns as $pattern => $confidence) {
            if (preg_match($pattern, $lower)) {
                return ['detected' => true, 'confidence' => $confidence, 'match' => "Matched pattern for direct answer request"];
            }
        }

        return ['detected' => false, 'confidence' => 0.0, 'match' => null];
    }

    /**
     * Detect AI laundering attempts.
     */
    public function detectLaunderingAttempt(string $prompt): array
    {
        $lower = strtolower(trim($prompt));

        foreach (self::LAUNDERING_KEYWORDS as $keyword) {
            if (strpos($lower, $keyword) !== false) {
                return ['detected' => true, 'confidence' => 0.95, 'match' => "Matched laundering keyword: \"{$keyword}\""];
            }
        }

        $patterns = [
            '/make\s+(it|this|the\s+text)\s+(sound|look|seem|appear)\s+(like|as\s+if)\s+(i|a\s+student|a\s+human)\s+wrote/i' => 0.95,
            '/(rewrite|rephrase|paraphrase).{0,40}(undetectable|avoid|bypass|evade)\s*(ai\s+)?detection/i' => 0.95,
            '/(humanize|naturalize).{0,30}(ai|generated|gpt|chatgpt|text)/i' => 0.8,
            '/(remove|strip|eliminate|hide).{0,20}(ai|artificial|generated)\s*(traces|fingerprint|markers|signature)/i' => 0.9,
            '/(pass|beat|fool|trick|bypass).{0,20}(turnitin|gptzero|originality|detection|detector|plagiarism)/i' => 0.95,
        ];

        foreach ($patterns as $pattern => $confidence) {
            if (preg_match($pattern, $lower)) {
                return ['detected' => true, 'confidence' => $confidence, 'match' => "Matched laundering pattern"];
            }
        }

        return ['detected' => false, 'confidence' => 0.0, 'match' => null];
    }

    /**
     * Detect full content rewriting requests.
     */
    public function detectRewriteRequest(string $prompt): array
    {
        $lower = strtolower(trim($prompt));

        foreach (self::REWRITE_KEYWORDS as $keyword) {
            if (strpos($lower, $keyword) !== false) {
                return ['detected' => true, 'confidence' => 0.7, 'match' => "Matched rewrite keyword: \"{$keyword}\""];
            }
        }

        $patterns = [
            '/^(please\s+)?(just\s+)?rewrite\s+(this|the|my)\s+(entire|whole|full|complete)/i' => 0.8,
            '/rewrite\s+.{0,30}from\s+scratch/i' => 0.75,
            '/replace\s+(all|everything|every\s+word|the\s+entire)/i' => 0.7,
            '/^(completely|totally|entirely)\s+rewrite/i' => 0.75,
        ];

        foreach ($patterns as $pattern => $confidence) {
            if (preg_match($pattern, $lower)) {
                return ['detected' => true, 'confidence' => $confidence, 'match' => "Matched rewrite pattern"];
            }
        }

        return ['detected' => false, 'confidence' => 0.0, 'match' => null];
    }

    /**
     * Detect exam or test help requests.
     */
    public function detectExamViolation(string $prompt, array $context = []): array
    {
        $lower = strtolower(trim($prompt));

        foreach (self::EXAM_KEYWORDS as $keyword) {
            if (strpos($lower, $keyword) !== false) {
                return ['detected' => true, 'confidence' => 0.85, 'match' => "Matched exam keyword: \"{$keyword}\""];
            }
        }

        if (!empty($context['assignment_id'])) {
            $strictness = $this->getStrictnessLevel((int) $context['assignment_id']);
            if ($strictness === 'exam') {
                return ['detected' => true, 'confidence' => 1.0, 'match' => "Assignment has exam-level strictness"];
            }
        }

        $patterns = [
            '/(during|while|in\s+the\s+middle\s+of)\s+(my|the|a)\s+(exam|test|quiz|final|midterm)/i' => 0.9,
            '/(answer|solve|complete)\s+.{0,20}(exam|test|quiz)\s+(question|problem)/i' => 0.85,
            '/help\s+me\s+(cheat|pass)\s+(on|in|during)/i' => 0.95,
        ];

        foreach ($patterns as $pattern => $confidence) {
            if (preg_match($pattern, $lower)) {
                return ['detected' => true, 'confidence' => $confidence, 'match' => "Matched exam violation pattern"];
            }
        }

        return ['detected' => false, 'confidence' => 0.0, 'match' => null];
    }

    /**
     * Detect excessive AI usage for a document.
     */
    public function detectExcessiveUse(int $userId, ?int $documentId): array
    {
        if (!$documentId) {
            return ['detected' => false, 'severity_weight' => 0.0, 'detail' => null];
        }

        $hourCount = fetch(
            "SELECT COUNT(*) AS cnt FROM ai_requests
             WHERE user_id = :uid AND document_id = :did AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)",
            [':uid' => $userId, ':did' => $documentId]
        );

        $totalCount = fetch(
            "SELECT COUNT(*) AS cnt FROM ai_requests WHERE user_id = :uid AND document_id = :did",
            [':uid' => $userId, ':did' => $documentId]
        );

        $hourRequests = (int) ($hourCount['cnt'] ?? 0);
        $totalRequests = (int) ($totalCount['cnt'] ?? 0);

        if ($hourRequests >= 30) {
            return [
                'detected'        => true,
                'severity_weight' => 0.7,
                'detail'          => "Excessive usage: {$hourRequests} requests in the last hour",
            ];
        }

        if ($totalRequests >= 100) {
            return [
                'detected'        => true,
                'severity_weight' => 0.5,
                'detail'          => "High total usage: {$totalRequests} total requests for this document",
            ];
        }

        return ['detected' => false, 'severity_weight' => 0.0, 'detail' => null];
    }

    /**
     * Get the strictness level for an assignment.
     */
    public function getStrictnessLevel(int $assignmentId): string
    {
        $rule = fetch(
            "SELECT strictness_level FROM ai_policy_rules
             WHERE (assignment_id = :aid OR
                    class_id = (SELECT class_id FROM assignments WHERE id = :aid2) OR
                    school_id = (SELECT c.school_id FROM assignments a JOIN classes c ON c.id = a.class_id WHERE a.id = :aid3))
               AND is_active = 1
             ORDER BY
                 CASE WHEN assignment_id IS NOT NULL THEN 1
                      WHEN class_id IS NOT NULL THEN 2
                      ELSE 3 END,
                 priority DESC
             LIMIT 1",
            [':aid' => $assignmentId, ':aid2' => $assignmentId, ':aid3' => $assignmentId]
        );

        return $rule ? $rule['strictness_level'] : 'moderate';
    }

    /**
     * Generate an educational refusal message.
     */
    public function generateRefusalMessage(string $violationType, array $context = []): string
    {
        $messages = [
            'direct_answer' => "I understand you want help with your work, but I can't write it for you. The purpose of this assignment is for you to develop your writing skills. Instead, I can help you brainstorm ideas, create an outline, or give feedback on what you've written so far. Which would be most helpful?",
            'laundering'    => "I'm not able to help disguise AI-generated content as your own work. Using AI tools to generate content and then trying to make it appear human-written is a form of academic dishonesty. I'm here to support your learning — let me help you improve your own writing through coaching and feedback instead.",
            'rewrite'       => "I can't rewrite your entire document, as that would mean the work isn't truly yours. Instead, try selecting specific sections you'd like to improve and I can offer targeted suggestions. Or, tell me what aspects of your writing you want to strengthen, and I'll help you learn those skills.",
            'exam_violation' => "AI assistance is not permitted during exams and tests. This policy exists to ensure a fair evaluation of your individual knowledge and skills. If you need help understanding the material, use AI study tools before the exam to prepare.",
            'excessive_use' => "You've been relying heavily on AI assistance for this document. Strong writing develops through practice — try drafting the next section on your own. You can always come back for specific feedback once you have something written. Your own ideas and voice matter!",
            'policy_rule'   => "This type of AI help isn't available for this assignment based on your teacher's policy. Check the assignment guidelines for details on what types of assistance are allowed, or ask your teacher if you have questions.",
        ];

        return $messages[$violationType] ?? "This request cannot be processed due to policy restrictions. Please try a different approach or speak with your teacher for guidance.";
    }

    /**
     * Generate a redirect suggestion for an alternative approach.
     */
    public function generateRedirectSuggestion(string $requestType, array $context = []): string
    {
        $suggestions = [
            'brainstorm'       => "Try freewriting for 5 minutes first, then come back and I can help you develop your best ideas further.",
            'outline'          => "Start by listing your main points, then I can help you organize them into a logical structure.",
            'draft_coach'      => "Instead of having me write content, try starting with a rough draft. I can then coach you through improving it.",
            'revision'         => "Focus on one aspect of your writing at a time — argument, organization, or style. Select a specific paragraph for targeted feedback.",
            'grammar'          => "Grammar checking is available! Paste the text you'd like me to review for mechanical issues.",
            'reflection'       => "Reflection mode is a great choice! Think about what went well and what was challenging in your writing process.",
            'analysis'         => "When analyzing a text, start by identifying the author's main claim, then look for how they support it with evidence.",
            'planning'         => "Let's break your project into manageable steps. When is it due and how much time do you have?",
            'interpret'        => "Share the assignment prompt and I'll help you understand what's being asked.",
            'content_generation' => "I can't generate content for you, but I can help you develop your own ideas. Would you like to try brainstorming or outlining?",
        ];

        return $suggestions[$requestType] ?? "Let me suggest a different approach. Try brainstorming or outlining to develop your own ideas, and I can provide guidance along the way.";
    }

    /**
     * Record a policy violation.
     */
    public function recordViolation(int $userId, string $violationType, string $severity, array $data = []): int
    {
        $validTypes = ['direct_answer', 'plagiarism_attempt', 'rewrite_detection', 'exam_violation', 'laundering', 'excessive_use', 'other'];
        if (!in_array($violationType, $validTypes, true)) {
            $violationType = 'other';
        }

        $validSeverities = ['low', 'medium', 'high', 'critical'];
        if (!in_array($severity, $validSeverities, true)) {
            $severity = 'low';
        }

        $violationId = insert('policy_violations', [
            'user_id'        => $userId,
            'document_id'    => $data['document_id'] ?? null,
            'assignment_id'  => $data['assignment_id'] ?? null,
            'ai_request_id'  => $data['ai_request_id'] ?? null,
            'violation_type' => $violationType,
            'severity'       => $severity,
            'description'    => $data['description'] ?? null,
            'evidence'       => $data['evidence'] ?? null,
            'status'         => 'pending',
        ]);

        logSecurityEvent('policy_violation_recorded', [
            'user_id'        => $userId,
            'violation_id'   => (int) $violationId,
            'violation_type' => $violationType,
            'severity'       => $severity,
        ]);

        return (int) $violationId;
    }

    /**
     * Get paginated violations with filters.
     */
    public function getViolations(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $sql = "SELECT pv.*, u.first_name, u.last_name, u.email,
                       reviewer.first_name AS reviewer_first_name, reviewer.last_name AS reviewer_last_name
                FROM policy_violations pv
                JOIN users u ON u.id = pv.user_id
                LEFT JOIN users reviewer ON reviewer.id = pv.reviewed_by
                WHERE 1=1";
        $params = [];

        if (!empty($filters['user_id'])) {
            $sql .= " AND pv.user_id = :user_id";
            $params[':user_id'] = $filters['user_id'];
        }
        if (!empty($filters['violation_type'])) {
            $sql .= " AND pv.violation_type = :vtype";
            $params[':vtype'] = $filters['violation_type'];
        }
        if (!empty($filters['severity'])) {
            $sql .= " AND pv.severity = :severity";
            $params[':severity'] = $filters['severity'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND pv.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['assignment_id'])) {
            $sql .= " AND pv.assignment_id = :aid";
            $params[':aid'] = $filters['assignment_id'];
        }
        if (!empty($filters['class_id'])) {
            $sql .= " AND pv.assignment_id IN (SELECT id FROM assignments WHERE class_id = :cid)";
            $params[':cid'] = $filters['class_id'];
        }

        $sql .= " ORDER BY pv.created_at DESC";

        return paginate($sql, $params, $page, $perPage);
    }

    /**
     * Get a violation by ID.
     */
    public function getViolationById(int $id): ?array
    {
        return fetch(
            "SELECT pv.*, u.first_name, u.last_name, u.email, u.username,
                    reviewer.first_name AS reviewer_first_name, reviewer.last_name AS reviewer_last_name,
                    a.title AS assignment_title, d.title AS document_title
             FROM policy_violations pv
             JOIN users u ON u.id = pv.user_id
             LEFT JOIN users reviewer ON reviewer.id = pv.reviewed_by
             LEFT JOIN assignments a ON a.id = pv.assignment_id
             LEFT JOIN documents d ON d.id = pv.document_id
             WHERE pv.id = :id",
            [':id' => $id]
        );
    }

    /**
     * Teacher reviews a violation.
     */
    public function reviewViolation(int $id, int $reviewerId, string $status, string $notes = ''): array
    {
        $violation = fetch("SELECT id, status FROM policy_violations WHERE id = :id", [':id' => $id]);
        if (!$violation) {
            throw new RuntimeException('Violation not found.');
        }

        $validStatuses = ['reviewed', 'resolved', 'dismissed'];
        if (!in_array($status, $validStatuses, true)) {
            throw new InvalidArgumentException('Invalid review status.');
        }

        update('policy_violations', [
            'status'           => $status,
            'reviewed_by'      => $reviewerId,
            'reviewed_at'      => date('Y-m-d H:i:s'),
            'resolution_notes' => trim($notes) ?: null,
        ], 'id = :id', [':id' => $id]);

        insert('activity_logs', [
            'user_id'     => $reviewerId,
            'action'      => 'violation_reviewed',
            'entity_type' => 'policy_violation',
            'entity_id'   => $id,
            'details'     => json_encode(['status' => $status]),
            'ip_address'  => getClientIP(),
        ]);

        return $this->getViolationById($id);
    }

    /**
     * Get integrity flags for a document.
     */
    public function getFlags(int $documentId): array
    {
        return fetchAll(
            "SELECT f.*, u.first_name, u.last_name,
                    reviewer.first_name AS reviewer_first_name, reviewer.last_name AS reviewer_last_name
             FROM integrity_flags f
             JOIN users u ON u.id = f.user_id
             LEFT JOIN users reviewer ON reviewer.id = f.reviewed_by
             WHERE f.document_id = :doc_id
             ORDER BY f.created_at DESC",
            [':doc_id' => $documentId]
        );
    }

    /**
     * Create an integrity flag on a document.
     */
    public function createFlag(int $documentId, int $userId, string $flagType, string $severity, array $details = []): int
    {
        $validTypes = ['suspicious_pattern', 'rapid_content', 'external_paste', 'style_mismatch', 'ai_overuse', 'policy_breach'];
        if (!in_array($flagType, $validTypes, true)) {
            throw new InvalidArgumentException('Invalid flag type.');
        }
        $validSeverities = ['info', 'warning', 'serious', 'critical'];
        if (!in_array($severity, $validSeverities, true)) {
            $severity = 'info';
        }

        $flagId = insert('integrity_flags', [
            'document_id' => $documentId,
            'user_id'     => $userId,
            'flag_type'   => $flagType,
            'severity'    => $severity,
            'details'     => !empty($details) ? json_encode($details) : null,
            'is_reviewed' => 0,
        ]);

        return (int) $flagId;
    }

    /**
     * Mark a flag as reviewed.
     */
    public function reviewFlag(int $flagId, int $reviewerId): bool
    {
        $flag = fetch("SELECT id, is_reviewed FROM integrity_flags WHERE id = :id", [':id' => $flagId]);
        if (!$flag) {
            throw new RuntimeException('Flag not found.');
        }

        update('integrity_flags', [
            'is_reviewed' => 1,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => date('Y-m-d H:i:s'),
        ], 'id = :id', [':id' => $flagId]);

        return true;
    }

    /**
     * Get a user's violation history.
     */
    public function getUserViolationHistory(int $userId): array
    {
        $violations = fetchAll(
            "SELECT pv.*, a.title AS assignment_title
             FROM policy_violations pv
             LEFT JOIN assignments a ON a.id = pv.assignment_id
             WHERE pv.user_id = :uid
             ORDER BY pv.created_at DESC",
            [':uid' => $userId]
        );

        $summary = fetch(
            "SELECT COUNT(*) AS total,
                    SUM(CASE WHEN severity = 'critical' THEN 1 ELSE 0 END) AS critical_count,
                    SUM(CASE WHEN severity = 'high' THEN 1 ELSE 0 END) AS high_count,
                    SUM(CASE WHEN severity = 'medium' THEN 1 ELSE 0 END) AS medium_count,
                    SUM(CASE WHEN severity = 'low' THEN 1 ELSE 0 END) AS low_count,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_count
             FROM policy_violations WHERE user_id = :uid",
            [':uid' => $userId]
        );

        return [
            'violations' => $violations,
            'summary'    => [
                'total'    => (int) ($summary['total'] ?? 0),
                'critical' => (int) ($summary['critical_count'] ?? 0),
                'high'     => (int) ($summary['high_count'] ?? 0),
                'medium'   => (int) ($summary['medium_count'] ?? 0),
                'low'      => (int) ($summary['low_count'] ?? 0),
                'pending'  => (int) ($summary['pending_count'] ?? 0),
            ],
        ];
    }

    /**
     * Get class-level violation summary.
     */
    public function getClassViolationSummary(int $classId): array
    {
        return fetch(
            "SELECT COUNT(*) AS total_violations,
                    COUNT(DISTINCT pv.user_id) AS students_with_violations,
                    SUM(CASE WHEN pv.severity = 'critical' THEN 1 ELSE 0 END) AS critical_count,
                    SUM(CASE WHEN pv.severity = 'high' THEN 1 ELSE 0 END) AS high_count,
                    SUM(CASE WHEN pv.status = 'pending' THEN 1 ELSE 0 END) AS pending_review,
                    SUM(CASE WHEN pv.violation_type = 'direct_answer' THEN 1 ELSE 0 END) AS direct_answer_count,
                    SUM(CASE WHEN pv.violation_type = 'laundering' THEN 1 ELSE 0 END) AS laundering_count,
                    SUM(CASE WHEN pv.violation_type = 'excessive_use' THEN 1 ELSE 0 END) AS excessive_use_count
             FROM policy_violations pv
             WHERE pv.assignment_id IN (SELECT id FROM assignments WHERE class_id = :cid)",
            [':cid' => $classId]
        ) ?: [];
    }

    /**
     * Get school-level violation summary.
     */
    public function getSchoolViolationSummary(int $schoolId): array
    {
        return fetch(
            "SELECT COUNT(*) AS total_violations,
                    COUNT(DISTINCT pv.user_id) AS students_with_violations,
                    SUM(CASE WHEN pv.severity = 'critical' THEN 1 ELSE 0 END) AS critical_count,
                    SUM(CASE WHEN pv.severity = 'high' THEN 1 ELSE 0 END) AS high_count,
                    SUM(CASE WHEN pv.status = 'pending' THEN 1 ELSE 0 END) AS pending_review,
                    SUM(CASE WHEN pv.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) AS this_week,
                    SUM(CASE WHEN pv.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) AS this_month
             FROM policy_violations pv
             JOIN users u ON u.id = pv.user_id
             WHERE u.school_id = :sid",
            [':sid' => $schoolId]
        ) ?: [];
    }

    /**
     * Create default policy rules for a school.
     */
    public function createDefaultRules(int $schoolId): array
    {
        $defaultRules = [
            [
                'rule_name'        => 'Allow Brainstorming',
                'rule_type'        => 'allow',
                'category'         => 'brainstorm',
                'strictness_level' => 'lenient',
                'message'          => 'Brainstorming assistance is available.',
                'priority'         => 10,
            ],
            [
                'rule_name'        => 'Allow Grammar Check',
                'rule_type'        => 'allow',
                'category'         => 'grammar',
                'strictness_level' => 'lenient',
                'message'          => 'Grammar checking is available.',
                'priority'         => 10,
            ],
            [
                'rule_name'        => 'Allow Outline Help',
                'rule_type'        => 'allow',
                'category'         => 'outline',
                'strictness_level' => 'lenient',
                'message'          => 'Outline assistance is available.',
                'priority'         => 10,
            ],
            [
                'rule_name'        => 'Allow Reflection',
                'rule_type'        => 'allow',
                'category'         => 'reflection',
                'strictness_level' => 'lenient',
                'message'          => 'Reflection assistance is available.',
                'priority'         => 10,
            ],
            [
                'rule_name'        => 'Redirect Content Generation',
                'rule_type'        => 'redirect',
                'category'         => 'content_generation',
                'strictness_level' => 'moderate',
                'message'          => 'Content generation is redirected to coaching mode. The AI will guide you rather than write for you.',
                'priority'         => 20,
            ],
            [
                'rule_name'        => 'Redirect Rewriting',
                'rule_type'        => 'redirect',
                'category'         => 'rewrite',
                'strictness_level' => 'moderate',
                'message'          => 'Full rewriting is redirected to revision mode. The AI will suggest improvements rather than rewrite your work.',
                'priority'         => 20,
            ],
        ];

        $createdIds = [];
        foreach ($defaultRules as $rule) {
            $rule['school_id'] = $schoolId;
            $rule['is_active'] = 1;
            $createdIds[] = (int) insert('ai_policy_rules', $rule);
        }

        return $createdIds;
    }

    /**
     * Update a policy rule.
     */
    public function updateRule(int $ruleId, array $data): array
    {
        $rule = fetch("SELECT id FROM ai_policy_rules WHERE id = :id", [':id' => $ruleId]);
        if (!$rule) {
            throw new RuntimeException('Policy rule not found.');
        }

        $updateData = [];
        $allowedFields = ['rule_name', 'rule_type', 'category', 'strictness_level', 'message', 'is_active', 'priority'];
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }
        if (array_key_exists('conditions', $data)) {
            $updateData['conditions'] = is_array($data['conditions']) ? json_encode($data['conditions']) : $data['conditions'];
        }

        if (empty($updateData)) {
            return fetch("SELECT * FROM ai_policy_rules WHERE id = :id", [':id' => $ruleId]);
        }

        update('ai_policy_rules', $updateData, 'id = :id', [':id' => $ruleId]);

        return fetch("SELECT * FROM ai_policy_rules WHERE id = :id", [':id' => $ruleId]);
    }

    /**
     * Delete a policy rule.
     */
    public function deleteRule(int $ruleId): bool
    {
        $rule = fetch("SELECT id FROM ai_policy_rules WHERE id = :id", [':id' => $ruleId]);
        if (!$rule) {
            throw new RuntimeException('Policy rule not found.');
        }

        update('assignments', ['ai_policy_id' => null], 'ai_policy_id = :rid', [':rid' => $ruleId]);

        delete('ai_policy_rules', 'id = :id', [':id' => $ruleId]);

        return true;
    }
}
