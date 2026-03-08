<?php
/**
 * EduWrite AI - Analytics API Endpoint
 *
 * Student, class, school, and dashboard analytics with chart data endpoints.
 */

require_once __DIR__ . '/router.php';
require_once BASE_PATH . '/includes/services/AnalyticsService.php';

requireAuth();

$analyticsService = new AnalyticsService();

routeAction([
    'student-stats'     => 'handleStudentStats',
    'class-stats'       => 'handleClassStats',
    'assignment-stats'  => 'handleAssignmentStats',
    'school-stats'      => 'handleSchoolStats',
    'dashboard'         => 'handleDashboard',
    'writing-progress'  => 'handleWritingProgress',
    'ai-usage'          => 'handleAIUsage',
    'activity-timeline' => 'handleActivityTimeline',
    'engagement'        => 'handleEngagement',
]);

/** GET - Get student writing and AI usage stats */
function handleStudentStats(): void {
    requireMethod('GET');

    global $analyticsService;
    $user = getCurrentUser();
    $period = getPeriodParam();

    // Teachers/admins can view any student's stats
    $studentId = $user['id'];
    if (in_array($user['role'], ['teacher', 'admin']) && !empty($_GET['student_id'])) {
        $studentId = (int)$_GET['student_id'];
    }

    $writingStats = $analyticsService->getStudentWritingStats($studentId, $period);
    $aiUsageStats = $analyticsService->getStudentAIUsageStats($studentId, $period);

    jsonSuccess([
        'writing' => $writingStats,
        'ai'      => $aiUsageStats,
    ]);
}

/** GET - Get class analytics (teacher only) */
function handleClassStats(): void {
    requireMethod('GET');
    requireRole(['teacher', 'admin']);

    global $analyticsService;

    $classId = (int)($_GET['class_id'] ?? 0);
    if ($classId <= 0) {
        jsonError('Class ID is required', 400);
    }

    $period = getPeriodParam();
    $stats = $analyticsService->getClassAnalytics($classId, $period);
    jsonSuccess(['stats' => $stats]);
}

/** GET - Get assignment analytics (teacher only) */
function handleAssignmentStats(): void {
    requireMethod('GET');
    requireRole(['teacher', 'admin']);

    global $analyticsService;

    $assignmentId = (int)($_GET['assignment_id'] ?? 0);
    if ($assignmentId <= 0) {
        jsonError('Assignment ID is required', 400);
    }

    $stats = $analyticsService->getAssignmentAnalytics($assignmentId);
    jsonSuccess(['stats' => $stats]);
}

/** GET - Get school-wide analytics (admin only) */
function handleSchoolStats(): void {
    requireMethod('GET');
    requireRole('admin');

    global $analyticsService;
    $user = getCurrentUser();

    $schoolId = (int)($_GET['school_id'] ?? $user['school_id'] ?? 0);
    if ($schoolId <= 0) {
        jsonError('School ID is required', 400);
    }

    $period = getPeriodParam();
    $stats = $analyticsService->getSchoolAnalytics($schoolId, $period);
    jsonSuccess(['stats' => $stats]);
}

/** GET - Get role-appropriate dashboard statistics */
function handleDashboard(): void {
    requireMethod('GET');

    global $analyticsService;
    $user = getCurrentUser();

    switch ($user['role']) {
        case 'student':
            $stats = $analyticsService->getStudentDashboardStats($user['id']);
            break;

        case 'teacher':
            $stats = $analyticsService->getTeacherDashboardStats($user['id']);
            break;

        case 'admin':
            $schoolId = (int)($user['school_id'] ?? 0);
            $stats = $analyticsService->getAdminDashboardStats($schoolId);
            break;

        default:
            jsonError('Unknown user role', 400);
            return;
    }

    jsonSuccess(['stats' => $stats]);
}

/** GET - Get writing progress chart data */
function handleWritingProgress(): void {
    requireMethod('GET');

    global $analyticsService;
    $user = getCurrentUser();

    $userId = $user['id'];
    if (in_array($user['role'], ['teacher', 'admin']) && !empty($_GET['student_id'])) {
        $userId = (int)$_GET['student_id'];
    }

    $period = getPeriodParam();
    $chartData = $analyticsService->getWritingProgressChart($userId, $period);
    jsonSuccess(['chart' => $chartData]);
}

/** GET - Get AI usage chart data */
function handleAIUsage(): void {
    requireMethod('GET');

    global $analyticsService;
    $user = getCurrentUser();

    // Class-level AI usage for teachers, personal for students
    if (in_array($user['role'], ['teacher', 'admin']) && !empty($_GET['class_id'])) {
        $classId = (int)$_GET['class_id'];
        $period = getPeriodParam();
        $chartData = $analyticsService->getAIUsageChart($classId, $period);
    } else {
        $period = getPeriodParam();
        $stats = $analyticsService->getStudentAIUsageStats($user['id'], $period);
        $chartData = $stats;
    }

    jsonSuccess(['chart' => $chartData]);
}

/** GET - Get activity timeline */
function handleActivityTimeline(): void {
    requireMethod('GET');

    global $analyticsService;
    $user = getCurrentUser();

    $userId = $user['id'];
    if (in_array($user['role'], ['teacher', 'admin']) && !empty($_GET['student_id'])) {
        $userId = (int)$_GET['student_id'];
    }

    $limit = min(100, max(5, (int)($_GET['limit'] ?? 20)));
    $timeline = $analyticsService->getActivityTimeline($userId, $limit);
    jsonSuccess(['timeline' => $timeline]);
}

/** GET - Get engagement scores */
function handleEngagement(): void {
    requireMethod('GET');

    global $analyticsService;
    $user = getCurrentUser();

    $userId = $user['id'];
    if (in_array($user['role'], ['teacher', 'admin']) && !empty($_GET['student_id'])) {
        $userId = (int)$_GET['student_id'];
    }

    $classId = (int)($_GET['class_id'] ?? 0);
    if ($classId <= 0) {
        jsonError('Class ID is required', 400);
    }

    $engagement = $analyticsService->getEngagementScore($userId, $classId);
    jsonSuccess(['engagement' => $engagement]);
}
