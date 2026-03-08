<?php
/**
 * EduWrite AI - Assignments API Endpoint
 *
 * Assignment CRUD, publishing, submissions, policies, and student helpers.
 */

require_once __DIR__ . '/router.php';
require_once BASE_PATH . '/includes/services/AssignmentService.php';
require_once BASE_PATH . '/includes/services/PolicyService.php';

requireAuth();

$assignmentService = new AssignmentService();
$policyService = new PolicyService();

routeAction([
    'list'             => 'handleList',
    'get'              => 'handleGet',
    'create'           => 'handleCreate',
    'update'           => 'handleUpdate',
    'delete'           => 'handleDelete',
    'publish'          => 'handlePublish',
    'unpublish'        => 'handleUnpublish',
    'submissions'      => 'handleSubmissions',
    'submission-stats' => 'handleSubmissionStats',
    'set-policy'       => 'handleSetPolicy',
    'policy'           => 'handleGetPolicy',
    'duplicate'        => 'handleDuplicate',
    'upcoming'         => 'handleUpcoming',
    'overdue'          => 'handleOverdue',
]);

/** GET - List assignments by class or for student */
function handleList(): void {
    requireMethod('GET');

    global $assignmentService;
    $user = getCurrentUser();

    $filters = [];
    if (!empty($_GET['status'])) $filters['status'] = sanitize($_GET['status'], 'string');
    if (!empty($_GET['search'])) $filters['search'] = sanitize($_GET['search'], 'string');

    // Students see their assignments across all classes
    if ($user['role'] === 'student') {
        $assignments = $assignmentService->getByStudent($user['id'], $filters);
        jsonSuccess(['assignments' => $assignments]);
    }

    // Teachers filter by class
    $classId = (int)($_GET['class_id'] ?? 0);
    if ($classId <= 0) {
        jsonError('Class ID is required for teacher view', 400);
    }

    [$page, $perPage] = getPaginationParams();
    $result = $assignmentService->getByClass($classId, $filters, $page, $perPage);
    jsonSuccess($result);
}

/** GET - Get assignment details */
function handleGet(): void {
    requireMethod('GET');

    global $assignmentService;

    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) {
        jsonError('Assignment ID is required', 400);
    }

    $assignment = $assignmentService->getById($id);
    if (!$assignment) {
        jsonError('Assignment not found', 404);
    }

    jsonSuccess(['assignment' => $assignment]);
}

/** POST - Create a new assignment (teacher only) */
function handleCreate(): void {
    requireMethod('POST');
    validateWriteCSRF();
    requireRole('teacher');

    global $assignmentService;
    $user = getCurrentUser();
    $data = getRequestBody();

    $errors = validate($data, [
        'class_id'     => 'required|integer',
        'title'        => 'required|string|max:255',
        'description'  => 'string',
        'instructions' => 'string',
        'due_date'     => 'date',
        'max_score'    => 'numeric',
        'type'         => 'string|in:essay,report,creative,research,reflection',
    ]);

    if (!empty($errors)) {
        jsonError('Validation failed', 422, $errors);
    }

    $assignment = $assignmentService->create((int)$data['class_id'], $user['id'], [
        'title'        => sanitize($data['title'], 'string'),
        'description'  => sanitize($data['description'] ?? '', 'string'),
        'instructions' => $data['instructions'] ?? '',
        'due_date'     => $data['due_date'] ?? null,
        'max_score'    => $data['max_score'] ?? 100,
        'type'         => $data['type'] ?? 'essay',
    ]);

    jsonSuccess(['assignment' => $assignment], 'Assignment created');
}

/** PUT - Update an assignment (teacher only) */
function handleUpdate(): void {
    requireMethod('PUT');
    validateWriteCSRF();
    requireRole('teacher');

    global $assignmentService;
    $user = getCurrentUser();
    $data = getRequestBody();

    $id = (int)($data['id'] ?? $_GET['id'] ?? 0);
    if ($id <= 0) {
        jsonError('Assignment ID is required', 400);
    }

    $errors = validate($data, [
        'title'        => 'string|max:255',
        'description'  => 'string',
        'instructions' => 'string',
        'due_date'     => 'date',
        'max_score'    => 'numeric',
        'type'         => 'string|in:essay,report,creative,research,reflection',
    ]);

    if (!empty($errors)) {
        jsonError('Validation failed', 422, $errors);
    }

    $updateData = [];
    foreach (['title', 'description', 'instructions', 'due_date', 'max_score', 'type'] as $field) {
        if (isset($data[$field])) {
            $updateData[$field] = in_array($field, ['title', 'description', 'type'])
                ? sanitize($data[$field], 'string')
                : $data[$field];
        }
    }

    if (empty($updateData)) {
        jsonError('No fields to update', 400);
    }

    $updated = $assignmentService->update($id, $user['id'], $updateData);
    jsonSuccess(['assignment' => $updated], 'Assignment updated');
}

/** DELETE - Archive an assignment (teacher only) */
function handleDelete(): void {
    requireMethod('DELETE');
    validateWriteCSRF();
    requireRole('teacher');

    global $assignmentService;
    $user = getCurrentUser();
    $data = getRequestBody();

    $id = (int)($data['id'] ?? $_GET['id'] ?? 0);
    if ($id <= 0) {
        jsonError('Assignment ID is required', 400);
    }

    $assignmentService->delete($id, $user['id']);
    jsonSuccess(null, 'Assignment archived');
}

/** POST - Publish an assignment */
function handlePublish(): void {
    requireMethod('POST');
    validateWriteCSRF();
    requireRole('teacher');

    global $assignmentService;
    $user = getCurrentUser();
    $data = getRequestBody();

    $id = (int)($data['id'] ?? 0);
    if ($id <= 0) {
        jsonError('Assignment ID is required', 400);
    }

    $result = $assignmentService->publish($id, $user['id']);
    jsonSuccess($result, 'Assignment published');
}

/** POST - Unpublish an assignment */
function handleUnpublish(): void {
    requireMethod('POST');
    validateWriteCSRF();
    requireRole('teacher');

    global $assignmentService;
    $user = getCurrentUser();
    $data = getRequestBody();

    $id = (int)($data['id'] ?? 0);
    if ($id <= 0) {
        jsonError('Assignment ID is required', 400);
    }

    $result = $assignmentService->unpublish($id, $user['id']);
    jsonSuccess($result, 'Assignment unpublished');
}

/** GET - Get submissions for an assignment */
function handleSubmissions(): void {
    requireMethod('GET');
    requireRole(['teacher', 'admin']);

    global $assignmentService;

    $assignmentId = (int)($_GET['assignment_id'] ?? 0);
    if ($assignmentId <= 0) {
        jsonError('Assignment ID is required', 400);
    }

    [$page, $perPage] = getPaginationParams();
    $result = $assignmentService->getSubmissions($assignmentId, $page, $perPage);
    jsonSuccess($result);
}

/** GET - Get submission statistics for an assignment */
function handleSubmissionStats(): void {
    requireMethod('GET');
    requireRole(['teacher', 'admin']);

    global $assignmentService;

    $assignmentId = (int)($_GET['assignment_id'] ?? 0);
    if ($assignmentId <= 0) {
        jsonError('Assignment ID is required', 400);
    }

    $stats = $assignmentService->getSubmissionStats($assignmentId);
    jsonSuccess(['stats' => $stats]);
}

/** POST - Set AI policy for an assignment */
function handleSetPolicy(): void {
    requireMethod('POST');
    validateWriteCSRF();
    requireRole('teacher');

    global $assignmentService;
    $user = getCurrentUser();
    $data = getRequestBody();

    $errors = validate($data, [
        'assignment_id'  => 'required|integer',
        'policy_rule_id' => 'required|integer',
    ]);

    if (!empty($errors)) {
        jsonError('Validation failed', 422, $errors);
    }

    $result = $assignmentService->setAIPolicy(
        (int)$data['assignment_id'],
        $user['id'],
        (int)$data['policy_rule_id']
    );

    jsonSuccess($result, 'AI policy set');
}

/** GET - Get effective AI policy for an assignment */
function handleGetPolicy(): void {
    requireMethod('GET');

    global $assignmentService;

    $assignmentId = (int)($_GET['assignment_id'] ?? 0);
    if ($assignmentId <= 0) {
        jsonError('Assignment ID is required', 400);
    }

    $policy = $assignmentService->getAssignmentPolicy($assignmentId);
    jsonSuccess(['policy' => $policy]);
}

/** POST - Duplicate assignment to another class */
function handleDuplicate(): void {
    requireMethod('POST');
    validateWriteCSRF();
    requireRole('teacher');

    global $assignmentService;
    $user = getCurrentUser();
    $data = getRequestBody();

    $errors = validate($data, [
        'assignment_id'  => 'required|integer',
        'target_class_id' => 'required|integer',
    ]);

    if (!empty($errors)) {
        jsonError('Validation failed', 422, $errors);
    }

    $result = $assignmentService->duplicateAssignment(
        (int)$data['assignment_id'],
        $user['id'],
        (int)$data['target_class_id']
    );

    jsonSuccess(['assignment' => $result], 'Assignment duplicated');
}

/** GET - Get upcoming assignments for student */
function handleUpcoming(): void {
    requireMethod('GET');

    global $assignmentService;
    $user = getCurrentUser();

    $limit = min(50, max(1, (int)($_GET['limit'] ?? 10)));
    $assignments = $assignmentService->getUpcoming($user['id'], $limit);
    jsonSuccess(['assignments' => $assignments]);
}

/** GET - Get overdue assignments for student */
function handleOverdue(): void {
    requireMethod('GET');

    global $assignmentService;
    $user = getCurrentUser();

    $assignments = $assignmentService->getOverdue($user['id']);
    jsonSuccess(['assignments' => $assignments]);
}
