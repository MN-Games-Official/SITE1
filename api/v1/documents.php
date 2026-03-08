<?php
/**
 * EduWrite AI - Documents API Endpoint
 *
 * CRUD operations, versioning, sharing, search, and export for documents.
 */

require_once __DIR__ . '/router.php';
require_once BASE_PATH . '/includes/services/DocumentService.php';

requireAuth();

$documentService = new DocumentService();

routeAction([
    'list'            => 'handleList',
    'get'             => 'handleGet',
    'create'          => 'handleCreate',
    'update'          => 'handleUpdate',
    'autosave'        => 'handleAutosave',
    'delete'          => 'handleDelete',
    'submit'          => 'handleSubmit',
    'versions'        => 'handleVersions',
    'version'         => 'handleGetVersion',
    'restore-version' => 'handleRestoreVersion',
    'share'           => 'handleShare',
    'revoke-share'    => 'handleRevokeShare',
    'shared'          => 'handleSharedDocuments',
    'stats'           => 'handleStats',
    'search'          => 'handleSearch',
    'export'          => 'handleExport',
]);

/** GET - List user's documents with pagination and filters */
function handleList(): void {
    requireMethod('GET');

    global $documentService;
    $user = getCurrentUser();
    [$page, $perPage] = getPaginationParams();

    $filters = [];
    if (!empty($_GET['status']))        $filters['status'] = sanitize($_GET['status'], 'string');
    if (!empty($_GET['assignment_id'])) $filters['assignment_id'] = (int)$_GET['assignment_id'];
    if (!empty($_GET['search']))        $filters['search'] = sanitize($_GET['search'], 'string');

    $result = $documentService->getByUser($user['id'], $filters, $page, $perPage);
    jsonSuccess($result);
}

/** GET - Get a single document by ID with ownership check */
function handleGet(): void {
    requireMethod('GET');

    global $documentService;
    $user = getCurrentUser();
    $id = (int)($_GET['id'] ?? 0);

    if ($id <= 0) {
        jsonError('Document ID is required', 400);
    }

    $document = $documentService->getById($id);

    if (!$document) {
        jsonError('Document not found', 404);
    }

    // Check ownership or shared access
    if ($document['user_id'] !== $user['id'] && !in_array($user['role'], ['teacher', 'admin'])) {
        jsonError('Unauthorized', 403);
    }

    jsonSuccess(['document' => $document]);
}

/** POST - Create a new document */
function handleCreate(): void {
    requireMethod('POST');
    validateWriteCSRF();

    global $documentService;
    $user = getCurrentUser();
    $data = getRequestBody();

    $errors = validate($data, [
        'title'         => 'required|string|max:255',
        'content'       => 'string',
        'assignment_id' => 'integer',
    ]);

    if (!empty($errors)) {
        jsonError('Validation failed', 422, $errors);
    }

    $document = $documentService->create($user['id'], [
        'title'         => sanitize($data['title'], 'string'),
        'content'       => $data['content'] ?? '',
        'assignment_id' => $data['assignment_id'] ?? null,
    ]);

    jsonSuccess(['document' => $document], 'Document created');
}

/** PUT - Update document content or title */
function handleUpdate(): void {
    requireMethod('PUT');
    validateWriteCSRF();

    global $documentService;
    $user = getCurrentUser();
    $data = getRequestBody();

    $id = (int)($data['id'] ?? $_GET['id'] ?? 0);
    if ($id <= 0) {
        jsonError('Document ID is required', 400);
    }

    $document = $documentService->getById($id);
    if (!$document) {
        jsonError('Document not found', 404);
    }
    if ($document['user_id'] !== $user['id']) {
        jsonError('Unauthorized', 403);
    }

    $errors = validate($data, [
        'title'   => 'string|max:255',
        'content' => 'string',
    ]);

    if (!empty($errors)) {
        jsonError('Validation failed', 422, $errors);
    }

    $updateData = [];
    if (isset($data['title']))   $updateData['title']   = sanitize($data['title'], 'string');
    if (isset($data['content'])) $updateData['content'] = $data['content'];

    if (empty($updateData)) {
        jsonError('No fields to update', 400);
    }

    $updated = $documentService->update($id, $updateData);
    jsonSuccess(['document' => $updated], 'Document updated');
}

/** POST - Autosave document content */
function handleAutosave(): void {
    requireMethod('POST');
    validateWriteCSRF();

    global $documentService;
    $user = getCurrentUser();
    $data = getRequestBody();

    $id = (int)($data['id'] ?? 0);
    if ($id <= 0) {
        jsonError('Document ID is required', 400);
    }

    if (!isset($data['content'])) {
        jsonError('Content is required', 400);
    }

    $result = $documentService->autosave($id, $user['id'], $data['content']);
    jsonSuccess($result);
}

/** DELETE - Delete a document */
function handleDelete(): void {
    requireMethod('DELETE');
    validateWriteCSRF();

    global $documentService;
    $user = getCurrentUser();
    $data = getRequestBody();

    $id = (int)($data['id'] ?? $_GET['id'] ?? 0);
    if ($id <= 0) {
        jsonError('Document ID is required', 400);
    }

    $documentService->delete($id, $user['id']);
    jsonSuccess(null, 'Document deleted');
}

/** POST - Submit document for an assignment */
function handleSubmit(): void {
    requireMethod('POST');
    validateWriteCSRF();

    global $documentService;
    $user = getCurrentUser();
    $data = getRequestBody();

    $id = (int)($data['id'] ?? 0);
    if ($id <= 0) {
        jsonError('Document ID is required', 400);
    }

    $result = $documentService->submit($id, $user['id']);
    jsonSuccess($result, 'Document submitted');
}

/** GET - Get version history for a document */
function handleVersions(): void {
    requireMethod('GET');

    global $documentService;
    $user = getCurrentUser();

    $documentId = (int)($_GET['document_id'] ?? 0);
    if ($documentId <= 0) {
        jsonError('Document ID is required', 400);
    }

    $document = $documentService->getById($documentId);
    if (!$document) {
        jsonError('Document not found', 404);
    }
    if ($document['user_id'] !== $user['id'] && !in_array($user['role'], ['teacher', 'admin'])) {
        jsonError('Unauthorized', 403);
    }

    [$page, $perPage] = getPaginationParams();
    $versions = $documentService->getVersions($documentId, $page, $perPage);
    jsonSuccess($versions);
}

/** GET - Get a specific version */
function handleGetVersion(): void {
    requireMethod('GET');

    global $documentService;

    $versionId = (int)($_GET['version_id'] ?? 0);
    if ($versionId <= 0) {
        jsonError('Version ID is required', 400);
    }

    $version = $documentService->getVersion($versionId);
    if (!$version) {
        jsonError('Version not found', 404);
    }

    jsonSuccess(['version' => $version]);
}

/** POST - Restore document to a specific version */
function handleRestoreVersion(): void {
    requireMethod('POST');
    validateWriteCSRF();

    global $documentService;
    $user = getCurrentUser();
    $data = getRequestBody();

    $documentId = (int)($data['document_id'] ?? 0);
    $versionId  = (int)($data['version_id'] ?? 0);

    if ($documentId <= 0 || $versionId <= 0) {
        jsonError('Document ID and Version ID are required', 400);
    }

    $result = $documentService->restoreVersion($documentId, $versionId, $user['id']);
    jsonSuccess($result, 'Version restored');
}

/** POST - Share document with another user */
function handleShare(): void {
    requireMethod('POST');
    validateWriteCSRF();

    global $documentService;
    $user = getCurrentUser();
    $data = getRequestBody();

    $errors = validate($data, [
        'document_id'      => 'required|integer',
        'shared_with_user_id' => 'required|integer',
        'permission'       => 'required|in:view,comment,edit',
    ]);

    if (!empty($errors)) {
        jsonError('Validation failed', 422, $errors);
    }

    $result = $documentService->shareDocument(
        (int)$data['document_id'],
        $user['id'],
        (int)$data['shared_with_user_id'],
        $data['permission']
    );

    jsonSuccess($result, 'Document shared');
}

/** DELETE - Revoke document share */
function handleRevokeShare(): void {
    requireMethod('DELETE');
    validateWriteCSRF();

    global $documentService;
    $user = getCurrentUser();
    $data = getRequestBody();

    $shareId = (int)($data['share_id'] ?? $_GET['share_id'] ?? 0);
    if ($shareId <= 0) {
        jsonError('Share ID is required', 400);
    }

    $documentService->revokeShare($shareId, $user['id']);
    jsonSuccess(null, 'Share revoked');
}

/** GET - Get documents shared with current user */
function handleSharedDocuments(): void {
    requireMethod('GET');

    global $documentService;
    $user = getCurrentUser();

    $documents = $documentService->getSharedDocuments($user['id']);
    jsonSuccess(['documents' => $documents]);
}

/** GET - Get writing statistics for the current user */
function handleStats(): void {
    requireMethod('GET');

    global $documentService;
    $user = getCurrentUser();

    $stats = $documentService->getDocumentStats($user['id']);
    jsonSuccess(['stats' => $stats]);
}

/** GET - Search documents */
function handleSearch(): void {
    requireMethod('GET');

    global $documentService;
    $user = getCurrentUser();

    $query = sanitize($_GET['q'] ?? '', 'string');
    if (empty($query)) {
        jsonError('Search query is required', 400);
    }

    $filters = [];
    if (!empty($_GET['status']))        $filters['status'] = sanitize($_GET['status'], 'string');
    if (!empty($_GET['assignment_id'])) $filters['assignment_id'] = (int)$_GET['assignment_id'];

    $results = $documentService->searchDocuments($user['id'], $query, $filters);
    jsonSuccess(['documents' => $results]);
}

/** GET - Export document in specified format */
function handleExport(): void {
    requireMethod('GET');

    global $documentService;
    $user = getCurrentUser();

    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) {
        jsonError('Document ID is required', 400);
    }

    $format = sanitize($_GET['format'] ?? 'html', 'string');
    if (!in_array($format, ['html', 'pdf', 'docx'])) {
        jsonError('Invalid format. Supported: html, pdf, docx', 400);
    }

    $document = $documentService->getById($id);
    if (!$document) {
        jsonError('Document not found', 404);
    }
    if ($document['user_id'] !== $user['id'] && !in_array($user['role'], ['teacher', 'admin'])) {
        jsonError('Unauthorized', 403);
    }

    $result = $documentService->exportDocument($id, $format);
    jsonSuccess($result);
}
