<?php
/**
 * EduWrite AI - Import/Export API Endpoint
 *
 * CSV student imports, document/roster/grade/analytics exports, and job management.
 */

require_once __DIR__ . '/router.php';
require_once BASE_PATH . '/includes/services/ImportExportService.php';

requireAuth();

$importExportService = new ImportExportService();

routeAction([
    'import-students'  => 'handleImportStudents',
    'export-document'  => 'handleExportDocument',
    'export-roster'    => 'handleExportRoster',
    'export-grades'    => 'handleExportGrades',
    'export-analytics' => 'handleExportAnalytics',
    'export-audit-log' => 'handleExportAuditLog',
    'export-jobs'      => 'handleExportJobs',
    'download'         => 'handleDownload',
]);

/** POST - Import students from CSV file */
function handleImportStudents(): void {
    requireMethod('POST');
    validateWriteCSRF();
    requireRole('teacher');

    global $importExportService;

    if (empty($_FILES['file'])) {
        jsonError('CSV file is required', 400);
    }

    $errors = validate($_POST, [
        'class_id' => 'required|integer',
    ]);

    if (!empty($errors)) {
        jsonError('Validation failed', 422, $errors);
    }

    $fileValidation = validateFileUpload($_FILES['file'], [
        'max_size'           => 5 * 1024 * 1024,
        'allowed_extensions' => ['csv'],
        'allowed_types'      => ['text/csv', 'text/plain', 'application/csv'],
    ]);

    if (!$fileValidation['valid']) {
        jsonError('Invalid file', 422, $fileValidation['errors']);
    }

    $result = $importExportService->importStudents((int)$_POST['class_id'], $_FILES['file']);
    jsonSuccess($result, 'Import complete');
}

/** GET - Export a document in the specified format */
function handleExportDocument(): void {
    requireMethod('GET');

    global $importExportService;

    $id = (int)($_GET['document_id'] ?? 0);
    if ($id <= 0) jsonError('Document ID is required', 400);

    $format = sanitize($_GET['format'] ?? 'html', 'string');
    if (!in_array($format, ['html', 'pdf', 'docx'])) {
        jsonError('Invalid format. Supported: html, pdf, docx', 400);
    }

    $result = $importExportService->exportDocument($id, $format);
    jsonSuccess($result);
}

/** GET - Export class roster */
function handleExportRoster(): void {
    requireMethod('GET');
    requireRole(['teacher', 'admin']);

    global $importExportService;

    $classId = (int)($_GET['class_id'] ?? 0);
    if ($classId <= 0) jsonError('Class ID is required', 400);

    $format = sanitize($_GET['format'] ?? 'csv', 'string');
    $result = $importExportService->exportClassRoster($classId, $format);
    jsonSuccess($result);
}

/** GET - Export grades for an assignment */
function handleExportGrades(): void {
    requireMethod('GET');
    requireRole(['teacher', 'admin']);

    global $importExportService;

    $assignmentId = (int)($_GET['assignment_id'] ?? 0);
    if ($assignmentId <= 0) jsonError('Assignment ID is required', 400);

    $format = sanitize($_GET['format'] ?? 'csv', 'string');
    $result = $importExportService->exportGrades($assignmentId, $format);
    jsonSuccess($result);
}

/** GET - Export analytics report */
function handleExportAnalytics(): void {
    requireMethod('GET');
    requireRole(['teacher', 'admin']);

    global $importExportService;

    $params = [];
    if (!empty($_GET['class_id']))  $params['class_id']  = (int)$_GET['class_id'];
    if (!empty($_GET['school_id'])) $params['school_id'] = (int)$_GET['school_id'];
    $params['period'] = getPeriodParam();

    $format = sanitize($_GET['format'] ?? 'csv', 'string');
    $result = $importExportService->exportAnalyticsReport($params, $format);
    jsonSuccess($result);
}

/** GET - Export audit log */
function handleExportAuditLog(): void {
    requireMethod('GET');
    requireRole('admin');

    global $importExportService;

    $filters = [];
    if (!empty($_GET['user_id'])) $filters['user_id'] = (int)$_GET['user_id'];
    if (!empty($_GET['action']))  $filters['action']  = sanitize($_GET['action'], 'string');
    if (!empty($_GET['from']))    $filters['from']    = sanitize($_GET['from'], 'string');
    if (!empty($_GET['to']))      $filters['to']      = sanitize($_GET['to'], 'string');

    $user = getCurrentUser();
    $filters['school_id'] = $user['school_id'];

    $format = sanitize($_GET['format'] ?? 'csv', 'string');
    $result = $importExportService->exportAuditLog($filters, $format);
    jsonSuccess($result);
}

/** GET - List export jobs for the current user */
function handleExportJobs(): void {
    requireMethod('GET');

    global $importExportService;
    $user = getCurrentUser();

    $jobs = $importExportService->getExportJobs($user['id']);
    jsonSuccess(['jobs' => $jobs]);
}

/** GET - Download a completed export */
function handleDownload(): void {
    requireMethod('GET');

    global $importExportService;
    $user = getCurrentUser();

    $jobId = (int)($_GET['job_id'] ?? 0);
    if ($jobId <= 0) jsonError('Job ID is required', 400);

    $result = $importExportService->downloadExport($jobId, $user['id']);
    jsonSuccess($result);
}
