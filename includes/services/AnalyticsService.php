<?php
/**
 * EduWrite AI - Analytics and Reporting Service
 *
 * Tracks user events, computes writing/AI usage statistics,
 * generates dashboard data, and produces exportable reports.
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../helpers/database.php';

class AnalyticsService
{
    /**
     * Track an analytics event.
     */
    public function trackEvent(int $userId, string $eventType, ?string $eventCategory = null, array $eventData = []): int
    {
        if (empty(trim($eventType))) {
            throw new InvalidArgumentException('Event type cannot be empty.');
        }

        $eventId = insert('analytics_events', [
            'user_id'        => $userId,
            'event_type'     => trim($eventType),
            'event_category' => $eventCategory ? trim($eventCategory) : null,
            'event_data'     => !empty($eventData) ? json_encode($eventData) : null,
            'session_id'     => $_SESSION['session_token'] ?? null,
            'page_url'       => $_SERVER['REQUEST_URI'] ?? null,
        ]);

        return (int) $eventId;
    }

    /**
     * Get student writing statistics over a period.
     */
    public function getStudentWritingStats(int $userId, string $period = '30d'): array
    {
        $interval = $this->periodToInterval($period);

        $totals = fetch(
            "SELECT COUNT(*) AS total_documents,
                    COALESCE(SUM(word_count), 0) AS total_words,
                    COALESCE(AVG(word_count), 0) AS avg_word_count,
                    COALESCE(MAX(word_count), 0) AS max_word_count,
                    SUM(CASE WHEN status = 'submitted' OR status = 'graded' THEN 1 ELSE 0 END) AS completed_docs
             FROM documents
             WHERE user_id = :uid AND created_at >= DATE_SUB(NOW(), INTERVAL {$interval})",
            [':uid' => $userId]
        );

        $dailyWords = fetchAll(
            "SELECT DATE(updated_at) AS date, COALESCE(SUM(word_count), 0) AS words, COUNT(*) AS docs_active
             FROM documents
             WHERE user_id = :uid AND updated_at >= DATE_SUB(NOW(), INTERVAL {$interval})
             GROUP BY DATE(updated_at)
             ORDER BY date ASC",
            [':uid' => $userId]
        );

        $versionActivity = fetch(
            "SELECT COUNT(*) AS total_saves,
                    SUM(CASE WHEN snapshot_type = 'autosave' THEN 1 ELSE 0 END) AS autosaves,
                    SUM(CASE WHEN snapshot_type = 'manual' THEN 1 ELSE 0 END) AS manual_saves,
                    SUM(CASE WHEN snapshot_type = 'submission' THEN 1 ELSE 0 END) AS submissions
             FROM document_versions dv
             JOIN documents d ON d.id = dv.document_id
             WHERE d.user_id = :uid AND dv.created_at >= DATE_SUB(NOW(), INTERVAL {$interval})",
            [':uid' => $userId]
        );

        $weeklyComparison = fetch(
            "SELECT COALESCE(SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN word_count ELSE 0 END), 0) AS this_week,
                    COALESCE(SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY) AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY) THEN word_count ELSE 0 END), 0) AS last_week
             FROM documents WHERE user_id = :uid",
            [':uid' => $userId]
        );

        $thisWeek = (int) ($weeklyComparison['this_week'] ?? 0);
        $lastWeek = (int) ($weeklyComparison['last_week'] ?? 0);
        $weeklyChange = $lastWeek > 0 ? round((($thisWeek - $lastWeek) / $lastWeek) * 100, 1) : ($thisWeek > 0 ? 100 : 0);

        return [
            'total_documents'   => (int) ($totals['total_documents'] ?? 0),
            'total_words'       => (int) ($totals['total_words'] ?? 0),
            'avg_word_count'    => round((float) ($totals['avg_word_count'] ?? 0)),
            'max_word_count'    => (int) ($totals['max_word_count'] ?? 0),
            'completed_docs'    => (int) ($totals['completed_docs'] ?? 0),
            'daily_words'       => $dailyWords,
            'version_activity'  => [
                'total_saves'  => (int) ($versionActivity['total_saves'] ?? 0),
                'autosaves'    => (int) ($versionActivity['autosaves'] ?? 0),
                'manual_saves' => (int) ($versionActivity['manual_saves'] ?? 0),
                'submissions'  => (int) ($versionActivity['submissions'] ?? 0),
            ],
            'weekly_comparison' => [
                'this_week'     => $thisWeek,
                'last_week'     => $lastWeek,
                'change_percent' => $weeklyChange,
            ],
        ];
    }

    /**
     * Get student AI usage statistics.
     */
    public function getStudentAIUsageStats(int $userId, string $period = '30d'): array
    {
        $interval = $this->periodToInterval($period);

        $totals = fetch(
            "SELECT COUNT(*) AS total_requests,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed,
                    SUM(CASE WHEN status = 'refused' THEN 1 ELSE 0 END) AS refused,
                    SUM(CASE WHEN status = 'redirected' THEN 1 ELSE 0 END) AS redirected,
                    COALESCE(SUM(tokens_used), 0) AS total_tokens,
                    COALESCE(AVG(response_time_ms), 0) AS avg_response_time
             FROM ai_requests
             WHERE user_id = :uid AND created_at >= DATE_SUB(NOW(), INTERVAL {$interval})",
            [':uid' => $userId]
        );

        $byType = fetchAll(
            "SELECT request_type, COUNT(*) AS count,
                    COALESCE(AVG(response_time_ms), 0) AS avg_time
             FROM ai_requests
             WHERE user_id = :uid AND created_at >= DATE_SUB(NOW(), INTERVAL {$interval})
             GROUP BY request_type
             ORDER BY count DESC",
            [':uid' => $userId]
        );

        $dailyUsage = fetchAll(
            "SELECT DATE(created_at) AS date, COUNT(*) AS requests
             FROM ai_requests
             WHERE user_id = :uid AND created_at >= DATE_SUB(NOW(), INTERVAL {$interval})
             GROUP BY DATE(created_at)
             ORDER BY date ASC",
            [':uid' => $userId]
        );

        $sessions = fetch(
            "SELECT COUNT(*) AS total_sessions,
                    COALESCE(AVG(request_count), 0) AS avg_requests_per_session
             FROM ai_sessions
             WHERE user_id = :uid AND created_at >= DATE_SUB(NOW(), INTERVAL {$interval})",
            [':uid' => $userId]
        );

        return [
            'total_requests'    => (int) ($totals['total_requests'] ?? 0),
            'completed'         => (int) ($totals['completed'] ?? 0),
            'refused'           => (int) ($totals['refused'] ?? 0),
            'redirected'        => (int) ($totals['redirected'] ?? 0),
            'total_tokens'      => (int) ($totals['total_tokens'] ?? 0),
            'avg_response_time' => round((float) ($totals['avg_response_time'] ?? 0)),
            'by_type'           => $byType,
            'daily_usage'       => $dailyUsage,
            'sessions'          => [
                'total'        => (int) ($sessions['total_sessions'] ?? 0),
                'avg_requests' => round((float) ($sessions['avg_requests_per_session'] ?? 0), 1),
            ],
        ];
    }

    /**
     * Get class-level analytics.
     */
    public function getClassAnalytics(int $classId, string $period = '30d'): array
    {
        $interval = $this->periodToInterval($period);

        $overview = fetch(
            "SELECT COUNT(DISTINCT e.student_id) AS active_students,
                    (SELECT COUNT(*) FROM assignments a WHERE a.class_id = :cid AND a.is_archived = 0) AS total_assignments
             FROM enrollments e WHERE e.class_id = :cid2 AND e.status = 'active'",
            [':cid' => $classId, ':cid2' => $classId]
        );

        $writingStats = fetch(
            "SELECT COUNT(*) AS total_documents,
                    COALESCE(SUM(d.word_count), 0) AS total_words,
                    COALESCE(AVG(d.word_count), 0) AS avg_word_count,
                    SUM(CASE WHEN d.status IN ('submitted','graded') THEN 1 ELSE 0 END) AS completed_docs
             FROM documents d
             WHERE d.assignment_id IN (SELECT id FROM assignments WHERE class_id = :cid)
               AND d.updated_at >= DATE_SUB(NOW(), INTERVAL {$interval})",
            [':cid' => $classId]
        );

        $aiStats = fetch(
            "SELECT COUNT(*) AS total_ai_requests,
                    SUM(CASE WHEN ar.status = 'refused' THEN 1 ELSE 0 END) AS refused_requests,
                    COUNT(DISTINCT ar.user_id) AS students_using_ai
             FROM ai_requests ar
             WHERE ar.document_id IN (
                 SELECT d.id FROM documents d
                 WHERE d.assignment_id IN (SELECT id FROM assignments WHERE class_id = :cid)
             )
             AND ar.created_at >= DATE_SUB(NOW(), INTERVAL {$interval})",
            [':cid' => $classId]
        );

        $violations = fetch(
            "SELECT COUNT(*) AS total_violations,
                    SUM(CASE WHEN pv.severity IN ('high','critical') THEN 1 ELSE 0 END) AS serious_violations
             FROM policy_violations pv
             WHERE pv.assignment_id IN (SELECT id FROM assignments WHERE class_id = :cid)
               AND pv.created_at >= DATE_SUB(NOW(), INTERVAL {$interval})",
            [':cid' => $classId]
        );

        $dailyActivity = fetchAll(
            "SELECT DATE(d.updated_at) AS date,
                    COUNT(DISTINCT d.user_id) AS active_students,
                    COALESCE(SUM(d.word_count), 0) AS words_written
             FROM documents d
             WHERE d.assignment_id IN (SELECT id FROM assignments WHERE class_id = :cid)
               AND d.updated_at >= DATE_SUB(NOW(), INTERVAL {$interval})
             GROUP BY DATE(d.updated_at)
             ORDER BY date ASC",
            [':cid' => $classId]
        );

        return [
            'active_students'    => (int) ($overview['active_students'] ?? 0),
            'total_assignments'  => (int) ($overview['total_assignments'] ?? 0),
            'total_documents'    => (int) ($writingStats['total_documents'] ?? 0),
            'total_words'        => (int) ($writingStats['total_words'] ?? 0),
            'avg_word_count'     => round((float) ($writingStats['avg_word_count'] ?? 0)),
            'completed_docs'     => (int) ($writingStats['completed_docs'] ?? 0),
            'total_ai_requests'  => (int) ($aiStats['total_ai_requests'] ?? 0),
            'refused_requests'   => (int) ($aiStats['refused_requests'] ?? 0),
            'students_using_ai'  => (int) ($aiStats['students_using_ai'] ?? 0),
            'total_violations'   => (int) ($violations['total_violations'] ?? 0),
            'serious_violations' => (int) ($violations['serious_violations'] ?? 0),
            'daily_activity'     => $dailyActivity,
        ];
    }

    /**
     * Get per-assignment analytics.
     */
    public function getAssignmentAnalytics(int $assignmentId): array
    {
        $assignment = fetch(
            "SELECT a.id, a.title, a.class_id, a.due_date
             FROM assignments a WHERE a.id = :id",
            [':id' => $assignmentId]
        );
        if (!$assignment) {
            throw new RuntimeException('Assignment not found.');
        }

        $totalStudents = count_rows('enrollments', "class_id = :cid AND status = 'active'", [':cid' => $assignment['class_id']]);

        $docStats = fetch(
            "SELECT COUNT(*) AS total_docs,
                    COALESCE(AVG(word_count), 0) AS avg_words,
                    COALESCE(MIN(word_count), 0) AS min_words,
                    COALESCE(MAX(word_count), 0) AS max_words,
                    SUM(CASE WHEN status = 'submitted' OR status = 'graded' THEN 1 ELSE 0 END) AS completed,
                    SUM(CASE WHEN status = 'graded' THEN 1 ELSE 0 END) AS graded
             FROM documents WHERE assignment_id = :aid",
            [':aid' => $assignmentId]
        );

        $gradeDistribution = fetchAll(
            "SELECT tc.grade, COUNT(*) AS count
             FROM teacher_comments tc
             WHERE tc.assignment_id = :aid AND tc.comment_type = 'grade' AND tc.grade IS NOT NULL
             GROUP BY tc.grade
             ORDER BY tc.grade DESC",
            [':aid' => $assignmentId]
        );

        $aiUsage = fetch(
            "SELECT COUNT(*) AS total_requests,
                    COUNT(DISTINCT ar.user_id) AS students_using_ai,
                    SUM(CASE WHEN ar.status = 'refused' THEN 1 ELSE 0 END) AS refused
             FROM ai_requests ar
             WHERE ar.document_id IN (SELECT id FROM documents WHERE assignment_id = :aid)",
            [':aid' => $assignmentId]
        );

        $submissionTimeline = fetchAll(
            "SELECT DATE(submitted_at) AS date, COUNT(*) AS submissions
             FROM documents
             WHERE assignment_id = :aid AND submitted_at IS NOT NULL
             GROUP BY DATE(submitted_at)
             ORDER BY date ASC",
            [':aid' => $assignmentId]
        );

        $notStarted = max(0, $totalStudents - (int) ($docStats['total_docs'] ?? 0));

        return [
            'assignment'          => $assignment,
            'total_students'      => $totalStudents,
            'total_docs'          => (int) ($docStats['total_docs'] ?? 0),
            'not_started'         => $notStarted,
            'completed'           => (int) ($docStats['completed'] ?? 0),
            'graded'              => (int) ($docStats['graded'] ?? 0),
            'avg_words'           => round((float) ($docStats['avg_words'] ?? 0)),
            'min_words'           => (int) ($docStats['min_words'] ?? 0),
            'max_words'           => (int) ($docStats['max_words'] ?? 0),
            'completion_rate'     => $totalStudents > 0 ? round(((int) ($docStats['completed'] ?? 0) / $totalStudents) * 100, 1) : 0,
            'grade_distribution'  => $gradeDistribution,
            'ai_requests'         => (int) ($aiUsage['total_requests'] ?? 0),
            'students_using_ai'   => (int) ($aiUsage['students_using_ai'] ?? 0),
            'ai_refused'          => (int) ($aiUsage['refused'] ?? 0),
            'submission_timeline' => $submissionTimeline,
        ];
    }

    /**
     * Get school-wide analytics.
     */
    public function getSchoolAnalytics(int $schoolId, string $period = '30d'): array
    {
        $interval = $this->periodToInterval($period);

        $users = fetch(
            "SELECT COUNT(*) AS total_users,
                    SUM(CASE WHEN role = 'student' THEN 1 ELSE 0 END) AS students,
                    SUM(CASE WHEN role = 'teacher' THEN 1 ELSE 0 END) AS teachers,
                    SUM(CASE WHEN last_login_at >= DATE_SUB(NOW(), INTERVAL {$interval}) THEN 1 ELSE 0 END) AS active_users
             FROM users WHERE school_id = :sid AND is_active = 1",
            [':sid' => $schoolId]
        );

        $classes = fetch(
            "SELECT COUNT(*) AS total_classes,
                    SUM(CASE WHEN is_active = 1 AND is_archived = 0 THEN 1 ELSE 0 END) AS active_classes
             FROM classes WHERE school_id = :sid",
            [':sid' => $schoolId]
        );

        $documents = fetch(
            "SELECT COUNT(*) AS total_documents,
                    COALESCE(SUM(word_count), 0) AS total_words
             FROM documents d
             JOIN users u ON u.id = d.user_id
             WHERE u.school_id = :sid AND d.created_at >= DATE_SUB(NOW(), INTERVAL {$interval})",
            [':sid' => $schoolId]
        );

        $aiUsage = fetch(
            "SELECT COUNT(*) AS total_requests,
                    COALESCE(SUM(tokens_used), 0) AS total_tokens
             FROM ai_requests ar
             JOIN users u ON u.id = ar.user_id
             WHERE u.school_id = :sid AND ar.created_at >= DATE_SUB(NOW(), INTERVAL {$interval})",
            [':sid' => $schoolId]
        );

        $violations = fetch(
            "SELECT COUNT(*) AS total_violations
             FROM policy_violations pv
             JOIN users u ON u.id = pv.user_id
             WHERE u.school_id = :sid AND pv.created_at >= DATE_SUB(NOW(), INTERVAL {$interval})",
            [':sid' => $schoolId]
        );

        return [
            'total_users'      => (int) ($users['total_users'] ?? 0),
            'students'         => (int) ($users['students'] ?? 0),
            'teachers'         => (int) ($users['teachers'] ?? 0),
            'active_users'     => (int) ($users['active_users'] ?? 0),
            'total_classes'    => (int) ($classes['total_classes'] ?? 0),
            'active_classes'   => (int) ($classes['active_classes'] ?? 0),
            'total_documents'  => (int) ($documents['total_documents'] ?? 0),
            'total_words'      => (int) ($documents['total_words'] ?? 0),
            'total_ai_requests' => (int) ($aiUsage['total_requests'] ?? 0),
            'total_ai_tokens'  => (int) ($aiUsage['total_tokens'] ?? 0),
            'total_violations' => (int) ($violations['total_violations'] ?? 0),
        ];
    }

    /**
     * Get teacher dashboard statistics.
     */
    public function getTeacherDashboardStats(int $teacherId): array
    {
        $classes = fetch(
            "SELECT COUNT(*) AS total_classes,
                    (SELECT COUNT(*) FROM enrollments e
                     JOIN classes c ON c.id = e.class_id
                     WHERE c.teacher_id = :tid AND e.status = 'active' AND c.is_active = 1) AS total_students
             FROM classes WHERE teacher_id = :tid2 AND is_active = 1 AND is_archived = 0",
            [':tid' => $teacherId, ':tid2' => $teacherId]
        );

        $assignments = fetch(
            "SELECT COUNT(*) AS total_assignments,
                    SUM(CASE WHEN due_date IS NOT NULL AND due_date >= NOW() THEN 1 ELSE 0 END) AS active_assignments
             FROM assignments a
             JOIN classes c ON c.id = a.class_id
             WHERE c.teacher_id = :tid AND a.is_archived = 0 AND c.is_active = 1",
            [':tid' => $teacherId]
        );

        $pendingSubmissions = fetch(
            "SELECT COUNT(*) AS pending
             FROM documents d
             JOIN assignments a ON a.id = d.assignment_id
             JOIN classes c ON c.id = a.class_id
             WHERE c.teacher_id = :tid AND d.status = 'submitted'",
            [':tid' => $teacherId]
        );

        $pendingViolations = fetch(
            "SELECT COUNT(*) AS pending
             FROM policy_violations pv
             JOIN assignments a ON a.id = pv.assignment_id
             JOIN classes c ON c.id = a.class_id
             WHERE c.teacher_id = :tid AND pv.status = 'pending'",
            [':tid' => $teacherId]
        );

        $recentSubmissions = fetchAll(
            "SELECT d.id, d.title, d.submitted_at, d.word_count,
                    u.first_name, u.last_name,
                    a.title AS assignment_title
             FROM documents d
             JOIN users u ON u.id = d.user_id
             JOIN assignments a ON a.id = d.assignment_id
             JOIN classes c ON c.id = a.class_id
             WHERE c.teacher_id = :tid AND d.status = 'submitted'
             ORDER BY d.submitted_at DESC
             LIMIT 10",
            [':tid' => $teacherId]
        );

        return [
            'total_classes'         => (int) ($classes['total_classes'] ?? 0),
            'total_students'        => (int) ($classes['total_students'] ?? 0),
            'total_assignments'     => (int) ($assignments['total_assignments'] ?? 0),
            'active_assignments'    => (int) ($assignments['active_assignments'] ?? 0),
            'pending_submissions'   => (int) ($pendingSubmissions['pending'] ?? 0),
            'pending_violations'    => (int) ($pendingViolations['pending'] ?? 0),
            'recent_submissions'    => $recentSubmissions,
        ];
    }

    /**
     * Get student dashboard statistics.
     */
    public function getStudentDashboardStats(int $studentId): array
    {
        $classes = fetch(
            "SELECT COUNT(*) AS enrolled_classes
             FROM enrollments WHERE student_id = :sid AND status = 'active'",
            [':sid' => $studentId]
        );

        $docs = fetch(
            "SELECT COUNT(*) AS total_documents,
                    COALESCE(SUM(word_count), 0) AS total_words,
                    SUM(CASE WHEN status = 'in_progress' OR status = 'draft' THEN 1 ELSE 0 END) AS active_docs
             FROM documents WHERE user_id = :uid",
            [':uid' => $studentId]
        );

        $upcoming = fetchAll(
            "SELECT a.id, a.title, a.due_date, c.name AS class_name,
                    d.id AS document_id, d.status AS document_status
             FROM assignments a
             JOIN classes c ON c.id = a.class_id
             JOIN enrollments e ON e.class_id = c.id AND e.student_id = :sid AND e.status = 'active'
             LEFT JOIN documents d ON d.assignment_id = a.id AND d.user_id = :sid2
             WHERE a.is_published = 1 AND a.is_archived = 0 AND a.due_date > NOW() AND c.is_active = 1
             ORDER BY a.due_date ASC
             LIMIT 5",
            [':sid' => $studentId, ':sid2' => $studentId]
        );

        $overdue = fetch(
            "SELECT COUNT(*) AS overdue_count
             FROM assignments a
             JOIN classes c ON c.id = a.class_id
             JOIN enrollments e ON e.class_id = c.id AND e.student_id = :sid AND e.status = 'active'
             LEFT JOIN documents d ON d.assignment_id = a.id AND d.user_id = :sid2
             WHERE a.is_published = 1 AND a.is_archived = 0 AND a.due_date < NOW()
               AND (d.status IS NULL OR d.status NOT IN ('submitted','graded'))",
            [':sid' => $studentId, ':sid2' => $studentId]
        );

        $aiUsage = fetch(
            "SELECT COUNT(*) AS requests_today
             FROM ai_requests WHERE user_id = :uid AND created_at >= CURDATE()",
            [':uid' => $studentId]
        );

        $recentGrades = fetchAll(
            "SELECT tc.grade, tc.comment, a.title AS assignment_title, tc.created_at
             FROM teacher_comments tc
             JOIN assignments a ON a.id = tc.assignment_id
             WHERE tc.student_id = :sid AND tc.comment_type = 'grade'
             ORDER BY tc.created_at DESC
             LIMIT 5",
            [':sid' => $studentId]
        );

        return [
            'enrolled_classes'   => (int) ($classes['enrolled_classes'] ?? 0),
            'total_documents'    => (int) ($docs['total_documents'] ?? 0),
            'total_words'        => (int) ($docs['total_words'] ?? 0),
            'active_docs'        => (int) ($docs['active_docs'] ?? 0),
            'upcoming_assignments' => $upcoming,
            'overdue_count'      => (int) ($overdue['overdue_count'] ?? 0),
            'ai_requests_today'  => (int) ($aiUsage['requests_today'] ?? 0),
            'recent_grades'      => $recentGrades,
        ];
    }

    /**
     * Get admin dashboard statistics.
     */
    public function getAdminDashboardStats(int $schoolId): array
    {
        $users = fetch(
            "SELECT COUNT(*) AS total_users,
                    SUM(CASE WHEN role = 'student' THEN 1 ELSE 0 END) AS students,
                    SUM(CASE WHEN role = 'teacher' THEN 1 ELSE 0 END) AS teachers,
                    SUM(CASE WHEN last_login_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) AS active_this_week
             FROM users WHERE school_id = :sid AND is_active = 1",
            [':sid' => $schoolId]
        );

        $classes = fetch(
            "SELECT COUNT(*) AS total, SUM(CASE WHEN is_active = 1 AND is_archived = 0 THEN 1 ELSE 0 END) AS active
             FROM classes WHERE school_id = :sid",
            [':sid' => $schoolId]
        );

        $documents = fetch(
            "SELECT COUNT(*) AS total, COALESCE(SUM(word_count), 0) AS total_words
             FROM documents d JOIN users u ON u.id = d.user_id WHERE u.school_id = :sid",
            [':sid' => $schoolId]
        );

        $aiRequests = fetch(
            "SELECT COUNT(*) AS total,
                    COALESCE(SUM(tokens_used), 0) AS tokens
             FROM ai_requests ar JOIN users u ON u.id = ar.user_id
             WHERE u.school_id = :sid AND ar.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
            [':sid' => $schoolId]
        );

        $violations = fetch(
            "SELECT COUNT(*) AS total, SUM(CASE WHEN pv.status = 'pending' THEN 1 ELSE 0 END) AS pending
             FROM policy_violations pv JOIN users u ON u.id = pv.user_id WHERE u.school_id = :sid",
            [':sid' => $schoolId]
        );

        return [
            'total_users'       => (int) ($users['total_users'] ?? 0),
            'students'          => (int) ($users['students'] ?? 0),
            'teachers'          => (int) ($users['teachers'] ?? 0),
            'active_this_week'  => (int) ($users['active_this_week'] ?? 0),
            'total_classes'     => (int) ($classes['total'] ?? 0),
            'active_classes'    => (int) ($classes['active'] ?? 0),
            'total_documents'   => (int) ($documents['total'] ?? 0),
            'total_words'       => (int) ($documents['total_words'] ?? 0),
            'ai_requests_30d'   => (int) ($aiRequests['total'] ?? 0),
            'ai_tokens_30d'     => (int) ($aiRequests['tokens'] ?? 0),
            'total_violations'  => (int) ($violations['total'] ?? 0),
            'pending_violations' => (int) ($violations['pending'] ?? 0),
        ];
    }

    /**
     * Get data for a writing progress chart.
     */
    public function getWritingProgressChart(int $userId, string $period = '30d'): array
    {
        $interval = $this->periodToInterval($period);

        return fetchAll(
            "SELECT DATE(created_at) AS date,
                    COUNT(*) AS documents_created,
                    COALESCE(SUM(word_count), 0) AS total_words
             FROM documents
             WHERE user_id = :uid AND created_at >= DATE_SUB(NOW(), INTERVAL {$interval})
             GROUP BY DATE(created_at)
             ORDER BY date ASC",
            [':uid' => $userId]
        );
    }

    /**
     * Get AI usage chart data for a class.
     */
    public function getAIUsageChart(int $classId, string $period = '30d'): array
    {
        $interval = $this->periodToInterval($period);

        return fetchAll(
            "SELECT DATE(ar.created_at) AS date,
                    COUNT(*) AS total_requests,
                    SUM(CASE WHEN ar.status = 'completed' THEN 1 ELSE 0 END) AS completed,
                    SUM(CASE WHEN ar.status = 'refused' THEN 1 ELSE 0 END) AS refused,
                    COUNT(DISTINCT ar.user_id) AS unique_users
             FROM ai_requests ar
             WHERE ar.document_id IN (
                 SELECT d.id FROM documents d
                 WHERE d.assignment_id IN (SELECT id FROM assignments WHERE class_id = :cid)
             )
             AND ar.created_at >= DATE_SUB(NOW(), INTERVAL {$interval})
             GROUP BY DATE(ar.created_at)
             ORDER BY date ASC",
            [':cid' => $classId]
        );
    }

    /**
     * Get submission rate chart data.
     */
    public function getSubmissionRateChart(int $classId, string $period = '30d'): array
    {
        $interval = $this->periodToInterval($period);

        return fetchAll(
            "SELECT DATE(d.submitted_at) AS date, COUNT(*) AS submissions
             FROM documents d
             WHERE d.assignment_id IN (SELECT id FROM assignments WHERE class_id = :cid)
               AND d.submitted_at IS NOT NULL
               AND d.submitted_at >= DATE_SUB(NOW(), INTERVAL {$interval})
             GROUP BY DATE(d.submitted_at)
             ORDER BY date ASC",
            [':cid' => $classId]
        );
    }

    /**
     * Get top writers in a class by word count.
     */
    public function getTopWriters(int $classId, int $limit = 10): array
    {
        $limit = max(1, min($limit, 50));

        return fetchAll(
            "SELECT u.id, u.first_name, u.last_name, u.username,
                    COALESCE(SUM(d.word_count), 0) AS total_words,
                    COUNT(d.id) AS document_count,
                    SUM(CASE WHEN d.status IN ('submitted','graded') THEN 1 ELSE 0 END) AS completed
             FROM enrollments e
             JOIN users u ON u.id = e.student_id
             LEFT JOIN documents d ON d.user_id = u.id
                 AND d.assignment_id IN (SELECT id FROM assignments WHERE class_id = :cid)
             WHERE e.class_id = :cid2 AND e.status = 'active'
             GROUP BY u.id, u.first_name, u.last_name, u.username
             ORDER BY total_words DESC
             LIMIT {$limit}",
            [':cid' => $classId, ':cid2' => $classId]
        );
    }

    /**
     * Calculate engagement score for a student in a class (0-100).
     */
    public function getEngagementScore(int $userId, int $classId): array
    {
        $totalAssignments = count_rows('assignments', "class_id = :cid AND is_published = 1 AND is_archived = 0", [':cid' => $classId]);

        $completed = fetch(
            "SELECT COUNT(*) AS cnt FROM documents d
             WHERE d.user_id = :uid
               AND d.assignment_id IN (SELECT id FROM assignments WHERE class_id = :cid)
               AND d.status IN ('submitted','graded')",
            [':uid' => $userId, ':cid' => $classId]
        );
        $completionRate = $totalAssignments > 0
            ? ((int) ($completed['cnt'] ?? 0) / $totalAssignments)
            : 0;

        $onTimeRate = 0;
        if ($totalAssignments > 0) {
            $onTime = fetch(
                "SELECT COUNT(*) AS cnt FROM documents d
                 JOIN assignments a ON a.id = d.assignment_id
                 WHERE d.user_id = :uid AND a.class_id = :cid
                   AND d.status IN ('submitted','graded')
                   AND (a.due_date IS NULL OR d.submitted_at <= a.due_date)",
                [':uid' => $userId, ':cid' => $classId]
            );
            $totalWithDue = (int) ($completed['cnt'] ?? 0);
            $onTimeRate = $totalWithDue > 0 ? ((int) ($onTime['cnt'] ?? 0) / $totalWithDue) : 1;
        }

        $aiActivity = fetch(
            "SELECT COUNT(*) AS cnt FROM ai_requests ar
             WHERE ar.user_id = :uid
               AND ar.document_id IN (
                   SELECT d.id FROM documents d
                   WHERE d.assignment_id IN (SELECT id FROM assignments WHERE class_id = :cid)
               )
               AND ar.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
            [':uid' => $userId, ':cid' => $classId]
        );
        $aiUsageNormalized = min(1, (int) ($aiActivity['cnt'] ?? 0) / 20);

        $versionActivity = fetch(
            "SELECT COUNT(*) AS cnt FROM document_versions dv
             JOIN documents d ON d.id = dv.document_id
             WHERE d.user_id = :uid
               AND d.assignment_id IN (SELECT id FROM assignments WHERE class_id = :cid)",
            [':uid' => $userId, ':cid' => $classId]
        );
        $revisionNormalized = min(1, (int) ($versionActivity['cnt'] ?? 0) / 15);

        $score = round(
            ($completionRate * 40) +
            ($onTimeRate * 25) +
            ($aiUsageNormalized * 15) +
            ($revisionNormalized * 20),
            1
        );

        return [
            'score'           => min(100, $score),
            'completion_rate' => round($completionRate * 100, 1),
            'on_time_rate'    => round($onTimeRate * 100, 1),
            'ai_engagement'   => round($aiUsageNormalized * 100, 1),
            'revision_effort' => round($revisionNormalized * 100, 1),
        ];
    }

    /**
     * Export analytics report (returns structured data).
     */
    public function exportAnalyticsReport(array $params): array
    {
        $reportType = $params['type'] ?? 'class';
        $period = $params['period'] ?? '30d';

        switch ($reportType) {
            case 'class':
                if (empty($params['class_id'])) {
                    throw new InvalidArgumentException('class_id is required for class report.');
                }
                $data = $this->getClassAnalytics((int) $params['class_id'], $period);
                $data['report_type'] = 'class';
                break;

            case 'student':
                if (empty($params['user_id'])) {
                    throw new InvalidArgumentException('user_id is required for student report.');
                }
                $data = [
                    'writing_stats' => $this->getStudentWritingStats((int) $params['user_id'], $period),
                    'ai_usage'      => $this->getStudentAIUsageStats((int) $params['user_id'], $period),
                    'report_type'   => 'student',
                ];
                break;

            case 'school':
                if (empty($params['school_id'])) {
                    throw new InvalidArgumentException('school_id is required for school report.');
                }
                $data = $this->getSchoolAnalytics((int) $params['school_id'], $period);
                $data['report_type'] = 'school';
                break;

            default:
                throw new InvalidArgumentException('Unsupported report type: ' . $reportType);
        }

        $data['generated_at'] = date('Y-m-d H:i:s');
        $data['period'] = $period;

        return $data;
    }

    /**
     * Get a user's recent activity timeline.
     */
    public function getActivityTimeline(int $userId, int $limit = 20): array
    {
        $limit = max(1, min($limit, 100));

        return fetchAll(
            "SELECT al.id, al.action, al.entity_type, al.entity_id, al.details, al.created_at
             FROM activity_logs al
             WHERE al.user_id = :uid
             ORDER BY al.created_at DESC
             LIMIT {$limit}",
            [':uid' => $userId]
        );
    }

    /**
     * Convert period string to SQL INTERVAL value.
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
