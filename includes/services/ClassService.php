<?php
/**
 * EduWrite AI - Class Management Service
 *
 * Handles class lifecycle: creation, enrollment, student management,
 * class statistics, and activity tracking.
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../helpers/database.php';
require_once __DIR__ . '/../helpers/security.php';

class ClassService
{
    /**
     * Create a new class.
     */
    public function create(int $teacherId, array $data): array
    {
        $teacher = fetch(
            "SELECT id, school_id, role FROM users WHERE id = :id AND is_active = 1",
            [':id' => $teacherId]
        );
        if (!$teacher) {
            throw new RuntimeException('Teacher not found.');
        }
        if (!in_array($teacher['role'], ['teacher', 'admin', 'super_admin'], true)) {
            throw new RuntimeException('Only teachers can create classes.');
        }

        $name = trim($data['name'] ?? '');
        if (mb_strlen($name) < 2 || mb_strlen($name) > 255) {
            throw new InvalidArgumentException('Class name must be between 2 and 255 characters.');
        }

        $code = $this->generateJoinCode();

        $classId = insert('classes', [
            'school_id'     => $teacher['school_id'],
            'teacher_id'    => $teacherId,
            'name'          => $name,
            'description'   => trim($data['description'] ?? '') ?: null,
            'code'          => $code,
            'subject'       => trim($data['subject'] ?? '') ?: null,
            'grade_level'   => trim($data['grade_level'] ?? '') ?: null,
            'academic_year' => trim($data['academic_year'] ?? '') ?: null,
            'semester'      => trim($data['semester'] ?? '') ?: null,
            'is_active'     => 1,
            'is_archived'   => 0,
        ]);

        insert('activity_logs', [
            'user_id'     => $teacherId,
            'action'      => 'class_created',
            'entity_type' => 'class',
            'entity_id'   => (int) $classId,
            'details'     => json_encode(['name' => $name, 'code' => $code]),
            'ip_address'  => getClientIP(),
        ]);

        return $this->getById((int) $classId);
    }

    /**
     * Update class information.
     */
    public function update(int $id, int $teacherId, array $data): array
    {
        $class = fetch("SELECT id, teacher_id FROM classes WHERE id = :id", [':id' => $id]);
        if (!$class) {
            throw new RuntimeException('Class not found.');
        }
        if ((int) $class['teacher_id'] !== $teacherId) {
            throw new RuntimeException('You are not the teacher of this class.');
        }

        $updateData = [];
        if (isset($data['name'])) {
            $name = trim($data['name']);
            if (mb_strlen($name) < 2 || mb_strlen($name) > 255) {
                throw new InvalidArgumentException('Class name must be between 2 and 255 characters.');
            }
            $updateData['name'] = $name;
        }
        if (array_key_exists('description', $data)) {
            $updateData['description'] = trim($data['description'] ?? '') ?: null;
        }
        if (isset($data['subject'])) {
            $updateData['subject'] = trim($data['subject']) ?: null;
        }
        if (isset($data['grade_level'])) {
            $updateData['grade_level'] = trim($data['grade_level']) ?: null;
        }
        if (isset($data['academic_year'])) {
            $updateData['academic_year'] = trim($data['academic_year']) ?: null;
        }
        if (isset($data['semester'])) {
            $updateData['semester'] = trim($data['semester']) ?: null;
        }
        if (isset($data['is_active'])) {
            $updateData['is_active'] = $data['is_active'] ? 1 : 0;
        }

        if (empty($updateData)) {
            return $this->getById($id);
        }

        update('classes', $updateData, 'id = :id', [':id' => $id]);

        insert('activity_logs', [
            'user_id'     => $teacherId,
            'action'      => 'class_updated',
            'entity_type' => 'class',
            'entity_id'   => $id,
            'details'     => json_encode(['updated_fields' => array_keys($updateData)]),
            'ip_address'  => getClientIP(),
        ]);

        return $this->getById($id);
    }

    /**
     * Get a class by ID with teacher info.
     */
    public function getById(int $id): ?array
    {
        $class = fetch(
            "SELECT c.*, u.first_name AS teacher_first_name, u.last_name AS teacher_last_name,
                    u.email AS teacher_email,
                    (SELECT COUNT(*) FROM enrollments e WHERE e.class_id = c.id AND e.status = 'active') AS student_count,
                    (SELECT COUNT(*) FROM assignments a WHERE a.class_id = c.id AND a.is_archived = 0) AS assignment_count
             FROM classes c
             JOIN users u ON u.id = c.teacher_id
             WHERE c.id = :id",
            [':id' => $id]
        );

        return $class;
    }

    /**
     * Get paginated classes for a teacher.
     */
    public function getByTeacher(int $teacherId, array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $sql = "SELECT c.*,
                    (SELECT COUNT(*) FROM enrollments e WHERE e.class_id = c.id AND e.status = 'active') AS student_count,
                    (SELECT COUNT(*) FROM assignments a WHERE a.class_id = c.id AND a.is_archived = 0) AS assignment_count
                FROM classes c WHERE c.teacher_id = :teacher_id";
        $params = [':teacher_id' => $teacherId];

        if (isset($filters['is_archived'])) {
            $sql .= " AND c.is_archived = :is_archived";
            $params[':is_archived'] = $filters['is_archived'] ? 1 : 0;
        } else {
            $sql .= " AND c.is_archived = 0";
        }
        if (isset($filters['is_active'])) {
            $sql .= " AND c.is_active = :is_active";
            $params[':is_active'] = $filters['is_active'] ? 1 : 0;
        }
        if (!empty($filters['subject'])) {
            $sql .= " AND c.subject = :subject";
            $params[':subject'] = $filters['subject'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (c.name LIKE :search OR c.description LIKE :search_desc)";
            $params[':search'] = '%' . $filters['search'] . '%';
            $params[':search_desc'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY c.created_at DESC";

        return paginate($sql, $params, $page, $perPage);
    }

    /**
     * Get all active classes for a student.
     */
    public function getByStudent(int $studentId): array
    {
        return fetchAll(
            "SELECT c.*, e.enrolled_at, e.status AS enrollment_status,
                    u.first_name AS teacher_first_name, u.last_name AS teacher_last_name,
                    (SELECT COUNT(*) FROM enrollments e2 WHERE e2.class_id = c.id AND e2.status = 'active') AS student_count,
                    (SELECT COUNT(*) FROM assignments a WHERE a.class_id = c.id AND a.is_published = 1 AND a.is_archived = 0) AS assignment_count
             FROM enrollments e
             JOIN classes c ON c.id = e.class_id
             JOIN users u ON u.id = c.teacher_id
             WHERE e.student_id = :student_id AND e.status = 'active' AND c.is_active = 1
             ORDER BY c.name ASC",
            [':student_id' => $studentId]
        );
    }

    /**
     * Delete (archive) a class.
     */
    public function delete(int $id, int $teacherId): bool
    {
        $class = fetch("SELECT id, teacher_id, name FROM classes WHERE id = :id", [':id' => $id]);
        if (!$class) {
            throw new RuntimeException('Class not found.');
        }
        if ((int) $class['teacher_id'] !== $teacherId) {
            throw new RuntimeException('You are not the teacher of this class.');
        }

        update('classes', ['is_archived' => 1, 'is_active' => 0], 'id = :id', [':id' => $id]);

        insert('activity_logs', [
            'user_id'     => $teacherId,
            'action'      => 'class_archived',
            'entity_type' => 'class',
            'entity_id'   => $id,
            'details'     => json_encode(['name' => $class['name']]),
            'ip_address'  => getClientIP(),
        ]);

        return true;
    }

    /**
     * Generate a unique 6-character alphanumeric join code.
     */
    public function generateJoinCode(): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $maxAttempts = 20;

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $code = '';
            for ($i = 0; $i < 6; $i++) {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }

            if (!exists('classes', 'code = :code', [':code' => $code])) {
                return $code;
            }
        }

        throw new RuntimeException('Unable to generate a unique join code. Please try again.');
    }

    /**
     * Student joins a class via join code.
     */
    public function joinClass(int $studentId, string $code): array
    {
        $code = strtoupper(trim($code));
        if (strlen($code) !== 6) {
            throw new InvalidArgumentException('Join code must be 6 characters.');
        }

        $student = fetch(
            "SELECT id, school_id, role FROM users WHERE id = :id AND is_active = 1",
            [':id' => $studentId]
        );
        if (!$student) {
            throw new RuntimeException('Student not found.');
        }

        $class = fetch(
            "SELECT id, school_id, name FROM classes WHERE code = :code AND is_active = 1 AND is_archived = 0",
            [':code' => $code]
        );
        if (!$class) {
            throw new InvalidArgumentException('Invalid or inactive class code.');
        }

        if ((int) $student['school_id'] !== (int) $class['school_id']) {
            throw new RuntimeException('You can only join classes in your school.');
        }

        $existing = fetch(
            "SELECT id, status FROM enrollments WHERE class_id = :class_id AND student_id = :student_id",
            [':class_id' => $class['id'], ':student_id' => $studentId]
        );

        if ($existing) {
            if ($existing['status'] === 'active') {
                throw new RuntimeException('You are already enrolled in this class.');
            }
            update('enrollments', [
                'status'      => 'active',
                'enrolled_at' => date('Y-m-d H:i:s'),
                'dropped_at'  => null,
            ], 'id = :id', [':id' => $existing['id']]);
        } else {
            insert('enrollments', [
                'class_id'   => $class['id'],
                'student_id' => $studentId,
                'status'     => 'active',
            ]);
        }

        insert('activity_logs', [
            'user_id'     => $studentId,
            'action'      => 'class_joined',
            'entity_type' => 'class',
            'entity_id'   => $class['id'],
            'details'     => json_encode(['class_name' => $class['name'], 'code' => $code]),
            'ip_address'  => getClientIP(),
        ]);

        return $this->getById((int) $class['id']);
    }

    /**
     * Student leaves a class.
     */
    public function leaveClass(int $studentId, int $classId): bool
    {
        $enrollment = fetch(
            "SELECT id, status FROM enrollments WHERE class_id = :class_id AND student_id = :student_id",
            [':class_id' => $classId, ':student_id' => $studentId]
        );
        if (!$enrollment) {
            throw new RuntimeException('You are not enrolled in this class.');
        }
        if ($enrollment['status'] !== 'active') {
            throw new RuntimeException('Your enrollment is not active.');
        }

        update('enrollments', [
            'status'     => 'dropped',
            'dropped_at' => date('Y-m-d H:i:s'),
        ], 'id = :id', [':id' => $enrollment['id']]);

        insert('activity_logs', [
            'user_id'     => $studentId,
            'action'      => 'class_left',
            'entity_type' => 'class',
            'entity_id'   => $classId,
            'ip_address'  => getClientIP(),
        ]);

        return true;
    }

    /**
     * Teacher enrolls a student in a class.
     */
    public function enrollStudent(int $classId, int $studentId): bool
    {
        $class = fetch("SELECT id, school_id FROM classes WHERE id = :id", [':id' => $classId]);
        if (!$class) {
            throw new RuntimeException('Class not found.');
        }

        $student = fetch(
            "SELECT id, school_id FROM users WHERE id = :id AND is_active = 1",
            [':id' => $studentId]
        );
        if (!$student) {
            throw new RuntimeException('Student not found.');
        }
        if ((int) $student['school_id'] !== (int) $class['school_id']) {
            throw new RuntimeException('Student must belong to the same school.');
        }

        $existing = fetch(
            "SELECT id, status FROM enrollments WHERE class_id = :class_id AND student_id = :student_id",
            [':class_id' => $classId, ':student_id' => $studentId]
        );

        if ($existing) {
            if ($existing['status'] === 'active') {
                throw new RuntimeException('Student is already enrolled.');
            }
            update('enrollments', [
                'status'      => 'active',
                'enrolled_at' => date('Y-m-d H:i:s'),
                'dropped_at'  => null,
            ], 'id = :id', [':id' => $existing['id']]);
        } else {
            insert('enrollments', [
                'class_id'   => $classId,
                'student_id' => $studentId,
                'status'     => 'active',
            ]);
        }

        return true;
    }

    /**
     * Teacher removes a student from a class.
     */
    public function removeStudent(int $classId, int $studentId): bool
    {
        $enrollment = fetch(
            "SELECT id FROM enrollments WHERE class_id = :class_id AND student_id = :student_id AND status = 'active'",
            [':class_id' => $classId, ':student_id' => $studentId]
        );
        if (!$enrollment) {
            throw new RuntimeException('Student is not actively enrolled in this class.');
        }

        update('enrollments', [
            'status'     => 'dropped',
            'dropped_at' => date('Y-m-d H:i:s'),
        ], 'id = :id', [':id' => $enrollment['id']]);

        insert('activity_logs', [
            'user_id'     => $studentId,
            'action'      => 'student_removed',
            'entity_type' => 'class',
            'entity_id'   => $classId,
            'ip_address'  => getClientIP(),
        ]);

        return true;
    }

    /**
     * Get paginated enrolled students with their stats.
     */
    public function getStudents(int $classId, int $page = 1, int $perPage = 20): array
    {
        $sql = "SELECT u.id, u.first_name, u.last_name, u.email, u.username, u.avatar_url,
                       e.enrolled_at, e.status AS enrollment_status,
                       (SELECT COUNT(*) FROM documents d
                        WHERE d.user_id = u.id AND d.assignment_id IN
                            (SELECT a.id FROM assignments a WHERE a.class_id = :class_id_sub)) AS document_count,
                       (SELECT COALESCE(SUM(d2.word_count), 0) FROM documents d2
                        WHERE d2.user_id = u.id AND d2.assignment_id IN
                            (SELECT a2.id FROM assignments a2 WHERE a2.class_id = :class_id_sub2)) AS total_words
                FROM enrollments e
                JOIN users u ON u.id = e.student_id
                WHERE e.class_id = :class_id AND e.status = 'active'
                ORDER BY u.last_name ASC, u.first_name ASC";
        return paginate($sql, [
            ':class_id'      => $classId,
            ':class_id_sub'  => $classId,
            ':class_id_sub2' => $classId,
        ], $page, $perPage);
    }

    /**
     * Get class-level statistics.
     */
    public function getClassStats(int $classId): array
    {
        $studentCount = count_rows('enrollments', "class_id = :cid AND status = 'active'", [':cid' => $classId]);

        $assignmentStats = fetch(
            "SELECT COUNT(*) AS total_assignments,
                    SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) AS published,
                    SUM(CASE WHEN due_date IS NOT NULL AND due_date < NOW() THEN 1 ELSE 0 END) AS past_due
             FROM assignments WHERE class_id = :class_id AND is_archived = 0",
            [':class_id' => $classId]
        );

        $docStats = fetch(
            "SELECT COUNT(*) AS total_documents,
                    COALESCE(SUM(d.word_count), 0) AS total_words,
                    COALESCE(AVG(d.word_count), 0) AS avg_word_count,
                    SUM(CASE WHEN d.status = 'submitted' THEN 1 ELSE 0 END) AS submitted,
                    SUM(CASE WHEN d.status = 'graded' THEN 1 ELSE 0 END) AS graded
             FROM documents d
             WHERE d.assignment_id IN (SELECT a.id FROM assignments a WHERE a.class_id = :class_id)",
            [':class_id' => $classId]
        );

        $avgGrade = fetch(
            "SELECT AVG(CAST(tc.grade AS DECIMAL(5,2))) AS avg_grade
             FROM teacher_comments tc
             JOIN assignments a ON a.id = tc.assignment_id
             WHERE a.class_id = :class_id AND tc.comment_type = 'grade' AND tc.grade REGEXP '^[0-9]+(\\.[0-9]+)?$'",
            [':class_id' => $classId]
        );

        $recentActivity = fetch(
            "SELECT COUNT(*) AS active_this_week
             FROM documents d
             WHERE d.assignment_id IN (SELECT a.id FROM assignments a WHERE a.class_id = :class_id)
               AND d.updated_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)",
            [':class_id' => $classId]
        );

        return [
            'student_count'     => $studentCount,
            'total_assignments' => (int) ($assignmentStats['total_assignments'] ?? 0),
            'published_assignments' => (int) ($assignmentStats['published'] ?? 0),
            'past_due_assignments'  => (int) ($assignmentStats['past_due'] ?? 0),
            'total_documents'   => (int) ($docStats['total_documents'] ?? 0),
            'total_words'       => (int) ($docStats['total_words'] ?? 0),
            'avg_word_count'    => round((float) ($docStats['avg_word_count'] ?? 0)),
            'submitted_docs'    => (int) ($docStats['submitted'] ?? 0),
            'graded_docs'       => (int) ($docStats['graded'] ?? 0),
            'avg_grade'         => $avgGrade['avg_grade'] !== null ? round((float) $avgGrade['avg_grade'], 1) : null,
            'active_this_week'  => (int) ($recentActivity['active_this_week'] ?? 0),
        ];
    }

    /**
     * Archive a class.
     */
    public function archiveClass(int $classId, int $teacherId): bool
    {
        return $this->delete($classId, $teacherId);
    }

    /**
     * Get recent activity in a class.
     */
    public function getClassActivity(int $classId, int $limit = 20): array
    {
        $limit = max(1, min($limit, 100));

        $stmt = db()->prepare(
            "SELECT al.*, u.first_name, u.last_name
             FROM activity_logs al
             JOIN users u ON u.id = al.user_id
             WHERE (al.entity_type = 'class' AND al.entity_id = :class_id)
                OR (al.entity_type = 'document' AND al.entity_id IN (
                    SELECT d.id FROM documents d
                    WHERE d.assignment_id IN (SELECT a.id FROM assignments a WHERE a.class_id = :class_id2)
                ))
                OR (al.entity_type = 'assignment' AND al.entity_id IN (
                    SELECT a2.id FROM assignments a2 WHERE a2.class_id = :class_id3
                ))
             ORDER BY al.created_at DESC
             LIMIT :lim"
        );
        $stmt->bindValue(':class_id', $classId, PDO::PARAM_INT);
        $stmt->bindValue(':class_id2', $classId, PDO::PARAM_INT);
        $stmt->bindValue(':class_id3', $classId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get assignments for a class.
     */
    public function getClassAssignments(int $classId, array $filters = []): array
    {
        $sql = "SELECT a.*,
                    (SELECT COUNT(*) FROM documents d WHERE d.assignment_id = a.id) AS submission_count,
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

        $sql .= " ORDER BY a.due_date ASC, a.created_at DESC";

        return fetchAll($sql, $params);
    }

    /**
     * Search teacher's classes.
     */
    public function searchClasses(int $teacherId, string $query): array
    {
        if (mb_strlen(trim($query)) < 2) {
            throw new InvalidArgumentException('Search query must be at least 2 characters.');
        }

        $searchTerm = '%' . trim($query) . '%';
        return fetchAll(
            "SELECT c.*,
                    (SELECT COUNT(*) FROM enrollments e WHERE e.class_id = c.id AND e.status = 'active') AS student_count
             FROM classes c
             WHERE c.teacher_id = :teacher_id AND c.is_archived = 0
               AND (c.name LIKE :search_name OR c.description LIKE :search_desc
                    OR c.subject LIKE :search_subj OR c.code LIKE :search_code)
             ORDER BY c.name ASC
             LIMIT 50",
            [
                ':teacher_id'  => $teacherId,
                ':search_name' => $searchTerm,
                ':search_desc' => $searchTerm,
                ':search_subj' => $searchTerm,
                ':search_code' => $searchTerm,
            ]
        );
    }

    /**
     * Get an individual student's progress in a class.
     */
    public function getStudentProgress(int $classId, int $studentId): array
    {
        $enrollment = fetch(
            "SELECT id, status, enrolled_at FROM enrollments WHERE class_id = :cid AND student_id = :sid",
            [':cid' => $classId, ':sid' => $studentId]
        );
        if (!$enrollment) {
            throw new RuntimeException('Student is not enrolled in this class.');
        }

        $assignments = fetchAll(
            "SELECT a.id, a.title, a.due_date, a.assignment_type, a.min_words, a.max_words,
                    d.id AS document_id, d.status AS document_status, d.word_count, d.submitted_at, d.updated_at,
                    tc.grade, tc.comment AS grade_comment
             FROM assignments a
             LEFT JOIN documents d ON d.assignment_id = a.id AND d.user_id = :student_id
             LEFT JOIN teacher_comments tc ON tc.assignment_id = a.id AND tc.student_id = :student_id2
                 AND tc.comment_type = 'grade'
             WHERE a.class_id = :class_id AND a.is_archived = 0 AND a.is_published = 1
             ORDER BY a.due_date ASC",
            [':student_id' => $studentId, ':student_id2' => $studentId, ':class_id' => $classId]
        );

        $totalAssignments = count($assignments);
        $completed = 0;
        $graded = 0;
        $totalWords = 0;
        $grades = [];

        foreach ($assignments as $a) {
            if (in_array($a['document_status'], ['submitted', 'graded'], true)) {
                $completed++;
            }
            if ($a['document_status'] === 'graded') {
                $graded++;
            }
            $totalWords += (int) ($a['word_count'] ?? 0);
            if ($a['grade'] !== null && is_numeric($a['grade'])) {
                $grades[] = (float) $a['grade'];
            }
        }

        $avgGrade = !empty($grades) ? round(array_sum($grades) / count($grades), 1) : null;

        $aiUsage = fetch(
            "SELECT COUNT(*) AS total_requests
             FROM ai_requests ar
             WHERE ar.user_id = :uid
               AND ar.document_id IN (
                   SELECT d.id FROM documents d WHERE d.assignment_id IN
                       (SELECT a.id FROM assignments a WHERE a.class_id = :cid)
               )",
            [':uid' => $studentId, ':cid' => $classId]
        );

        return [
            'enrollment'        => $enrollment,
            'assignments'       => $assignments,
            'total_assignments' => $totalAssignments,
            'completed'         => $completed,
            'graded'            => $graded,
            'completion_rate'   => $totalAssignments > 0 ? round(($completed / $totalAssignments) * 100, 1) : 0,
            'total_words'       => $totalWords,
            'avg_grade'         => $avgGrade,
            'ai_requests'       => (int) ($aiUsage['total_requests'] ?? 0),
        ];
    }

    /**
     * Bulk-enroll students by email addresses.
     */
    public function bulkEnrollStudents(int $classId, array $studentEmails): array
    {
        $class = fetch("SELECT id, school_id FROM classes WHERE id = :id AND is_active = 1", [':id' => $classId]);
        if (!$class) {
            throw new RuntimeException('Class not found or inactive.');
        }

        $results = ['enrolled' => [], 'already_enrolled' => [], 'not_found' => [], 'errors' => []];

        foreach ($studentEmails as $email) {
            $email = trim(strtolower($email));
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $results['errors'][] = ['email' => $email, 'reason' => 'Invalid email format'];
                continue;
            }

            $student = fetch(
                "SELECT id, school_id FROM users WHERE email = :email AND is_active = 1",
                [':email' => $email]
            );
            if (!$student) {
                $results['not_found'][] = $email;
                continue;
            }
            if ((int) $student['school_id'] !== (int) $class['school_id']) {
                $results['errors'][] = ['email' => $email, 'reason' => 'Student belongs to a different school'];
                continue;
            }

            $existing = fetch(
                "SELECT id, status FROM enrollments WHERE class_id = :cid AND student_id = :sid",
                [':cid' => $classId, ':sid' => $student['id']]
            );

            if ($existing && $existing['status'] === 'active') {
                $results['already_enrolled'][] = $email;
                continue;
            }

            try {
                if ($existing) {
                    update('enrollments', [
                        'status'      => 'active',
                        'enrolled_at' => date('Y-m-d H:i:s'),
                        'dropped_at'  => null,
                    ], 'id = :id', [':id' => $existing['id']]);
                } else {
                    insert('enrollments', [
                        'class_id'   => $classId,
                        'student_id' => $student['id'],
                        'status'     => 'active',
                    ]);
                }
                $results['enrolled'][] = $email;
            } catch (Exception $e) {
                $results['errors'][] = ['email' => $email, 'reason' => 'Enrollment failed'];
            }
        }

        $results['summary'] = [
            'total_processed'  => count($studentEmails),
            'enrolled'         => count($results['enrolled']),
            'already_enrolled' => count($results['already_enrolled']),
            'not_found'        => count($results['not_found']),
            'errors'           => count($results['errors']),
        ];

        return $results;
    }
}
