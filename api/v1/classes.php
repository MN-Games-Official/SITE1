<?php
/**
 * EduWrite AI - Classes API Endpoint
 *
 * Class management, enrollment, student management, and class statistics.
 */

require_once __DIR__ . '/router.php';
require_once BASE_PATH . '/includes/services/ClassService.php';

requireAuth();

$classService = new ClassService();

routeAction([
    'list'           => 'handleList',
    'get'            => 'handleGet',
    'create'         => 'handleCreate',
    'update'         => 'handleUpdate',
    'delete'         => 'handleDelete',
    'join'           => 'handleJoin',
    'leave'          => 'handleLeave',
    'enroll'         => 'handleEnroll',
    'remove-student' => 'handleRemoveStudent',
    'students'       => 'handleStudents',
    'stats'          => 'handleStats',
    'activity'       => 'handleActivity',
    'bulk-enroll'    => 'handleBulkEnroll',
]);

/** GET - List classes (teacher gets their classes, student gets enrolled) */
function handleList(): void {
    requireMethod('GET');

    global $classService;
    $user = getCurrentUser();

    $filters = [];
    if (!empty($_GET['status'])) $filters['status'] = sanitize($_GET['status'], 'string');
    if (!empty($_GET['search'])) $filters['search'] = sanitize($_GET['search'], 'string');

    if ($user['role'] === 'student') {
        $classes = $classService->getByStudent($user['id']);
        jsonSuccess(['classes' => $classes]);
    }

    [$page, $perPage] = getPaginationParams();
    $result = $classService->getByTeacher($user['id'], $filters, $page, $perPage);
    jsonSuccess($result);
}

/** GET - Get class details */
function handleGet(): void {
    requireMethod('GET');

    global $classService;

    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) {
        jsonError('Class ID is required', 400);
    }

    $class = $classService->getById($id);
    if (!$class) {
        jsonError('Class not found', 404);
    }

    jsonSuccess(['class' => $class]);
}

/** POST - Create a new class (teacher only) */
function handleCreate(): void {
    requireMethod('POST');
    validateWriteCSRF();
    requireRole('teacher');

    global $classService;
    $user = getCurrentUser();
    $data = getRequestBody();

    $errors = validate($data, [
        'name'        => 'required|string|max:255',
        'description' => 'string|max:1000',
        'subject'     => 'string|max:100',
        'grade_level' => 'string|max:50',
        'period'      => 'string|max:50',
    ]);

    if (!empty($errors)) {
        jsonError('Validation failed', 422, $errors);
    }

    $class = $classService->create($user['id'], [
        'name'        => sanitize($data['name'], 'string'),
        'description' => sanitize($data['description'] ?? '', 'string'),
        'subject'     => sanitize($data['subject'] ?? '', 'string'),
        'grade_level' => sanitize($data['grade_level'] ?? '', 'string'),
        'period'      => sanitize($data['period'] ?? '', 'string'),
    ]);

    jsonSuccess(['class' => $class], 'Class created');
}

/** PUT - Update class details (teacher only) */
function handleUpdate(): void {
    requireMethod('PUT');
    validateWriteCSRF();
    requireRole('teacher');

    global $classService;
    $user = getCurrentUser();
    $data = getRequestBody();

    $id = (int)($data['id'] ?? $_GET['id'] ?? 0);
    if ($id <= 0) {
        jsonError('Class ID is required', 400);
    }

    $errors = validate($data, [
        'name'        => 'string|max:255',
        'description' => 'string|max:1000',
        'subject'     => 'string|max:100',
        'grade_level' => 'string|max:50',
        'period'      => 'string|max:50',
    ]);

    if (!empty($errors)) {
        jsonError('Validation failed', 422, $errors);
    }

    $updateData = [];
    foreach (['name', 'description', 'subject', 'grade_level', 'period'] as $field) {
        if (isset($data[$field])) {
            $updateData[$field] = sanitize($data[$field], 'string');
        }
    }

    if (empty($updateData)) {
        jsonError('No fields to update', 400);
    }

    $updated = $classService->update($id, $user['id'], $updateData);
    jsonSuccess(['class' => $updated], 'Class updated');
}

/** DELETE - Archive class (teacher only) */
function handleDelete(): void {
    requireMethod('DELETE');
    validateWriteCSRF();
    requireRole('teacher');

    global $classService;
    $user = getCurrentUser();
    $data = getRequestBody();

    $id = (int)($data['id'] ?? $_GET['id'] ?? 0);
    if ($id <= 0) {
        jsonError('Class ID is required', 400);
    }

    $classService->archiveClass($id, $user['id']);
    jsonSuccess(null, 'Class archived');
}

/** POST - Student joins a class via join code */
function handleJoin(): void {
    requireMethod('POST');
    validateWriteCSRF();
    requireRole('student');

    global $classService;
    $user = getCurrentUser();
    $data = getRequestBody();

    $errors = validate($data, [
        'code' => 'required|string|min:4|max:20',
    ]);

    if (!empty($errors)) {
        jsonError('Validation failed', 422, $errors);
    }

    $result = $classService->joinClass($user['id'], sanitize($data['code'], 'string'));
    jsonSuccess($result, 'Joined class successfully');
}

/** POST - Student leaves a class */
function handleLeave(): void {
    requireMethod('POST');
    validateWriteCSRF();
    requireRole('student');

    global $classService;
    $user = getCurrentUser();
    $data = getRequestBody();

    $classId = (int)($data['class_id'] ?? 0);
    if ($classId <= 0) {
        jsonError('Class ID is required', 400);
    }

    $classService->leaveClass($user['id'], $classId);
    jsonSuccess(null, 'Left class successfully');
}

/** POST - Teacher enrolls a student */
function handleEnroll(): void {
    requireMethod('POST');
    validateWriteCSRF();
    requireRole('teacher');

    global $classService;
    $data = getRequestBody();

    $errors = validate($data, [
        'class_id'   => 'required|integer',
        'student_id' => 'required|integer',
    ]);

    if (!empty($errors)) {
        jsonError('Validation failed', 422, $errors);
    }

    $classService->enrollStudent((int)$data['class_id'], (int)$data['student_id']);
    jsonSuccess(null, 'Student enrolled');
}

/** POST - Teacher removes a student from class */
function handleRemoveStudent(): void {
    requireMethod('POST');
    validateWriteCSRF();
    requireRole('teacher');

    global $classService;
    $data = getRequestBody();

    $errors = validate($data, [
        'class_id'   => 'required|integer',
        'student_id' => 'required|integer',
    ]);

    if (!empty($errors)) {
        jsonError('Validation failed', 422, $errors);
    }

    $classService->removeStudent((int)$data['class_id'], (int)$data['student_id']);
    jsonSuccess(null, 'Student removed');
}

/** GET - Get enrolled students with stats */
function handleStudents(): void {
    requireMethod('GET');
    requireRole(['teacher', 'admin']);

    global $classService;

    $classId = (int)($_GET['class_id'] ?? 0);
    if ($classId <= 0) {
        jsonError('Class ID is required', 400);
    }

    [$page, $perPage] = getPaginationParams();
    $result = $classService->getStudents($classId, $page, $perPage);
    jsonSuccess($result);
}

/** GET - Get class statistics */
function handleStats(): void {
    requireMethod('GET');
    requireRole(['teacher', 'admin']);

    global $classService;

    $classId = (int)($_GET['class_id'] ?? 0);
    if ($classId <= 0) {
        jsonError('Class ID is required', 400);
    }

    $stats = $classService->getClassStats($classId);
    jsonSuccess(['stats' => $stats]);
}

/** GET - Get recent class activity */
function handleActivity(): void {
    requireMethod('GET');
    requireRole(['teacher', 'admin']);

    global $classService;

    $classId = (int)($_GET['class_id'] ?? 0);
    if ($classId <= 0) {
        jsonError('Class ID is required', 400);
    }

    $limit = min(50, max(5, (int)($_GET['limit'] ?? 20)));
    $activity = $classService->getClassActivity($classId, $limit);
    jsonSuccess(['activity' => $activity]);
}

/** POST - Bulk enroll students from email list */
function handleBulkEnroll(): void {
    requireMethod('POST');
    validateWriteCSRF();
    requireRole('teacher');

    global $classService;
    $data = getRequestBody();

    $errors = validate($data, [
        'class_id' => 'required|integer',
        'emails'   => 'required|array',
    ]);

    if (!empty($errors)) {
        jsonError('Validation failed', 422, $errors);
    }

    $emails = array_map(function ($email) {
        return sanitize(trim($email), 'email');
    }, $data['emails']);

    $emails = array_filter($emails);

    if (empty($emails)) {
        jsonError('No valid email addresses provided', 400);
    }

    $result = $classService->bulkEnrollStudents((int)$data['class_id'], $emails);
    jsonSuccess($result, 'Bulk enrollment complete');
}
