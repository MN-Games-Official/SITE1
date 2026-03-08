<?php
/**
 * EduWrite AI - Assignment Management Service
 *
 * Handles assignment lifecycle: creation, publishing, submissions,
 * grading, duplication, and AI policy assignment.
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../helpers/database.php';
require_once __DIR__ . '/../helpers/security.php';

class AssignmentService
{
    /**
     * Create a new assignment.
     */
    public function create(int $classId, int $teacherId, array $data): array
    {
        $class = fetch(
            "SELECT id, teacher_id, school_id FROM classes WHERE id = :id AND is_active = 1 AND is_archived = 0",
            [':id' => $classId]
        );
        if (!$class) {
            throw new RuntimeException('Class not found or inactive.');
        }
        if ((int) $class['teacher_id'] !== $teacherId) {
            throw new RuntimeException('You are not the teacher of this class.');
        }

        $title = trim($data['title'] ?? '');
        if (mb_strlen($title) < 2 || mb_strlen($title) > 255) {
            throw new InvalidArgumentException('Assignment title must be between 2 and 255 characters.');
        }

        $validTypes = ['essay', 'research', 'creative', 'reflection', 'analysis', 'other'];
        $assignmentType = $data['assignment_type'] ?? 'essay';
        if (!in_array($assignmentType, $validTypes, true)) {
            throw new InvalidArgumentException('Invalid assignment type.');
        }

        $dueDate = null;
        if (!empty($data['due_date'])) {
            $dueDate = date('Y-m-d H:i:s', strtotime($data['due_date']));
            if ($dueDate === false) {
                throw new InvalidArgumentException('Invalid due date format.');
            }
        }

        $availableFrom = null;
        if (!empty($data['available_from'])) {
            $availableFrom = date('Y-m-d H:i:s', strtotime($data['available_from']));
            if ($availableFrom === false) {
                throw new InvalidArgumentException('Invalid available-from date format.');
            }
        }

        $minWords = isset($data['min_words']) ? (int) $data['min_words'] : null;
        $maxWords = isset($data['max_words']) ? (int) $data['max_words'] : null;
        if ($minWords !== null && $maxWords !== null && $minWords > $maxWords) {
            throw new InvalidArgumentException('Minimum word count cannot exceed maximum.');
        }

        $rubric = null;
        if (!empty($data['rubric'])) {
            if (is_array($data['rubric'])) {
                $rubric = json_encode($data['rubric']);
            } elseif (is_string($data['rubric']) && json_decode($data['rubric']) !== null) {
                $rubric = $data['rubric'];
            }
        }

        $assignmentId = insert('assignments', [
            'class_id'        => $classId,
            'teacher_id'      => $teacherId,
            'title'           => $title,
            'description'     => trim($data['description'] ?? '') ?: null,
            'instructions'    => trim($data['instructions'] ?? '') ?: null,
            'assignment_type' => $assignmentType,
            'due_date'        => $dueDate,
            'available_from'  => $availableFrom,
            'max_words'       => $maxWords,
            'min_words'       => $minWords,
            'rubric'          => $rubric,
            'is_published'    => 0,
            'is_archived'     => 0,
            'allow_late'      => !empty($data['allow_late']) ? 1 : 0,
        ]);

        insert('activity_logs', [
            'user_id'     => $teacherId,
            'action'      => 'assignment_created',
            'entity_type' => 'assignment',
            'entity_id'   => (int) $assignmentId,
            'details'     => json_encode(['title' => $title, 'class_id' => $classId]),
            'ip_address'  => getClientIP(),
        ]);

        return $this->getById((int) $assignmentId);
    }

    /**
     * Update an assignment.
     */
    public function update(int $id, int $teacherId, array $data): array
    {
        $assignment = fetch("SELECT id, teacher_id, class_id FROM assignments WHERE id = :id", [':id' => $id]);
        if (!$assignment) {
            throw new RuntimeException('Assignment not found.');
        }
        if ((int) $assignment['teacher_id'] !== $teacherId) {
            throw new RuntimeException('You are not the teacher of this assignment.');
        }

        $updateData = [];
        if (isset($data['title'])) {
            $title = trim($data['title']);
            if (mb_strlen($title) < 2 || mb_strlen($title) > 255) {
                throw new InvalidArgumentException('Title must be between 2 and 255 characters.');
            }
            $updateData['title'] = $title;
        }
        if (array_key_exists('description', $data)) {
            $updateData['description'] = trim($data['description'] ?? '') ?: null;
        }
        if (array_key_exists('instructions', $data)) {
            $updateData['instructions'] = trim($data['instructions'] ?? '') ?: null;
        }
        if (isset($data['assignment_type'])) {
            $validTypes = ['essay', 'research', 'creative', 'reflection', 'analysis', 'other'];
            if (!in_array($data['assignment_type'], $validTypes, true)) {
                throw new InvalidArgumentException('Invalid assignment type.');
            }
            $updateData['assignment_type'] = $data['assignment_type'];
        }
        if (array_key_exists('due_date', $data)) {
            $updateData['due_date'] = !empty($data['due_date'])
                ? date('Y-m-d H:i:s', strtotime($data['due_date']))
                : null;
        }
        if (array_key_exists('available_from', $data)) {
            $updateData['available_from'] = !empty($data['available_from'])
                ? date('Y-m-d H:i:s', strtotime($data['available_from']))
                : null;
        }
        if (array_key_exists('min_words', $data)) {
            $updateData['min_words'] = $data['min_words'] !== null ? (int) $data['min_words'] : null;
        }
        if (array_key_exists('max_words', $data)) {
            $updateData['max_words'] = $data['max_words'] !== null ? (int) $data['max_words'] : null;
        }
        if (array_key_exists('rubric', $data)) {
            $updateData['rubric'] = is_array($data['rubric']) ? json_encode($data['rubric']) : $data['rubric'];
        }
        if (isset($data['allow_late'])) {
            $updateData['allow_late'] = $data['allow_late'] ? 1 : 0;
        }

        if (empty($updateData)) {
            return $this->getById($id);
        }

        update('assignments', $updateData, 'id = :id', [':id' => $id]);

        insert('activity_logs', [
            'user_id'     => $teacherId,
            'action'      => 'assignment_updated',
            'entity_type' => 'assignment',
            'entity_id'   => $id,
            'details'     => json_encode(['updated_fields' => array_keys($updateData)]),
            'ip_address'  => getClientIP(),
        ]);

        return $this->getById($id);
    }

    /**
     * Get an assignment by ID with class and teacher info.
     */
    public function getById(int $id): ?array
    {
        $assignment = fetch(
            "SELECT a.*,
                    c.name AS class_name, c.subject AS class_subject,
                    u.first_name AS teacher_first_name, u.last_name AS teacher_last_name,
                    (SELECT COUNT(*) FROM documents d WHERE d.assignment_id = a.id) AS total_submissions,
                    (SELECT COUNT(*) FROM documents d2 WHERE d2.assignment_id = a.id AND d2.status = 'submitted') AS pending_submissions,
                    (SELECT COUNT(*) FROM documents d3 WHERE d3.assignment_id = a.id AND d3.status = 'graded') AS graded_submissions
             FROM assignments a
             JOIN classes c ON c.id = a.class_id
             JOIN users u ON u.id = a.teacher_id
             WHERE a.id = :id",
            [':id' => $id]
        );

        if ($assignment && $assignment['rubric']) {
            $decoded = json_decode($assignment['rubric'], true);
            if ($decoded !== null) {
                $assignment['rubric_parsed'] = $decoded;
            }
        }

        return $assignment;
    }

    /**
     * Get paginated assignments for a class.
     */
    public function getByClass(int $classId, array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $sql = "SELECT a.*,
                    (SELECT COUNT(*) FROM documents d WHERE d.assignment_id = a.id) AS total_submissions,
                    (SELECT COUNT(*) FROM documents d2 WHERE d2.assignment_id = a.id AND d2.status = 'graded') AS graded_count
                FROM assignments a
                WHERE a.class_id = :class_id AND a.is_archived = 0";
        $params = [':class_id' => $classId];

        if (isset($filters['is_published'])) {
            $sql .= " AND a.is_published = :is_published";
            $params[':is_published'] = $filters['is_published'] ? 1 : 0;
        }
        if (!empty($filters['assignment_type'])) {
            $sql .= " AND a.assignment_type = :type";
            $params[':type'] = $filters['assignment_type'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (a.title LIKE :search OR a.description LIKE :search_desc)";
            $params[':search'] = '%' . $filters['search'] . '%';
            $params[':search_desc'] = '%' . $filters['search'] . '%';
        }

        $sortField = $filters['sort'] ?? 'due_date';
        $allowedSorts = ['due_date', 'created_at', 'title'];
        if (!in_array($sortField, $allowedSorts, true)) {
            $sortField = 'due_date';
        }
        $sortDir = (isset($filters['direction']) && strtoupper($filters['direction']) === 'DESC') ? 'DESC' : 'ASC';
        $sql .= " ORDER BY a.{$sortField} IS NULL, a.{$sortField} {$sortDir}";

        return paginate($sql, $params, $page, $perPage);
    }

    /**
     * Get assignments for a student across all enrolled classes.
     */
    public function getByStudent(int $studentId, array $filters = []): array
    {
        $sql = "SELECT a.*, c.name AS class_name, c.subject AS class_subject,
                       u.first_name AS teacher_first_name, u.last_name AS teacher_last_name,
                       d.id AS document_id, d.status AS document_status, d.word_count, d.submitted_at
                FROM assignments a
                JOIN classes c ON c.id = a.class_id
                JOIN users u ON u.id = a.teacher_id
                JOIN enrollments e ON e.class_id = c.id AND e.student_id = :student_id AND e.status = 'active'
                LEFT JOIN documents d ON d.assignment_id = a.id AND d.user_id = :student_id2
                WHERE a.is_published = 1 AND a.is_archived = 0 AND c.is_active = 1";
        $params = [':student_id' => $studentId, ':student_id2' => $studentId];

        if (!empty($filters['status'])) {
            switch ($filters['status']) {
                case 'upcoming':
                    $sql .= " AND a.due_date > NOW()";
                    break;
                case 'overdue':
                    $sql .= " AND a.due_date < NOW() AND (d.status IS NULL OR d.status NOT IN ('submitted','graded'))";
                    break;
                case 'submitted':
                    $sql .= " AND d.status = 'submitted'";
                    break;
                case 'graded':
                    $sql .= " AND d.status = 'graded'";
                    break;
                case 'not_started':
                    $sql .= " AND d.id IS NULL";
                    break;
            }
        }
        if (!empty($filters['class_id'])) {
            $sql .= " AND a.class_id = :class_id";
            $params[':class_id'] = $filters['class_id'];
        }

        $sql .= " ORDER BY a.due_date ASC";

        return fetchAll($sql, $params);
    }

    /**
     * Delete (archive) an assignment.
     */
    public function delete(int $id, int $teacherId): bool
    {
        $assignment = fetch(
            "SELECT id, teacher_id, title FROM assignments WHERE id = :id",
            [':id' => $id]
        );
        if (!$assignment) {
            throw new RuntimeException('Assignment not found.');
        }
        if ((int) $assignment['teacher_id'] !== $teacherId) {
            throw new RuntimeException('You are not the teacher of this assignment.');
        }

        update('assignments', ['is_archived' => 1], 'id = :id', [':id' => $id]);

        insert('activity_logs', [
            'user_id'     => $teacherId,
            'action'      => 'assignment_archived',
            'entity_type' => 'assignment',
            'entity_id'   => $id,
            'details'     => json_encode(['title' => $assignment['title']]),
            'ip_address'  => getClientIP(),
        ]);

        return true;
    }

    /**
     * Publish an assignment (make visible to students).
     */
    public function publish(int $id, int $teacherId): array
    {
        $assignment = fetch(
            "SELECT id, teacher_id, is_published FROM assignments WHERE id = :id AND is_archived = 0",
            [':id' => $id]
        );
        if (!$assignment) {
            throw new RuntimeException('Assignment not found.');
        }
        if ((int) $assignment['teacher_id'] !== $teacherId) {
            throw new RuntimeException('You are not the teacher of this assignment.');
        }
        if ($assignment['is_published']) {
            throw new RuntimeException('Assignment is already published.');
        }

        update('assignments', ['is_published' => 1], 'id = :id', [':id' => $id]);

        insert('activity_logs', [
            'user_id'     => $teacherId,
            'action'      => 'assignment_published',
            'entity_type' => 'assignment',
            'entity_id'   => $id,
            'ip_address'  => getClientIP(),
        ]);

        return $this->getById($id);
    }

    /**
     * Unpublish an assignment.
     */
    public function unpublish(int $id, int $teacherId): array
    {
        $assignment = fetch(
            "SELECT id, teacher_id, is_published FROM assignments WHERE id = :id",
            [':id' => $id]
        );
        if (!$assignment) {
            throw new RuntimeException('Assignment not found.');
        }
        if ((int) $assignment['teacher_id'] !== $teacherId) {
            throw new RuntimeException('You are not the teacher of this assignment.');
        }
        if (!$assignment['is_published']) {
            throw new RuntimeException('Assignment is already unpublished.');
        }

        $hasSubmissions = exists('documents', "assignment_id = :aid AND status IN ('submitted','graded')", [':aid' => $id]);
        if ($hasSubmissions) {
            throw new RuntimeException('Cannot unpublish an assignment that has submissions.');
        }

        update('assignments', ['is_published' => 0], 'id = :id', [':id' => $id]);

        return $this->getById($id);
    }

    /**
     * Get submitted documents for an assignment.
     */
    public function getSubmissions(int $assignmentId, int $page = 1, int $perPage = 20): array
    {
        $sql = "SELECT d.*, u.first_name, u.last_name, u.email, u.username,
                       tc.grade, tc.comment AS grade_comment
                FROM documents d
                JOIN users u ON u.id = d.user_id
                LEFT JOIN teacher_comments tc ON tc.document_id = d.id AND tc.comment_type = 'grade'
                WHERE d.assignment_id = :assignment_id
                ORDER BY d.submitted_at DESC, d.updated_at DESC";
        return paginate($sql, [':assignment_id' => $assignmentId], $page, $perPage);
    }

    /**
     * Get submission statistics for an assignment.
     */
    public function getSubmissionStats(int $assignmentId): array
    {
        $assignment = fetch(
            "SELECT a.id, a.class_id, a.due_date
             FROM assignments a WHERE a.id = :id",
            [':id' => $assignmentId]
        );
        if (!$assignment) {
            throw new RuntimeException('Assignment not found.');
        }

        $totalStudents = count_rows('enrollments', "class_id = :cid AND status = 'active'", [':cid' => $assignment['class_id']]);

        $stats = fetch(
            "SELECT COUNT(*) AS total_submissions,
                    SUM(CASE WHEN status = 'draft' OR status = 'in_progress' THEN 1 ELSE 0 END) AS in_progress,
                    SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END) AS submitted,
                    SUM(CASE WHEN status = 'graded' THEN 1 ELSE 0 END) AS graded,
                    SUM(CASE WHEN status = 'returned' THEN 1 ELSE 0 END) AS returned,
                    COALESCE(AVG(word_count), 0) AS avg_word_count,
                    COALESCE(MIN(word_count), 0) AS min_word_count,
                    COALESCE(MAX(word_count), 0) AS max_word_count
             FROM documents WHERE assignment_id = :aid",
            [':aid' => $assignmentId]
        );

        $gradeStats = fetch(
            "SELECT AVG(CAST(tc.grade AS DECIMAL(5,2))) AS avg_grade,
                    MIN(CAST(tc.grade AS DECIMAL(5,2))) AS min_grade,
                    MAX(CAST(tc.grade AS DECIMAL(5,2))) AS max_grade
             FROM teacher_comments tc
             WHERE tc.assignment_id = :aid AND tc.comment_type = 'grade' AND tc.grade REGEXP '^[0-9]+(\\.[0-9]+)?$'",
            [':aid' => $assignmentId]
        );

        $notStarted = $totalStudents - (int) ($stats['total_submissions'] ?? 0);

        return [
            'total_students'    => $totalStudents,
            'total_submissions' => (int) ($stats['total_submissions'] ?? 0),
            'not_started'       => max(0, $notStarted),
            'in_progress'       => (int) ($stats['in_progress'] ?? 0),
            'submitted'         => (int) ($stats['submitted'] ?? 0),
            'graded'            => (int) ($stats['graded'] ?? 0),
            'returned'          => (int) ($stats['returned'] ?? 0),
            'submission_rate'   => $totalStudents > 0
                ? round(((int) ($stats['total_submissions'] ?? 0) / $totalStudents) * 100, 1)
                : 0,
            'avg_word_count'    => round((float) ($stats['avg_word_count'] ?? 0)),
            'min_word_count'    => (int) ($stats['min_word_count'] ?? 0),
            'max_word_count'    => (int) ($stats['max_word_count'] ?? 0),
            'avg_grade'         => $gradeStats['avg_grade'] !== null ? round((float) $gradeStats['avg_grade'], 1) : null,
            'min_grade'         => $gradeStats['min_grade'] !== null ? round((float) $gradeStats['min_grade'], 1) : null,
            'max_grade'         => $gradeStats['max_grade'] !== null ? round((float) $gradeStats['max_grade'], 1) : null,
        ];
    }

    /**
     * Get a student's document for a specific assignment.
     */
    public function getStudentSubmission(int $assignmentId, int $studentId): ?array
    {
        return fetch(
            "SELECT d.*, tc.grade, tc.comment AS grade_comment
             FROM documents d
             LEFT JOIN teacher_comments tc ON tc.document_id = d.id AND tc.comment_type = 'grade'
             WHERE d.assignment_id = :aid AND d.user_id = :uid",
            [':aid' => $assignmentId, ':uid' => $studentId]
        );
    }

    /**
     * Check if an assignment is overdue.
     */
    public function isOverdue(array $assignment): bool
    {
        if (empty($assignment['due_date'])) {
            return false;
        }
        return strtotime($assignment['due_date']) < time();
    }

    /**
     * Get upcoming assignments for a student.
     */
    public function getUpcoming(int $studentId, int $limit = 10): array
    {
        $limit = max(1, min($limit, 50));
        $stmt = db()->prepare(
            "SELECT a.*, c.name AS class_name, c.subject AS class_subject,
                    d.id AS document_id, d.status AS document_status
             FROM assignments a
             JOIN classes c ON c.id = a.class_id
             JOIN enrollments e ON e.class_id = c.id AND e.student_id = :sid AND e.status = 'active'
             LEFT JOIN documents d ON d.assignment_id = a.id AND d.user_id = :sid2
             WHERE a.is_published = 1 AND a.is_archived = 0
               AND a.due_date > NOW() AND c.is_active = 1
             ORDER BY a.due_date ASC
             LIMIT :lim"
        );
        $stmt->bindValue(':sid', $studentId, PDO::PARAM_INT);
        $stmt->bindValue(':sid2', $studentId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get assignments due today for a student.
     */
    public function getDueToday(int $studentId): array
    {
        return fetchAll(
            "SELECT a.*, c.name AS class_name,
                    d.id AS document_id, d.status AS document_status
             FROM assignments a
             JOIN classes c ON c.id = a.class_id
             JOIN enrollments e ON e.class_id = c.id AND e.student_id = :sid AND e.status = 'active'
             LEFT JOIN documents d ON d.assignment_id = a.id AND d.user_id = :sid2
             WHERE a.is_published = 1 AND a.is_archived = 0
               AND DATE(a.due_date) = CURDATE() AND c.is_active = 1
             ORDER BY a.due_date ASC",
            [':sid' => $studentId, ':sid2' => $studentId]
        );
    }

    /**
     * Get overdue assignments for a student.
     */
    public function getOverdue(int $studentId): array
    {
        return fetchAll(
            "SELECT a.*, c.name AS class_name,
                    d.id AS document_id, d.status AS document_status
             FROM assignments a
             JOIN classes c ON c.id = a.class_id
             JOIN enrollments e ON e.class_id = c.id AND e.student_id = :sid AND e.status = 'active'
             LEFT JOIN documents d ON d.assignment_id = a.id AND d.user_id = :sid2
             WHERE a.is_published = 1 AND a.is_archived = 0
               AND a.due_date < NOW() AND c.is_active = 1
               AND (d.status IS NULL OR d.status NOT IN ('submitted','graded'))
             ORDER BY a.due_date ASC",
            [':sid' => $studentId, ':sid2' => $studentId]
        );
    }

    /**
     * Set AI policy for an assignment.
     */
    public function setAIPolicy(int $assignmentId, int $teacherId, int $policyRuleId): array
    {
        $assignment = fetch(
            "SELECT id, teacher_id FROM assignments WHERE id = :id",
            [':id' => $assignmentId]
        );
        if (!$assignment) {
            throw new RuntimeException('Assignment not found.');
        }
        if ((int) $assignment['teacher_id'] !== $teacherId) {
            throw new RuntimeException('You are not the teacher of this assignment.');
        }

        $rule = fetch("SELECT id FROM ai_policy_rules WHERE id = :id AND is_active = 1", [':id' => $policyRuleId]);
        if (!$rule) {
            throw new RuntimeException('Policy rule not found or inactive.');
        }

        update('assignments', ['ai_policy_id' => $policyRuleId], 'id = :id', [':id' => $assignmentId]);

        insert('activity_logs', [
            'user_id'     => $teacherId,
            'action'      => 'assignment_policy_set',
            'entity_type' => 'assignment',
            'entity_id'   => $assignmentId,
            'details'     => json_encode(['policy_rule_id' => $policyRuleId]),
            'ip_address'  => getClientIP(),
        ]);

        return $this->getById($assignmentId);
    }

    /**
     * Get the effective AI policy for an assignment.
     */
    public function getAssignmentPolicy(int $assignmentId): ?array
    {
        $assignment = fetch(
            "SELECT a.id, a.ai_policy_id, a.class_id, c.school_id
             FROM assignments a
             JOIN classes c ON c.id = a.class_id
             WHERE a.id = :id",
            [':id' => $assignmentId]
        );
        if (!$assignment) {
            return null;
        }

        if ($assignment['ai_policy_id']) {
            $rule = fetch(
                "SELECT * FROM ai_policy_rules WHERE id = :id AND is_active = 1",
                [':id' => $assignment['ai_policy_id']]
            );
            if ($rule) {
                return $rule;
            }
        }

        $assignmentRule = fetch(
            "SELECT * FROM ai_policy_rules
             WHERE assignment_id = :aid AND is_active = 1
             ORDER BY priority DESC LIMIT 1",
            [':aid' => $assignmentId]
        );
        if ($assignmentRule) {
            return $assignmentRule;
        }

        $classRule = fetch(
            "SELECT * FROM ai_policy_rules
             WHERE class_id = :cid AND assignment_id IS NULL AND is_active = 1
             ORDER BY priority DESC LIMIT 1",
            [':cid' => $assignment['class_id']]
        );
        if ($classRule) {
            return $classRule;
        }

        $schoolRule = fetch(
            "SELECT * FROM ai_policy_rules
             WHERE school_id = :sid AND class_id IS NULL AND assignment_id IS NULL AND is_active = 1
             ORDER BY priority DESC LIMIT 1",
            [':sid' => $assignment['school_id']]
        );

        return $schoolRule;
    }

    /**
     * Duplicate an assignment to another class.
     */
    public function duplicateAssignment(int $id, int $teacherId, int $targetClassId): array
    {
        $original = fetch("SELECT * FROM assignments WHERE id = :id", [':id' => $id]);
        if (!$original) {
            throw new RuntimeException('Original assignment not found.');
        }
        if ((int) $original['teacher_id'] !== $teacherId) {
            throw new RuntimeException('You are not the teacher of this assignment.');
        }

        $targetClass = fetch(
            "SELECT id, teacher_id FROM classes WHERE id = :id AND is_active = 1 AND is_archived = 0",
            [':id' => $targetClassId]
        );
        if (!$targetClass) {
            throw new RuntimeException('Target class not found or inactive.');
        }
        if ((int) $targetClass['teacher_id'] !== $teacherId) {
            throw new RuntimeException('You are not the teacher of the target class.');
        }

        $newTitle = $original['title'];
        $existingInTarget = exists(
            'assignments',
            "class_id = :cid AND title = :title AND is_archived = 0",
            [':cid' => $targetClassId, ':title' => $newTitle]
        );
        if ($existingInTarget) {
            $newTitle .= ' (Copy)';
        }

        $newId = insert('assignments', [
            'class_id'        => $targetClassId,
            'teacher_id'      => $teacherId,
            'title'           => $newTitle,
            'description'     => $original['description'],
            'instructions'    => $original['instructions'],
            'assignment_type' => $original['assignment_type'],
            'due_date'        => null,
            'available_from'  => null,
            'max_words'       => $original['max_words'],
            'min_words'       => $original['min_words'],
            'rubric'          => $original['rubric'],
            'is_published'    => 0,
            'is_archived'     => 0,
            'allow_late'      => $original['allow_late'],
        ]);

        insert('activity_logs', [
            'user_id'     => $teacherId,
            'action'      => 'assignment_duplicated',
            'entity_type' => 'assignment',
            'entity_id'   => (int) $newId,
            'details'     => json_encode([
                'original_id'     => $id,
                'target_class_id' => $targetClassId,
            ]),
            'ip_address'  => getClientIP(),
        ]);

        return $this->getById((int) $newId);
    }

    /**
     * Export grade data for an assignment.
     */
    public function exportGrades(int $assignmentId): array
    {
        $assignment = fetch(
            "SELECT a.title, c.name AS class_name
             FROM assignments a
             JOIN classes c ON c.id = a.class_id
             WHERE a.id = :id",
            [':id' => $assignmentId]
        );
        if (!$assignment) {
            throw new RuntimeException('Assignment not found.');
        }

        $submissions = fetchAll(
            "SELECT u.first_name, u.last_name, u.email, u.username,
                    d.word_count, d.status AS document_status, d.submitted_at,
                    tc.grade, tc.comment AS grade_comment
             FROM enrollments e
             JOIN users u ON u.id = e.student_id
             LEFT JOIN documents d ON d.assignment_id = :aid AND d.user_id = u.id
             LEFT JOIN teacher_comments tc ON tc.assignment_id = :aid2
                 AND tc.student_id = u.id AND tc.comment_type = 'grade'
             WHERE e.class_id = (SELECT class_id FROM assignments WHERE id = :aid3)
               AND e.status = 'active'
             ORDER BY u.last_name ASC, u.first_name ASC",
            [':aid' => $assignmentId, ':aid2' => $assignmentId, ':aid3' => $assignmentId]
        );

        $headers = ['Last Name', 'First Name', 'Email', 'Username', 'Status', 'Word Count', 'Submitted At', 'Grade', 'Comments'];
        $rows = [];
        foreach ($submissions as $s) {
            $rows[] = [
                $s['last_name'],
                $s['first_name'],
                $s['email'],
                $s['username'],
                $s['document_status'] ?? 'Not Started',
                $s['word_count'] ?? 0,
                $s['submitted_at'] ?? '',
                $s['grade'] ?? '',
                $s['grade_comment'] ?? '',
            ];
        }

        return [
            'assignment_title' => $assignment['title'],
            'class_name'       => $assignment['class_name'],
            'headers'          => $headers,
            'rows'             => $rows,
            'total_students'   => count($submissions),
        ];
    }
}
