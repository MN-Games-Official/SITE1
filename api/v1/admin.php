<?php
/**
 * EduWrite AI - Admin API Endpoint
 *
 * User management, school settings, policy rules, violations, flags, and audit logs.
 */

require_once __DIR__ . '/router.php';
require_once BASE_PATH . '/includes/services/PolicyService.php';
require_once BASE_PATH . '/includes/services/AnalyticsService.php';
require_once BASE_PATH . '/includes/services/ImportExportService.php';

requireAuth();
requireRole('admin');

$policyService = new PolicyService();
$analyticsService = new AnalyticsService();

routeAction([
    'users'              => 'handleUsers',
    'user'               => 'handleUser',
    'create-user'        => 'handleCreateUser',
    'update-user'        => 'handleUpdateUser',
    'deactivate-user'    => 'handleDeactivateUser',
    'activate-user'      => 'handleActivateUser',
    'update-role'        => 'handleUpdateRole',
    'school'             => 'handleSchool',
    'update-school'      => 'handleUpdateSchool',
    'policies'           => 'handlePolicies',
    'create-policy'      => 'handleCreatePolicy',
    'update-policy'      => 'handleUpdatePolicy',
    'delete-policy'      => 'handleDeletePolicy',
    'violations'         => 'handleViolations',
    'violation'          => 'handleViolation',
    'review-violation'   => 'handleReviewViolation',
    'flags'              => 'handleFlags',
    'review-flag'        => 'handleReviewFlag',
    'audit-log'          => 'handleAuditLog',
    'analytics-overview' => 'handleAnalyticsOverview',
    'ai-config'          => 'handleAIConfig',
    'update-ai-config'   => 'handleUpdateAIConfig',
    'export'             => 'handleExport',
]);

function handleUsers(): void {
    requireMethod('GET');
    $user = getCurrentUser();
    [$page, $perPage] = getPaginationParams();
    $where = 'school_id = ?';
    $params = [$user['school_id']];
    if (!empty($_GET['role'])) { $where .= ' AND role = ?'; $params[] = sanitize($_GET['role'], 'string'); }
    if (!empty($_GET['search'])) {
        $where .= ' AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)';
        $s = '%' . sanitize($_GET['search'], 'string') . '%';
        $params = array_merge($params, [$s, $s, $s]);
    }
    $result = paginate("SELECT id, email, first_name, last_name, role, status, created_at FROM users WHERE {$where} ORDER BY created_at DESC", $params, $page, $perPage);
    jsonSuccess($result);
}

function handleUser(): void {
    requireMethod('GET');
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) jsonError('User ID is required', 400);
    $u = fetch("SELECT id, email, first_name, last_name, role, status, avatar, created_at, last_login_at FROM users WHERE id = ?", [$id]);
    if (!$u) jsonError('User not found', 404);
    jsonSuccess(['user' => $u]);
}

function handleCreateUser(): void {
    requireMethod('POST');
    validateWriteCSRF();
    $data = getRequestBody();
    $errors = validate($data, ['email'=>'required|email|unique:users,email','first_name'=>'required|string|max:100','last_name'=>'required|string|max:100','role'=>'required|in:student,teacher,admin','password'=>'required|string|min:8']);
    if (!empty($errors)) jsonError('Validation failed', 422, $errors);
    $user = getCurrentUser();
    $id = insert('users', ['email'=>sanitize($data['email'],'email'),'first_name'=>sanitize($data['first_name'],'string'),'last_name'=>sanitize($data['last_name'],'string'),'role'=>$data['role'],'password_hash'=>hashPassword($data['password']),'school_id'=>$user['school_id'],'status'=>'active','created_at'=>date('Y-m-d H:i:s')]);
    jsonSuccess(['user_id' => $id], 'User created');
}

function handleUpdateUser(): void {
    requireMethod('PUT');
    validateWriteCSRF();
    $data = getRequestBody();
    $id = (int)($data['id'] ?? $_GET['id'] ?? 0);
    if ($id <= 0) jsonError('User ID is required', 400);
    $updateData = [];
    foreach (['first_name','last_name','email'] as $f) { if (isset($data[$f])) $updateData[$f] = sanitize($data[$f], 'string'); }
    if (empty($updateData)) jsonError('No fields to update', 400);
    update('users', $updateData, 'id = ?', [$id]);
    jsonSuccess(null, 'User updated');
}

function handleDeactivateUser(): void {
    requireMethod('POST');
    validateWriteCSRF();
    $data = getRequestBody();
    $id = (int)($data['user_id'] ?? 0);
    if ($id <= 0) jsonError('User ID is required', 400);
    update('users', ['status' => 'inactive'], 'id = ?', [$id]);
    logSecurityEvent('user_deactivated', ['user_id' => $id]);
    jsonSuccess(null, 'User deactivated');
}

function handleActivateUser(): void {
    requireMethod('POST');
    validateWriteCSRF();
    $data = getRequestBody();
    $id = (int)($data['user_id'] ?? 0);
    if ($id <= 0) jsonError('User ID is required', 400);
    update('users', ['status' => 'active'], 'id = ?', [$id]);
    jsonSuccess(null, 'User activated');
}

function handleUpdateRole(): void {
    requireMethod('PUT');
    validateWriteCSRF();
    $data = getRequestBody();
    $errors = validate($data, ['user_id'=>'required|integer','role'=>'required|in:student,teacher,admin']);
    if (!empty($errors)) jsonError('Validation failed', 422, $errors);
    update('users', ['role' => $data['role']], 'id = ?', [(int)$data['user_id']]);
    logSecurityEvent('role_changed', ['user_id' => (int)$data['user_id'], 'new_role' => $data['role']]);
    jsonSuccess(null, 'Role updated');
}

function handleSchool(): void {
    requireMethod('GET');
    $user = getCurrentUser();
    $school = fetch("SELECT * FROM schools WHERE id = ?", [$user['school_id']]);
    if (!$school) jsonError('School not found', 404);
    jsonSuccess(['school' => $school]);
}

function handleUpdateSchool(): void {
    requireMethod('PUT');
    validateWriteCSRF();
    $user = getCurrentUser();
    $data = getRequestBody();
    $updateData = [];
    foreach (['name','address','phone','website','settings'] as $f) { if (isset($data[$f])) $updateData[$f] = is_array($data[$f]) ? json_encode($data[$f]) : sanitize($data[$f], 'string'); }
    if (empty($updateData)) jsonError('No fields to update', 400);
    update('schools', $updateData, 'id = ?', [$user['school_id']]);
    jsonSuccess(null, 'School updated');
}

function handlePolicies(): void {
    requireMethod('GET');
    $user = getCurrentUser();
    $rules = fetchAll("SELECT * FROM ai_policy_rules WHERE school_id = ? ORDER BY created_at DESC", [$user['school_id']]);
    jsonSuccess(['policies' => $rules]);
}

function handleCreatePolicy(): void {
    requireMethod('POST');
    validateWriteCSRF();
    global $policyService;
    $user = getCurrentUser();
    $data = getRequestBody();
    $errors = validate($data, ['name'=>'required|string|max:255','action'=>'required|in:allow,deny,redirect','request_type'=>'string','strictness'=>'string|in:permissive,moderate,strict']);
    if (!empty($errors)) jsonError('Validation failed', 422, $errors);
    $id = insert('ai_policy_rules', ['school_id'=>$user['school_id'],'name'=>sanitize($data['name'],'string'),'action'=>$data['action'],'request_type'=>$data['request_type']??null,'strictness'=>$data['strictness']??'moderate','created_at'=>date('Y-m-d H:i:s')]);
    jsonSuccess(['policy_id' => $id], 'Policy created');
}

function handleUpdatePolicy(): void {
    requireMethod('PUT');
    validateWriteCSRF();
    global $policyService;
    $data = getRequestBody();
    $id = (int)($data['id'] ?? $_GET['id'] ?? 0);
    if ($id <= 0) jsonError('Policy ID is required', 400);
    $updateData = [];
    foreach (['name','action','request_type','strictness'] as $f) { if (isset($data[$f])) $updateData[$f] = sanitize($data[$f], 'string'); }
    if (empty($updateData)) jsonError('No fields to update', 400);
    $policyService->updateRule($id, $updateData);
    jsonSuccess(null, 'Policy updated');
}

function handleDeletePolicy(): void {
    requireMethod('DELETE');
    validateWriteCSRF();
    global $policyService;
    $data = getRequestBody();
    $id = (int)($data['id'] ?? $_GET['id'] ?? 0);
    if ($id <= 0) jsonError('Policy ID is required', 400);
    $policyService->deleteRule($id);
    jsonSuccess(null, 'Policy deleted');
}

function handleViolations(): void {
    requireMethod('GET');
    global $policyService;
    $user = getCurrentUser();
    [$page, $perPage] = getPaginationParams();
    $filters = ['school_id' => $user['school_id']];
    if (!empty($_GET['severity'])) $filters['severity'] = sanitize($_GET['severity'], 'string');
    if (!empty($_GET['status']))   $filters['status']   = sanitize($_GET['status'], 'string');
    $result = $policyService->getViolations($filters, $page, $perPage);
    jsonSuccess($result);
}

function handleViolation(): void {
    requireMethod('GET');
    global $policyService;
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) jsonError('Violation ID is required', 400);
    $violation = $policyService->getViolationById($id);
    if (!$violation) jsonError('Violation not found', 404);
    jsonSuccess(['violation' => $violation]);
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

function handleFlags(): void {
    requireMethod('GET');
    $user = getCurrentUser();
    [$page, $perPage] = getPaginationParams();
    $result = paginate("SELECT f.*, d.title as document_title, u.first_name, u.last_name FROM integrity_flags f JOIN documents d ON f.document_id = d.id JOIN users u ON f.user_id = u.id WHERE u.school_id = ? ORDER BY f.created_at DESC", [$user['school_id']], $page, $perPage);
    jsonSuccess($result);
}

function handleReviewFlag(): void {
    requireMethod('POST');
    validateWriteCSRF();
    global $policyService;
    $user = getCurrentUser();
    $data = getRequestBody();
    $flagId = (int)($data['flag_id'] ?? 0);
    if ($flagId <= 0) jsonError('Flag ID is required', 400);
    $policyService->reviewFlag($flagId, $user['id']);
    jsonSuccess(null, 'Flag reviewed');
}

function handleAuditLog(): void {
    requireMethod('GET');
    $user = getCurrentUser();
    [$page, $perPage] = getPaginationParams();
    $where = 'school_id = ?';
    $params = [$user['school_id']];
    if (!empty($_GET['user_id'])) { $where .= ' AND user_id = ?'; $params[] = (int)$_GET['user_id']; }
    if (!empty($_GET['action']))  { $where .= ' AND action = ?'; $params[] = sanitize($_GET['action'], 'string'); }
    $result = paginate("SELECT * FROM activity_logs WHERE {$where} ORDER BY created_at DESC", $params, $page, $perPage);
    jsonSuccess($result);
}

function handleAnalyticsOverview(): void {
    requireMethod('GET');
    global $analyticsService;
    $user = getCurrentUser();
    $schoolId = (int)($user['school_id'] ?? 0);
    $stats = $analyticsService->getAdminDashboardStats($schoolId);
    jsonSuccess(['stats' => $stats]);
}

function handleAIConfig(): void {
    requireMethod('GET');
    $user = getCurrentUser();
    $config = fetch("SELECT * FROM ai_configurations WHERE school_id = ?", [$user['school_id']]);
    jsonSuccess(['config' => $config ?? ['provider' => 'abacus', 'model' => AI_DEFAULT_MODEL ?? 'abacus-gpt4']]);
}

function handleUpdateAIConfig(): void {
    requireMethod('PUT');
    validateWriteCSRF();
    $user = getCurrentUser();
    $data = getRequestBody();
    $errors = validate($data, ['model'=>'string','max_tokens'=>'integer','temperature'=>'numeric']);
    if (!empty($errors)) jsonError('Validation failed', 422, $errors);
    $existing = fetch("SELECT id FROM ai_configurations WHERE school_id = ?", [$user['school_id']]);
    $configData = [];
    foreach (['model','max_tokens','temperature','rate_limit_per_minute','rate_limit_per_hour'] as $f) { if (isset($data[$f])) $configData[$f] = $data[$f]; }
    if (empty($configData)) jsonError('No fields to update', 400);
    if ($existing) { update('ai_configurations', $configData, 'school_id = ?', [$user['school_id']]); }
    else { $configData['school_id'] = $user['school_id']; insert('ai_configurations', $configData); }
    logSecurityEvent('ai_config_updated', ['school_id' => $user['school_id']]);
    jsonSuccess(null, 'AI configuration updated');
}

function handleExport(): void {
    requireMethod('POST');
    validateWriteCSRF();
    $user = getCurrentUser();
    $data = getRequestBody();
    $errors = validate($data, ['type'=>'required|in:users,analytics,audit_log,violations','format'=>'string|in:csv,xlsx,pdf']);
    if (!empty($errors)) jsonError('Validation failed', 422, $errors);
    $exportService = new ImportExportService();
    $jobId = $exportService->createExportJob($user['id'], $data['type'], ['school_id'=>$user['school_id'],'format'=>$data['format']??'csv']);
    jsonSuccess(['job_id' => $jobId], 'Export job created');
}
