<?php
/**
 * EduWrite AI - Teacher API Endpoint
 *
 * Teacher dashboard, student progress, document review, grading, and violations.
 */

require_once __DIR__ . '/router.php';
require_once BASE_PATH . '/includes/services/DocumentService.php';
require_once BASE_PATH . '/includes/services/ClassService.php';
require_once BASE_PATH . '/includes/services/AssignmentService.php';
require_once BASE_PATH . '/includes/services/AnalyticsService.php';
require_once BASE_PATH . '/includes/services/PolicyService.php';

requireAuth();
requireRole('teacher');

$documentService = new DocumentService();
$classService = new ClassService();
$assignmentService = new AssignmentService();
$analyticsService = new AnalyticsService();
$policyService = new PolicyService();

routeAction([
    'dashboard-stats'    => 'handleDashboardStats',
    'class-summary'      => 'handleClassSummary',
    'student-progress'   => 'handleStudentProgress',
    'student-activity'   => 'handleStudentActivity',
    'student-ai-usage'   => 'handleStudentAIUsage',
    'comment'            => 'handleAddComment',
    'update-comment'     => 'handleUpdateComment',
    'delete-comment'     => 'handleDeleteComment',
    'return-document'    => 'handleReturnDocument',
    'grade-document'     => 'handleGradeDocument',
    'document-review'    => 'handleDocumentReview',
    'flags'              => 'handleFlags',
    'violations'         => 'handleViolations',
    'review-violation'   => 'handleReviewViolation',
    'assignment-overview'=> 'handleAssignmentOverview',
]);

function handleDashboardStats(): void {
    requireMethod('GET');
    global $analyticsService;
    $user = getCurrentUser();
    $stats = $analyticsService->getTeacherDashboardStats($user['id']);
    jsonSuccess(['stats' => $stats]);
}

function handleClassSummary(): void {
    requireMethod('GET');
    global $classService;
    $user = getCurrentUser();
    [$page, $perPage] = getPaginationParams();
    $filters = [];
    if (!empty($_GET['status'])) $filters['status'] = sanitize($_GET['status'], 'string');
    $result = $classService->getByTeacher($user['id'], $filters, $page, $perPage);
    jsonSuccess($result);
}

function handleStudentProgress(): void {
    requireMethod('GET');
    global $classService;
    $classId = (int)($_GET['class_id'] ?? 0);
    $studentId = (int)($_GET['student_id'] ?? 0);
    if ($classId <= 0 || $studentId <= 0) jsonError('Class ID and Student ID are required', 400);
    $progress = $classService->getStudentProgress($classId, $studentId);
    jsonSuccess(['progress' => $progress]);
}

function handleStudentActivity(): void {
    requireMethod('GET');
    global $analyticsService;
    $studentId = (int)($_GET['student_id'] ?? 0);
    if ($studentId <= 0) jsonError('Student ID is required', 400);
    $limit = min(100, max(5, (int)($_GET['limit'] ?? 20)));
    $timeline = $analyticsService->getActivityTimeline($studentId, $limit);
    jsonSuccess(['activity' => $timeline]);
}

function handleStudentAIUsage(): void {
    requireMethod('GET');
    global $analyticsService;
    $studentId = (int)($_GET['student_id'] ?? 0);
    if ($studentId <= 0) jsonError('Student ID is required', 400);
    $period = getPeriodParam();
    $usage = $analyticsService->getStudentAIUsageStats($studentId, $period);
    jsonSuccess(['usage' => $usage]);
}

function handleAddComment(): void {
    requireMethod('POST');
    validateWriteCSRF();
    $user = getCurrentUser();
    $data = getRequestBody();
    $errors = validate($data, ['document_id'=>'required|integer','content'=>'required|string|min:1|max:5000','selection_start'=>'integer','selection_end'=>'integer']);
    if (!empty($errors)) jsonError('Validation failed', 422, $errors);
    $id = insert('document_comments', [
        'document_id'     => (int)$data['document_id'],
        'user_id'         => $user['id'],
        'content'         => sanitize($data['content'], 'string'),
        'selection_start' => $data['selection_start'] ?? null,
        'selection_end'   => $data['selection_end'] ?? null,
        'created_at'      => date('Y-m-d H:i:s'),
    ]);
    jsonSuccess(['comment_id' => $id], 'Comment added');
}

function handleUpdateComment(): void {
    requireMethod('PUT');
    validateWriteCSRF();
    $user = getCurrentUser();
    $data = getRequestBody();
    $id = (int)($data['id'] ?? $_GET['id'] ?? 0);
    if ($id <= 0) jsonError('Comment ID is required', 400);
    $errors = validate($data, ['content'=>'required|string|min:1|max:5000']);
    if (!empty($errors)) jsonError('Validation failed', 422, $errors);
    $comment = fetch("SELECT id, user_id FROM document_comments WHERE id = ?", [$id]);
    if (!$comment) jsonError('Comment not found', 404);
    if ($comment['user_id'] !== $user['id']) jsonError('Unauthorized', 403);
    update('document_comments', ['content' => sanitize($data['content'], 'string'), 'updated_at' => date('Y-m-d H:i:s')], 'id = ?', [$id]);
    jsonSuccess(null, 'Comment updated');
}

function handleDeleteComment(): void {
    requireMethod('DELETE');
    validateWriteCSRF();
    $user = getCurrentUser();
    $data = getRequestBody();
    $id = (int)($data['id'] ?? $_GET['id'] ?? 0);
    if ($id <= 0) jsonError('Comment ID is required', 400);
    $comment = fetch("SELECT id, user_id FROM document_comments WHERE id = ?", [$id]);
    if (!$comment) jsonError('Comment not found', 404);
    if ($comment['user_id'] !== $user['id']) jsonError('Unauthorized', 403);
    delete('document_comments', 'id = ?', [$id]);
    jsonSuccess(null, 'Comment deleted');
}

function handleReturnDocument(): void {
    requireMethod('POST');
    validateWriteCSRF();
    global $documentService;
    $user = getCurrentUser();
    $data = getRequestBody();
    $errors = validate($data, ['document_id'=>'required|integer','notes'=>'string']);
    if (!empty($errors)) jsonError('Validation failed', 422, $errors);
    $result = $documentService->returnDocument((int)$data['document_id'], $user['id'], $data['notes'] ?? '');
    jsonSuccess($result, 'Document returned to student');
}

function handleGradeDocument(): void {
    requireMethod('POST');
    validateWriteCSRF();
    global $documentService;
    $user = getCurrentUser();
    $data = getRequestBody();
    $errors = validate($data, ['document_id'=>'required|integer','score'=>'required|numeric','rubric_feedback'=>'string','general_feedback'=>'string']);
    if (!empty($errors)) jsonError('Validation failed', 422, $errors);
    $result = $documentService->gradeDocument((int)$data['document_id'], $user['id'], [
        'score'            => (float)$data['score'],
        'rubric_feedback'  => $data['rubric_feedback'] ?? '',
        'general_feedback' => $data['general_feedback'] ?? '',
    ]);
    jsonSuccess($result, 'Document graded');
}

function handleDocumentReview(): void {
    requireMethod('GET');
    global $documentService;
    $id = (int)($_GET['document_id'] ?? 0);
    if ($id <= 0) jsonError('Document ID is required', 400);
    $document = $documentService->getById($id);
    if (!$document) jsonError('Document not found', 404);
    jsonSuccess(['document' => $document]);
}

function handleFlags(): void {
    requireMethod('GET');
    global $policyService;
    $user = getCurrentUser();
    [$page, $perPage] = getPaginationParams();
    // Get flags for documents in teacher's classes
    $result = paginate(
        "SELECT f.*, d.title as document_title, u.first_name, u.last_name
         FROM integrity_flags f
         JOIN documents d ON f.document_id = d.id
         JOIN users u ON f.user_id = u.id
         JOIN class_enrollments ce ON ce.student_id = u.id
         JOIN classes c ON c.id = ce.class_id AND c.teacher_id = ?
         ORDER BY f.created_at DESC",
        [$user['id']], $page, $perPage
    );
    jsonSuccess($result);
}

function handleViolations(): void {
    requireMethod('GET');
    global $policyService;
    $user = getCurrentUser();
    [$page, $perPage] = getPaginationParams();
    $filters = ['teacher_id' => $user['id']];
    if (!empty($_GET['severity'])) $filters['severity'] = sanitize($_GET['severity'], 'string');
    $result = $policyService->getViolations($filters, $page, $perPage);
    jsonSuccess($result);
}

function handleReviewViolation(): void {
    requireMethod('POST');
    validateWriteCSRF();
    global $policyService;
    $user = getCurrentUser();
    $data = getRequestBody();
    $errors = validate($data, ['violation_id'=>'required|integer','status'=>'required|in:reviewed,dismissed,escalated','notes'=>'string']);
    if (!empty($errors)) jsonError('Validation failed', 422, $errors);
    $result = $policyService->reviewViolation((int)$data['violation_id'], $user['id'], $data['status'], $data['notes'] ?? '');
    jsonSuccess($result, 'Violation reviewed');
}

function handleAssignmentOverview(): void {
    requireMethod('GET');
    global $assignmentService, $analyticsService;
    $assignmentId = (int)($_GET['assignment_id'] ?? 0);
    if ($assignmentId <= 0) jsonError('Assignment ID is required', 400);
    $assignment = $assignmentService->getById($assignmentId);
    if (!$assignment) jsonError('Assignment not found', 404);
    $stats = $assignmentService->getSubmissionStats($assignmentId);
    $analytics = $analyticsService->getAssignmentAnalytics($assignmentId);
    jsonSuccess(['assignment' => $assignment, 'submission_stats' => $stats, 'analytics' => $analytics]);
}
