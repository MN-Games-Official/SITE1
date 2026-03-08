<?php
/**
 * EduWrite AI - Notification Service
 *
 * Manages user notifications: creation, retrieval, read status,
 * cleanup, and event-driven notification dispatching.
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../helpers/database.php';
require_once __DIR__ . '/../helpers/security.php';

class NotificationService
{
    /**
     * Create a notification.
     */
    public function create(int $userId, string $type, string $title, string $message = '', ?string $link = null, array $metadata = []): int
    {
        $validTypes = ['assignment', 'comment', 'flag', 'grade', 'system', 'policy', 'ai_alert', 'class', 'reminder'];
        if (!in_array($type, $validTypes, true)) {
            throw new InvalidArgumentException('Invalid notification type.');
        }

        if (empty(trim($title))) {
            throw new InvalidArgumentException('Notification title cannot be empty.');
        }

        $notificationId = insert('notifications', [
            'user_id'  => $userId,
            'type'     => $type,
            'title'    => trim($title),
            'message'  => trim($message) ?: null,
            'link'     => $link,
            'is_read'  => 0,
            'metadata' => !empty($metadata) ? json_encode($metadata) : null,
        ]);

        return (int) $notificationId;
    }

    /**
     * Get paginated notifications for a user.
     */
    public function getForUser(int $userId, int $page = 1, int $perPage = 20): array
    {
        $sql = "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC";
        return paginate($sql, [':user_id' => $userId], $page, $perPage);
    }

    /**
     * Get unread notification count for a user.
     */
    public function getUnreadCount(int $userId): int
    {
        return count_rows('notifications', 'user_id = :uid AND is_read = 0', [':uid' => $userId]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        $notification = fetch(
            "SELECT id, user_id, is_read FROM notifications WHERE id = :id",
            [':id' => $notificationId]
        );
        if (!$notification) {
            throw new RuntimeException('Notification not found.');
        }
        if ((int) $notification['user_id'] !== $userId) {
            throw new RuntimeException('You do not own this notification.');
        }
        if ($notification['is_read']) {
            return true;
        }

        update('notifications', [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s'),
        ], 'id = :id', [':id' => $notificationId]);

        return true;
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(int $userId): int
    {
        $stmt = query(
            "UPDATE `notifications` SET `is_read` = 1, `read_at` = :now WHERE `user_id` = :uid AND `is_read` = 0",
            [':now' => date('Y-m-d H:i:s'), ':uid' => $userId]
        );
        return $stmt->rowCount();
    }

    /**
     * Delete a notification.
     */
    public function deleteNotification(int $id, int $userId): bool
    {
        $notification = fetch("SELECT id, user_id FROM notifications WHERE id = :id", [':id' => $id]);
        if (!$notification) {
            throw new RuntimeException('Notification not found.');
        }
        if ((int) $notification['user_id'] !== $userId) {
            throw new RuntimeException('You do not own this notification.');
        }

        delete('notifications', 'id = :id', [':id' => $id]);
        return true;
    }

    /**
     * Delete old notifications (cleanup).
     */
    public function deleteOld(int $daysOld = 90): int
    {
        $daysOld = max(1, $daysOld);
        $stmt = query(
            "DELETE FROM `notifications` WHERE `created_at` < DATE_SUB(NOW(), INTERVAL :days DAY)",
            [':days' => $daysOld]
        );
        return $stmt->rowCount();
    }

    /**
     * Notify all enrolled students that a new assignment was created.
     */
    public function notifyAssignmentCreated(int $assignmentId): int
    {
        $assignment = fetch(
            "SELECT a.id, a.title, a.class_id, a.due_date, c.name AS class_name,
                    u.first_name AS teacher_first_name, u.last_name AS teacher_last_name
             FROM assignments a
             JOIN classes c ON c.id = a.class_id
             JOIN users u ON u.id = a.teacher_id
             WHERE a.id = :id",
            [':id' => $assignmentId]
        );
        if (!$assignment) {
            throw new RuntimeException('Assignment not found.');
        }

        $students = fetchAll(
            "SELECT student_id FROM enrollments WHERE class_id = :cid AND status = 'active'",
            [':cid' => $assignment['class_id']]
        );

        $count = 0;
        $teacherName = trim($assignment['teacher_first_name'] . ' ' . $assignment['teacher_last_name']);
        $dueInfo = $assignment['due_date'] ? ' Due: ' . date('M j, Y g:i A', strtotime($assignment['due_date'])) . '.' : '';
        $link = '/assignments/' . $assignmentId;

        foreach ($students as $student) {
            $this->create(
                (int) $student['student_id'],
                'assignment',
                'New Assignment: ' . $assignment['title'],
                "{$teacherName} posted a new assignment in {$assignment['class_name']}.{$dueInfo}",
                $link,
                ['assignment_id' => $assignmentId, 'class_id' => $assignment['class_id']]
            );
            $count++;
        }

        return $count;
    }

    /**
     * Send due date reminder notifications.
     */
    public function notifyAssignmentDue(int $assignmentId): int
    {
        $assignment = fetch(
            "SELECT a.id, a.title, a.class_id, a.due_date, c.name AS class_name
             FROM assignments a
             JOIN classes c ON c.id = a.class_id
             WHERE a.id = :id AND a.due_date IS NOT NULL",
            [':id' => $assignmentId]
        );
        if (!$assignment) {
            throw new RuntimeException('Assignment not found or has no due date.');
        }

        $students = fetchAll(
            "SELECT e.student_id
             FROM enrollments e
             WHERE e.class_id = :cid AND e.status = 'active'
               AND e.student_id NOT IN (
                   SELECT d.user_id FROM documents d
                   WHERE d.assignment_id = :aid AND d.status IN ('submitted', 'graded')
               )",
            [':cid' => $assignment['class_id'], ':aid' => $assignmentId]
        );

        $count = 0;
        $dueFormatted = date('M j, Y g:i A', strtotime($assignment['due_date']));
        $now = time();
        $dueTimestamp = strtotime($assignment['due_date']);
        $hoursLeft = max(0, round(($dueTimestamp - $now) / 3600));

        $urgencyPrefix = $hoursLeft <= 24 ? '⚠️ ' : '';
        $timeMsg = $hoursLeft <= 1 ? 'less than 1 hour' : ($hoursLeft <= 24 ? "{$hoursLeft} hours" : round($hoursLeft / 24) . ' days');

        foreach ($students as $student) {
            $this->create(
                (int) $student['student_id'],
                'reminder',
                "{$urgencyPrefix}Reminder: {$assignment['title']} due soon",
                "Your assignment \"{$assignment['title']}\" in {$assignment['class_name']} is due in {$timeMsg} ({$dueFormatted}).",
                '/assignments/' . $assignmentId,
                ['assignment_id' => $assignmentId, 'hours_left' => $hoursLeft]
            );
            $count++;
        }

        return $count;
    }

    /**
     * Notify document owner about a new comment.
     */
    public function notifyDocumentComment(int $documentId, int $commenterId): int
    {
        $doc = fetch(
            "SELECT d.id, d.title, d.user_id FROM documents d WHERE d.id = :id",
            [':id' => $documentId]
        );
        if (!$doc) {
            throw new RuntimeException('Document not found.');
        }

        if ((int) $doc['user_id'] === $commenterId) {
            return 0;
        }

        $commenter = fetch(
            "SELECT first_name, last_name FROM users WHERE id = :id",
            [':id' => $commenterId]
        );
        $commenterName = $commenter
            ? trim($commenter['first_name'] . ' ' . $commenter['last_name'])
            : 'Someone';

        $this->create(
            (int) $doc['user_id'],
            'comment',
            'New comment on "' . mb_substr($doc['title'], 0, 50) . '"',
            "{$commenterName} commented on your document.",
            '/documents/' . $documentId,
            ['document_id' => $documentId, 'commenter_id' => $commenterId]
        );

        return 1;
    }

    /**
     * Notify teacher that a submission was received.
     */
    public function notifySubmissionReceived(int $documentId, int $teacherId): int
    {
        $doc = fetch(
            "SELECT d.id, d.title, d.user_id, d.assignment_id,
                    u.first_name, u.last_name,
                    a.title AS assignment_title
             FROM documents d
             JOIN users u ON u.id = d.user_id
             LEFT JOIN assignments a ON a.id = d.assignment_id
             WHERE d.id = :id",
            [':id' => $documentId]
        );
        if (!$doc) {
            throw new RuntimeException('Document not found.');
        }

        $studentName = trim($doc['first_name'] . ' ' . $doc['last_name']);
        $assignmentInfo = $doc['assignment_title'] ? " for \"{$doc['assignment_title']}\"" : '';

        $this->create(
            $teacherId,
            'assignment',
            'New Submission' . $assignmentInfo,
            "{$studentName} submitted \"{$doc['title']}\"{$assignmentInfo}.",
            $doc['assignment_id'] ? '/assignments/' . $doc['assignment_id'] . '/submissions' : '/documents/' . $documentId,
            ['document_id' => $documentId, 'student_id' => $doc['user_id'], 'assignment_id' => $doc['assignment_id']]
        );

        return 1;
    }

    /**
     * Notify student that their grade was posted.
     */
    public function notifyGradePosted(int $documentId, int $studentId): int
    {
        $doc = fetch(
            "SELECT d.id, d.title, d.assignment_id, a.title AS assignment_title
             FROM documents d
             LEFT JOIN assignments a ON a.id = d.assignment_id
             WHERE d.id = :id",
            [':id' => $documentId]
        );
        if (!$doc) {
            throw new RuntimeException('Document not found.');
        }

        $assignmentInfo = $doc['assignment_title'] ? " for \"{$doc['assignment_title']}\"" : '';

        $this->create(
            $studentId,
            'grade',
            'Grade Posted' . $assignmentInfo,
            "Your grade has been posted for \"{$doc['title']}\"{$assignmentInfo}. Check your document for feedback.",
            '/documents/' . $documentId,
            ['document_id' => $documentId, 'assignment_id' => $doc['assignment_id']]
        );

        return 1;
    }

    /**
     * Notify teacher of a policy violation.
     */
    public function notifyPolicyViolation(int $violationId, int $teacherId): int
    {
        $violation = fetch(
            "SELECT pv.id, pv.violation_type, pv.severity, pv.user_id,
                    u.first_name, u.last_name,
                    a.title AS assignment_title
             FROM policy_violations pv
             JOIN users u ON u.id = pv.user_id
             LEFT JOIN assignments a ON a.id = pv.assignment_id
             WHERE pv.id = :id",
            [':id' => $violationId]
        );
        if (!$violation) {
            throw new RuntimeException('Violation not found.');
        }

        $studentName = trim($violation['first_name'] . ' ' . $violation['last_name']);
        $severityLabel = ucfirst($violation['severity']);
        $typeLabel = str_replace('_', ' ', $violation['violation_type']);

        $this->create(
            $teacherId,
            'policy',
            "Policy Violation: {$severityLabel} - {$typeLabel}",
            "{$studentName} triggered a {$severityLabel} severity {$typeLabel} violation"
                . ($violation['assignment_title'] ? " on \"{$violation['assignment_title']}\"" : '')
                . '. Review required.',
            '/violations/' . $violationId,
            ['violation_id' => $violationId, 'student_id' => $violation['user_id'], 'severity' => $violation['severity']]
        );

        return 1;
    }

    /**
     * Notify teacher of a new integrity flag.
     */
    public function notifyFlagCreated(int $flagId, int $teacherId): int
    {
        $flag = fetch(
            "SELECT f.id, f.flag_type, f.severity, f.document_id, f.user_id,
                    u.first_name, u.last_name,
                    d.title AS document_title
             FROM integrity_flags f
             JOIN users u ON u.id = f.user_id
             JOIN documents d ON d.id = f.document_id
             WHERE f.id = :id",
            [':id' => $flagId]
        );
        if (!$flag) {
            throw new RuntimeException('Flag not found.');
        }

        $studentName = trim($flag['first_name'] . ' ' . $flag['last_name']);
        $flagLabel = str_replace('_', ' ', $flag['flag_type']);

        $this->create(
            $teacherId,
            'flag',
            "Integrity Flag: " . ucfirst($flagLabel),
            "A {$flag['severity']} {$flagLabel} flag was raised on \"{$flag['document_title']}\" by {$studentName}.",
            '/documents/' . $flag['document_id'] . '/flags',
            ['flag_id' => $flagId, 'document_id' => $flag['document_id'], 'student_id' => $flag['user_id']]
        );

        return 1;
    }

    /**
     * Notify teacher that a student joined their class.
     */
    public function notifyClassJoined(int $classId, int $studentId): int
    {
        $class = fetch(
            "SELECT c.id, c.name, c.teacher_id FROM classes c WHERE c.id = :id",
            [':id' => $classId]
        );
        if (!$class) {
            throw new RuntimeException('Class not found.');
        }

        $student = fetch(
            "SELECT first_name, last_name FROM users WHERE id = :id",
            [':id' => $studentId]
        );
        $studentName = $student ? trim($student['first_name'] . ' ' . $student['last_name']) : 'A student';

        $studentCount = count_rows('enrollments', "class_id = :cid AND status = 'active'", [':cid' => $classId]);

        $this->create(
            (int) $class['teacher_id'],
            'class',
            "New Student in {$class['name']}",
            "{$studentName} joined your class \"{$class['name']}\". You now have {$studentCount} enrolled students.",
            '/classes/' . $classId . '/students',
            ['class_id' => $classId, 'student_id' => $studentId, 'student_count' => $studentCount]
        );

        return 1;
    }

    /**
     * Get recent notifications for a user.
     */
    public function getRecentNotifications(int $userId, int $limit = 10): array
    {
        $limit = max(1, min($limit, 50));
        return fetchAll(
            "SELECT * FROM notifications
             WHERE user_id = :uid
             ORDER BY created_at DESC
             LIMIT {$limit}",
            [':uid' => $userId]
        );
    }
}
