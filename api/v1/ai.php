<?php
/**
 * EduWrite AI - AI Interaction API Endpoint
 *
 * AI request processing, session management, usage tracking, and mode listing.
 */

require_once __DIR__ . '/router.php';
require_once BASE_PATH . '/includes/services/AIService.php';
require_once BASE_PATH . '/includes/services/PolicyService.php';

requireAuth();

$aiService = new AIService();
$policyService = new PolicyService();

routeAction([
    'request'         => 'handleRequest',
    'create-session'  => 'handleCreateSession',
    'end-session'     => 'handleEndSession',
    'history'         => 'handleHistory',
    'usage'           => 'handleUsage',
    'modes'           => 'handleModes',
    'session-history' => 'handleSessionHistory',
]);

/**
 * POST - Main AI request endpoint.
 *
 * Validates input, enforces rate limits and policies, processes the AI
 * request through the service layer, and returns the response or refusal.
 */
function handleRequest(): void {
    requireMethod('POST');
    validateWriteCSRF();

    global $aiService, $policyService;
    $user = getCurrentUser();
    $data = getRequestBody();

    // Validate required fields
    $errors = validate($data, [
        'prompt'        => 'required|string|min:1|max:5000',
        'request_type'  => 'required|string|in:brainstorm,outline,draft_coach,revision,grammar,reflection,analysis,planning,interpret,redirect',
        'document_id'   => 'integer',
        'assignment_id' => 'integer',
        'session_id'    => 'integer',
        'selected_text' => 'string|max:10000',
    ]);

    if (!empty($errors)) {
        jsonError('Validation failed', 422, $errors);
    }

    // Rate limiting
    if (!rateLimiter('ai_request_' . $user['id'], AI_RATE_LIMIT_PER_MINUTE ?? 10, 1)) {
        logSecurityEvent('ai_rate_limit_exceeded', ['user_id' => $user['id']]);
        jsonError('Rate limit exceeded. Please wait before making another request.', 429);
    }

    // Build context for the AI service
    $context = [
        'document_id'   => $data['document_id'] ?? null,
        'assignment_id' => $data['assignment_id'] ?? null,
        'session_id'    => $data['session_id'] ?? null,
        'selected_text' => $data['selected_text'] ?? null,
    ];

    // Check policy before processing
    $policyCheck = $policyService->checkRequest(
        $user['id'],
        $context['document_id'],
        $context['assignment_id'],
        $data['request_type'],
        $data['prompt']
    );

    if ($policyCheck['result'] === 'denied') {
        logSecurityEvent('ai_request_denied', [
            'user_id' => $user['id'],
            'reason'  => $policyCheck['reason'],
        ]);

        jsonSuccess([
            'status'     => 'refused',
            'reason'     => $policyCheck['reason'],
            'risk_score' => $policyCheck['risk_score'] ?? null,
        ]);
        return;
    }

    // Process the AI request
    $result = $aiService->sendRequest(
        $user['id'],
        $data['request_type'],
        $data['prompt'],
        $context
    );

    // Return based on result status
    if ($result['status'] === 'failed') {
        jsonError('AI request failed. Please try again.', 500, [
            'request_id' => $result['request_id'] ?? null,
        ]);
    }

    jsonSuccess([
        'request_id'     => $result['request_id'] ?? null,
        'status'         => $result['status'],
        'response'       => $result['response'] ?? null,
        'response_type'  => $result['response_type'] ?? null,
        'suggestion'     => $result['suggestion'] ?? null,
        'suggested_mode' => $result['suggested_mode'] ?? null,
    ]);
}

/** POST - Create a new AI interaction session */
function handleCreateSession(): void {
    requireMethod('POST');
    validateWriteCSRF();

    global $aiService;
    $user = getCurrentUser();
    $data = getRequestBody();

    $errors = validate($data, [
        'document_id' => 'integer',
        'mode'        => 'required|string|in:brainstorm,outline,draft_coach,revision,grammar,reflection,analysis,planning,interpret',
    ]);

    if (!empty($errors)) {
        jsonError('Validation failed', 422, $errors);
    }

    $session = $aiService->createSession(
        $user['id'],
        $data['document_id'] ?? null,
        $data['mode']
    );

    jsonSuccess(['session' => $session], 'AI session created');
}

/** POST - End an AI interaction session */
function handleEndSession(): void {
    requireMethod('POST');
    validateWriteCSRF();

    global $aiService;
    $data = getRequestBody();

    $sessionId = (int)($data['session_id'] ?? 0);
    if ($sessionId <= 0) {
        jsonError('Session ID is required', 400);
    }

    $aiService->endSession($sessionId);
    jsonSuccess(null, 'AI session ended');
}

/** GET - Get AI session history for the user */
function handleHistory(): void {
    requireMethod('GET');

    $user = getCurrentUser();
    [$page, $perPage] = getPaginationParams();

    $result = paginate(
        "SELECT s.*, 
                (SELECT COUNT(*) FROM ai_requests WHERE session_id = s.id) as request_count
         FROM ai_sessions s 
         WHERE s.user_id = ? 
         ORDER BY s.created_at DESC",
        [$user['id']],
        $page,
        $perPage
    );

    jsonSuccess($result);
}

/** GET - Get AI usage statistics for the current user */
function handleUsage(): void {
    requireMethod('GET');

    global $aiService;
    $user = getCurrentUser();

    $period = getPeriodParam('30d');
    $stats = $aiService->getUsageStats($user['id'], $period);
    jsonSuccess(['usage' => $stats]);
}

/** GET - List available AI modes with descriptions */
function handleModes(): void {
    requireMethod('GET');

    $modes = [
        [
            'id'          => 'brainstorm',
            'name'        => 'Brainstorm',
            'description' => 'Generate and explore ideas for your writing topic.',
            'icon'        => 'lightbulb',
        ],
        [
            'id'          => 'outline',
            'name'        => 'Outline',
            'description' => 'Create a structured outline for your paper.',
            'icon'        => 'list',
        ],
        [
            'id'          => 'draft_coach',
            'name'        => 'Draft Coach',
            'description' => 'Get guided assistance while writing your draft.',
            'icon'        => 'edit',
        ],
        [
            'id'          => 'revision',
            'name'        => 'Revision',
            'description' => 'Get suggestions to improve your writing.',
            'icon'        => 'refresh',
        ],
        [
            'id'          => 'grammar',
            'name'        => 'Grammar Check',
            'description' => 'Check grammar, spelling, and punctuation.',
            'icon'        => 'check-circle',
        ],
        [
            'id'          => 'reflection',
            'name'        => 'Reflection',
            'description' => 'Reflect on your writing process and choices.',
            'icon'        => 'thought-bubble',
        ],
        [
            'id'          => 'analysis',
            'name'        => 'Analysis',
            'description' => 'Analyze text structure, arguments, and rhetoric.',
            'icon'        => 'search',
        ],
        [
            'id'          => 'planning',
            'name'        => 'Planning',
            'description' => 'Plan your writing project with goals and timeline.',
            'icon'        => 'calendar',
        ],
        [
            'id'          => 'interpret',
            'name'        => 'Interpret',
            'description' => 'Help interpret source texts and prompts.',
            'icon'        => 'book-open',
        ],
    ];

    jsonSuccess(['modes' => $modes]);
}

/** GET - Get requests within a specific AI session */
function handleSessionHistory(): void {
    requireMethod('GET');

    global $aiService;
    $user = getCurrentUser();

    $sessionId = (int)($_GET['session_id'] ?? 0);
    if ($sessionId <= 0) {
        jsonError('Session ID is required', 400);
    }

    $history = $aiService->getSessionHistory($user['id'], $sessionId);
    jsonSuccess(['requests' => $history]);
}
