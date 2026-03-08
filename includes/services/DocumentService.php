<?php
/**
 * EduWrite AI - Document Management Service
 *
 * Handles document lifecycle: creation, editing, versioning, sharing,
 * submission, grading, and export.
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../helpers/database.php';
require_once __DIR__ . '/../helpers/security.php';

class DocumentService
{
    /**
     * Create a new document.
     */
    public function create(int $userId, array $data): array
    {
        $title = trim($data['title'] ?? 'Untitled Document');
        $content = $data['content'] ?? '';
        $assignmentId = $data['assignment_id'] ?? null;

        if (mb_strlen($title) < 1 || mb_strlen($title) > 255) {
            throw new InvalidArgumentException('Document title must be between 1 and 255 characters.');
        }

        if ($assignmentId !== null) {
            $assignment = fetch(
                "SELECT id, class_id FROM assignments WHERE id = :id AND is_archived = 0",
                [':id' => $assignmentId]
            );
            if (!$assignment) {
                throw new InvalidArgumentException('Assignment not found.');
            }
            $enrolled = exists('enrollments', "class_id = :class_id AND student_id = :student_id AND status = 'active'", [
                ':class_id' => $assignment['class_id'],
                ':student_id' => $userId,
            ]);
            if (!$enrolled) {
                throw new RuntimeException('You are not enrolled in this class.');
            }
            $existingDoc = fetch(
                "SELECT id FROM documents WHERE user_id = :uid AND assignment_id = :aid",
                [':uid' => $userId, ':aid' => $assignmentId]
            );
            if ($existingDoc) {
                throw new RuntimeException('You already have a document for this assignment.');
            }
        }

        $wordCount = $this->getWordCount($content);

        $docId = insert('documents', [
            'user_id'         => $userId,
            'assignment_id'   => $assignmentId,
            'title'           => $title,
            'content'         => $content,
            'word_count'      => $wordCount,
            'character_count' => mb_strlen($content),
            'status'          => 'draft',
        ]);

        $this->saveVersion((int) $docId, $userId, 'manual', 'Initial creation');

        insert('activity_logs', [
            'user_id'     => $userId,
            'action'      => 'document_created',
            'entity_type' => 'document',
            'entity_id'   => (int) $docId,
            'details'     => json_encode(['title' => $title, 'assignment_id' => $assignmentId]),
            'ip_address'  => getClientIP(),
        ]);

        return $this->getById((int) $docId);
    }

    /**
     * Update a document's content and/or metadata.
     */
    public function update(int $id, array $data): array
    {
        $doc = $this->getById($id);
        if (!$doc) {
            throw new RuntimeException('Document not found.');
        }
        if (in_array($doc['status'], ['submitted', 'graded'], true)) {
            throw new RuntimeException('Cannot edit a submitted or graded document.');
        }

        $updateData = [];
        if (isset($data['title'])) {
            $title = trim($data['title']);
            if (mb_strlen($title) < 1 || mb_strlen($title) > 255) {
                throw new InvalidArgumentException('Title must be between 1 and 255 characters.');
            }
            $updateData['title'] = $title;
        }
        if (array_key_exists('content', $data)) {
            $updateData['content'] = $data['content'];
            $updateData['word_count'] = $this->getWordCount($data['content']);
            $updateData['character_count'] = mb_strlen($data['content'] ?? '');
        }
        if (isset($data['status']) && in_array($data['status'], ['draft', 'in_progress'], true)) {
            $updateData['status'] = $data['status'];
        }

        if (empty($updateData)) {
            return $doc;
        }

        if (isset($updateData['content']) && $doc['status'] === 'draft') {
            $updateData['status'] = 'in_progress';
        }

        update('documents', $updateData, 'id = :id', [':id' => $id]);

        return $this->getById($id);
    }

    /**
     * Get a document by ID with user info.
     */
    public function getById(int $id): ?array
    {
        return fetch(
            "SELECT d.*, u.first_name, u.last_name, u.email, u.username,
                    a.title AS assignment_title, a.due_date AS assignment_due_date
             FROM documents d
             JOIN users u ON u.id = d.user_id
             LEFT JOIN assignments a ON a.id = d.assignment_id
             WHERE d.id = :id",
            [':id' => $id]
        );
    }

    /**
     * Get paginated documents for a user with filters.
     */
    public function getByUser(int $userId, array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $sql = "SELECT d.*, a.title AS assignment_title, a.due_date AS assignment_due_date
                FROM documents d
                LEFT JOIN assignments a ON a.id = d.assignment_id
                WHERE d.user_id = :user_id";
        $params = [':user_id' => $userId];

        if (!empty($filters['status'])) {
            $sql .= " AND d.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['assignment_id'])) {
            $sql .= " AND d.assignment_id = :assignment_id";
            $params[':assignment_id'] = $filters['assignment_id'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (d.title LIKE :search OR d.content LIKE :search_content)";
            $params[':search'] = '%' . $filters['search'] . '%';
            $params[':search_content'] = '%' . $filters['search'] . '%';
        }

        $sortField = $filters['sort'] ?? 'updated_at';
        $allowedSorts = ['created_at', 'updated_at', 'title', 'word_count', 'status'];
        if (!in_array($sortField, $allowedSorts, true)) {
            $sortField = 'updated_at';
        }
        $sortDir = (isset($filters['direction']) && strtoupper($filters['direction']) === 'ASC') ? 'ASC' : 'DESC';
        $sql .= " ORDER BY d.{$sortField} {$sortDir}";

        return paginate($sql, $params, $page, $perPage);
    }

    /**
     * Get documents for an assignment.
     */
    public function getByAssignment(int $assignmentId, int $page = 1, int $perPage = 20): array
    {
        $sql = "SELECT d.*, u.first_name, u.last_name, u.email, u.username
                FROM documents d
                JOIN users u ON u.id = d.user_id
                WHERE d.assignment_id = :assignment_id
                ORDER BY d.submitted_at DESC, d.updated_at DESC";
        return paginate($sql, [':assignment_id' => $assignmentId], $page, $perPage);
    }

    /**
     * Soft-delete a document with ownership check.
     */
    public function delete(int $id, int $userId): bool
    {
        $doc = $this->getById($id);
        if (!$doc) {
            throw new RuntimeException('Document not found.');
        }
        if ((int) $doc['user_id'] !== $userId) {
            throw new RuntimeException('You do not own this document.');
        }
        if (in_array($doc['status'], ['submitted', 'graded'], true)) {
            throw new RuntimeException('Cannot delete a submitted or graded document.');
        }

        delete('documents', 'id = :id', [':id' => $id]);

        insert('activity_logs', [
            'user_id'     => $userId,
            'action'      => 'document_deleted',
            'entity_type' => 'document',
            'entity_id'   => $id,
            'details'     => json_encode(['title' => $doc['title']]),
            'ip_address'  => getClientIP(),
        ]);

        return true;
    }

    /**
     * Auto-save a document, creating an autosave version.
     */
    public function autosave(int $id, int $userId, string $content): array
    {
        $doc = fetch("SELECT id, user_id, status, content FROM documents WHERE id = :id", [':id' => $id]);
        if (!$doc) {
            throw new RuntimeException('Document not found.');
        }
        if ((int) $doc['user_id'] !== $userId) {
            throw new RuntimeException('You do not own this document.');
        }
        if (in_array($doc['status'], ['submitted', 'graded'], true)) {
            throw new RuntimeException('Cannot edit a submitted or graded document.');
        }

        if ($doc['content'] === $content) {
            return $this->getById($id);
        }

        $wordCount = $this->getWordCount($content);

        $updateData = [
            'content'          => $content,
            'word_count'       => $wordCount,
            'character_count'  => mb_strlen($content),
            'last_autosave_at' => date('Y-m-d H:i:s'),
        ];
        if ($doc['status'] === 'draft') {
            $updateData['status'] = 'in_progress';
        }

        update('documents', $updateData, 'id = :id', [':id' => $id]);

        $lastAutosave = fetch(
            "SELECT created_at FROM document_versions
             WHERE document_id = :doc_id AND snapshot_type = 'autosave'
             ORDER BY created_at DESC LIMIT 1",
            [':doc_id' => $id]
        );

        $shouldSaveVersion = true;
        if ($lastAutosave) {
            $elapsed = time() - strtotime($lastAutosave['created_at']);
            if ($elapsed < 120) {
                $shouldSaveVersion = false;
            }
        }

        if ($shouldSaveVersion) {
            $this->saveVersion($id, $userId, 'autosave', null);
        }

        return $this->getById($id);
    }

    /**
     * Create a version snapshot of a document.
     */
    public function saveVersion(int $documentId, int $userId, string $snapshotType = 'manual', ?string $changeSummary = null): int
    {
        $validTypes = ['autosave', 'manual', 'submission', 'revert'];
        if (!in_array($snapshotType, $validTypes, true)) {
            throw new InvalidArgumentException('Invalid snapshot type.');
        }

        $doc = fetch("SELECT id, content, word_count FROM documents WHERE id = :id", [':id' => $documentId]);
        if (!$doc) {
            throw new RuntimeException('Document not found.');
        }

        $lastVersion = fetch(
            "SELECT MAX(version_number) AS max_ver FROM document_versions WHERE document_id = :doc_id",
            [':doc_id' => $documentId]
        );
        $nextVersion = ($lastVersion && $lastVersion['max_ver'] !== null)
            ? (int) $lastVersion['max_ver'] + 1
            : 1;

        $versionId = insert('document_versions', [
            'document_id'    => $documentId,
            'user_id'        => $userId,
            'version_number' => $nextVersion,
            'content'        => $doc['content'],
            'word_count'     => $doc['word_count'],
            'change_summary' => $changeSummary,
            'snapshot_type'  => $snapshotType,
        ]);

        return (int) $versionId;
    }

    /**
     * Get paginated version history for a document.
     */
    public function getVersions(int $documentId, int $page = 1, int $perPage = 20): array
    {
        $sql = "SELECT dv.*, u.first_name, u.last_name
                FROM document_versions dv
                JOIN users u ON u.id = dv.user_id
                WHERE dv.document_id = :document_id
                ORDER BY dv.version_number DESC";
        return paginate($sql, [':document_id' => $documentId], $page, $perPage);
    }

    /**
     * Get a specific version.
     */
    public function getVersion(int $versionId): ?array
    {
        return fetch(
            "SELECT dv.*, u.first_name, u.last_name
             FROM document_versions dv
             JOIN users u ON u.id = dv.user_id
             WHERE dv.id = :id",
            [':id' => $versionId]
        );
    }

    /**
     * Restore a document to a previous version.
     */
    public function restoreVersion(int $documentId, int $versionId, int $userId): array
    {
        $doc = fetch("SELECT id, user_id, status FROM documents WHERE id = :id", [':id' => $documentId]);
        if (!$doc) {
            throw new RuntimeException('Document not found.');
        }
        if ((int) $doc['user_id'] !== $userId) {
            throw new RuntimeException('You do not own this document.');
        }
        if (in_array($doc['status'], ['submitted', 'graded'], true)) {
            throw new RuntimeException('Cannot restore a submitted or graded document.');
        }

        $version = fetch(
            "SELECT id, content, word_count, version_number FROM document_versions WHERE id = :id AND document_id = :doc_id",
            [':id' => $versionId, ':doc_id' => $documentId]
        );
        if (!$version) {
            throw new RuntimeException('Version not found for this document.');
        }

        $this->saveVersion($documentId, $userId, 'revert', 'Before restoring to version ' . $version['version_number']);

        update('documents', [
            'content'         => $version['content'],
            'word_count'      => $version['word_count'],
            'character_count' => mb_strlen($version['content'] ?? ''),
        ], 'id = :id', [':id' => $documentId]);

        $this->saveVersion($documentId, $userId, 'manual', 'Restored from version ' . $version['version_number']);

        insert('activity_logs', [
            'user_id'     => $userId,
            'action'      => 'document_version_restored',
            'entity_type' => 'document',
            'entity_id'   => $documentId,
            'details'     => json_encode(['restored_version' => $version['version_number']]),
            'ip_address'  => getClientIP(),
        ]);

        return $this->getById($documentId);
    }

    /**
     * Submit a document for review/grading.
     */
    public function submit(int $id, int $userId): array
    {
        $doc = fetch("SELECT id, user_id, status, assignment_id, content FROM documents WHERE id = :id", [':id' => $id]);
        if (!$doc) {
            throw new RuntimeException('Document not found.');
        }
        if ((int) $doc['user_id'] !== $userId) {
            throw new RuntimeException('You do not own this document.');
        }
        if ($doc['status'] === 'submitted' || $doc['status'] === 'graded') {
            throw new RuntimeException('Document has already been submitted.');
        }

        if ($doc['assignment_id']) {
            $assignment = fetch("SELECT id, min_words, max_words FROM assignments WHERE id = :id", [':id' => $doc['assignment_id']]);
            if ($assignment) {
                $wordCount = $this->getWordCount($doc['content']);
                if ($assignment['min_words'] && $wordCount < (int) $assignment['min_words']) {
                    throw new RuntimeException("Document does not meet minimum word count of {$assignment['min_words']} words (current: {$wordCount}).");
                }
                if ($assignment['max_words'] && $wordCount > (int) $assignment['max_words']) {
                    throw new RuntimeException("Document exceeds maximum word count of {$assignment['max_words']} words (current: {$wordCount}).");
                }
            }
        }

        update('documents', [
            'status'       => 'submitted',
            'submitted_at' => date('Y-m-d H:i:s'),
        ], 'id = :id', [':id' => $id]);

        $this->saveVersion($id, $userId, 'submission', 'Submitted for review');

        insert('activity_logs', [
            'user_id'     => $userId,
            'action'      => 'document_submitted',
            'entity_type' => 'document',
            'entity_id'   => $id,
            'details'     => json_encode(['assignment_id' => $doc['assignment_id']]),
            'ip_address'  => getClientIP(),
        ]);

        return $this->getById($id);
    }

    /**
     * Return a document for revision (teacher action).
     */
    public function returnDocument(int $id, int $teacherId, string $notes = ''): array
    {
        $doc = $this->getById($id);
        if (!$doc) {
            throw new RuntimeException('Document not found.');
        }
        if ($doc['status'] !== 'submitted') {
            throw new RuntimeException('Only submitted documents can be returned.');
        }

        if ($doc['assignment_id']) {
            $assignment = fetch("SELECT teacher_id FROM assignments WHERE id = :id", [':id' => $doc['assignment_id']]);
            if (!$assignment || (int) $assignment['teacher_id'] !== $teacherId) {
                throw new RuntimeException('You are not the teacher for this assignment.');
            }
        }

        update('documents', ['status' => 'returned'], 'id = :id', [':id' => $id]);

        if (!empty(trim($notes)) && $doc['assignment_id']) {
            insert('teacher_comments', [
                'assignment_id' => $doc['assignment_id'],
                'student_id'    => $doc['user_id'],
                'document_id'   => $id,
                'teacher_id'    => $teacherId,
                'comment'       => trim($notes),
                'comment_type'  => 'feedback',
            ]);
        }

        insert('activity_logs', [
            'user_id'     => $teacherId,
            'action'      => 'document_returned',
            'entity_type' => 'document',
            'entity_id'   => $id,
            'details'     => json_encode(['student_id' => $doc['user_id'], 'notes' => $notes]),
            'ip_address'  => getClientIP(),
        ]);

        return $this->getById($id);
    }

    /**
     * Grade a submitted document.
     */
    public function gradeDocument(int $id, int $teacherId, array $grade): array
    {
        $doc = $this->getById($id);
        if (!$doc) {
            throw new RuntimeException('Document not found.');
        }
        if (!in_array($doc['status'], ['submitted', 'graded'], true)) {
            throw new RuntimeException('Only submitted documents can be graded.');
        }
        if (!$doc['assignment_id']) {
            throw new RuntimeException('Document has no associated assignment.');
        }

        $assignment = fetch("SELECT id, teacher_id FROM assignments WHERE id = :id", [':id' => $doc['assignment_id']]);
        if (!$assignment || (int) $assignment['teacher_id'] !== $teacherId) {
            throw new RuntimeException('You are not the teacher for this assignment.');
        }

        $gradeValue = trim($grade['grade'] ?? '');
        $comment = trim($grade['comment'] ?? '');

        if (empty($gradeValue)) {
            throw new InvalidArgumentException('Grade value is required.');
        }

        update('documents', ['status' => 'graded'], 'id = :id', [':id' => $id]);

        insert('teacher_comments', [
            'assignment_id' => $doc['assignment_id'],
            'student_id'    => $doc['user_id'],
            'document_id'   => $id,
            'teacher_id'    => $teacherId,
            'comment'       => !empty($comment) ? $comment : 'Grade assigned: ' . $gradeValue,
            'comment_type'  => 'grade',
            'grade'         => $gradeValue,
        ]);

        insert('activity_logs', [
            'user_id'     => $teacherId,
            'action'      => 'document_graded',
            'entity_type' => 'document',
            'entity_id'   => $id,
            'details'     => json_encode([
                'student_id' => $doc['user_id'],
                'grade'      => $gradeValue,
            ]),
            'ip_address'  => getClientIP(),
        ]);

        return $this->getById($id);
    }

    /**
     * Calculate word count from content.
     */
    public function getWordCount(string $content): int
    {
        if (empty(trim($content))) {
            return 0;
        }
        $text = strip_tags($content);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $text);
        $text = preg_replace('/\s+/', ' ', trim($text));
        if (empty($text)) {
            return 0;
        }
        return count(preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY));
    }

    /**
     * Recalculate and store the word count for a document.
     */
    public function updateWordCount(int $id): int
    {
        $doc = fetch("SELECT id, content FROM documents WHERE id = :id", [':id' => $id]);
        if (!$doc) {
            throw new RuntimeException('Document not found.');
        }

        $wordCount = $this->getWordCount($doc['content'] ?? '');
        $charCount = mb_strlen($doc['content'] ?? '');

        update('documents', [
            'word_count'      => $wordCount,
            'character_count' => $charCount,
        ], 'id = :id', [':id' => $id]);

        return $wordCount;
    }

    /**
     * Share a document with another user.
     */
    public function shareDocument(int $documentId, int $userId, int $sharedWithUserId, string $permission = 'view'): array
    {
        $validPermissions = ['view', 'comment', 'edit'];
        if (!in_array($permission, $validPermissions, true)) {
            throw new InvalidArgumentException('Invalid permission. Must be: view, comment, or edit.');
        }

        $doc = fetch("SELECT id, user_id FROM documents WHERE id = :id", [':id' => $documentId]);
        if (!$doc) {
            throw new RuntimeException('Document not found.');
        }
        if ((int) $doc['user_id'] !== $userId) {
            throw new RuntimeException('You do not own this document.');
        }
        if ($userId === $sharedWithUserId) {
            throw new InvalidArgumentException('Cannot share a document with yourself.');
        }

        $targetUser = fetch("SELECT id FROM users WHERE id = :id AND is_active = 1", [':id' => $sharedWithUserId]);
        if (!$targetUser) {
            throw new RuntimeException('Target user not found.');
        }

        $existingShare = fetch(
            "SELECT id FROM document_shares WHERE document_id = :doc_id AND shared_with_user_id = :uid",
            [':doc_id' => $documentId, ':uid' => $sharedWithUserId]
        );

        if ($existingShare) {
            update('document_shares', ['permission' => $permission], 'id = :id', [':id' => $existingShare['id']]);
            $shareId = $existingShare['id'];
        } else {
            $shareId = insert('document_shares', [
                'document_id'        => $documentId,
                'shared_with_user_id' => $sharedWithUserId,
                'shared_by_user_id'  => $userId,
                'permission'         => $permission,
            ]);

            update('documents', ['is_shared' => 1], 'id = :id', [':id' => $documentId]);
        }

        insert('activity_logs', [
            'user_id'     => $userId,
            'action'      => 'document_shared',
            'entity_type' => 'document',
            'entity_id'   => $documentId,
            'details'     => json_encode([
                'shared_with' => $sharedWithUserId,
                'permission'  => $permission,
            ]),
            'ip_address'  => getClientIP(),
        ]);

        return fetch(
            "SELECT ds.*, u.first_name, u.last_name, u.email
             FROM document_shares ds
             JOIN users u ON u.id = ds.shared_with_user_id
             WHERE ds.id = :id",
            [':id' => $shareId]
        );
    }

    /**
     * Revoke a document share.
     */
    public function revokeShare(int $shareId, int $userId): bool
    {
        $share = fetch(
            "SELECT ds.id, ds.document_id, d.user_id
             FROM document_shares ds
             JOIN documents d ON d.id = ds.document_id
             WHERE ds.id = :id",
            [':id' => $shareId]
        );
        if (!$share) {
            throw new RuntimeException('Share not found.');
        }
        if ((int) $share['user_id'] !== $userId) {
            throw new RuntimeException('You do not own this document.');
        }

        delete('document_shares', 'id = :id', [':id' => $shareId]);

        $remainingShares = count_rows('document_shares', 'document_id = :doc_id', [':doc_id' => $share['document_id']]);
        if ($remainingShares === 0) {
            update('documents', ['is_shared' => 0], 'id = :id', [':id' => $share['document_id']]);
        }

        return true;
    }

    /**
     * Get documents shared with a user.
     */
    public function getSharedDocuments(int $userId): array
    {
        return fetchAll(
            "SELECT d.id, d.title, d.status, d.word_count, d.updated_at,
                    ds.permission, ds.created_at AS shared_at,
                    owner.first_name AS owner_first_name, owner.last_name AS owner_last_name
             FROM document_shares ds
             JOIN documents d ON d.id = ds.document_id
             JOIN users owner ON owner.id = d.user_id
             WHERE ds.shared_with_user_id = :user_id
             ORDER BY ds.created_at DESC",
            [':user_id' => $userId]
        );
    }

    /**
     * Get writing statistics for a user.
     */
    public function getDocumentStats(int $userId): array
    {
        $totals = fetch(
            "SELECT COUNT(*) AS total_documents,
                    COALESCE(SUM(word_count), 0) AS total_words,
                    COALESCE(AVG(word_count), 0) AS avg_word_count,
                    COALESCE(MAX(word_count), 0) AS max_word_count,
                    SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) AS drafts,
                    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) AS in_progress,
                    SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END) AS submitted,
                    SUM(CASE WHEN status = 'graded' THEN 1 ELSE 0 END) AS graded,
                    SUM(CASE WHEN status = 'returned' THEN 1 ELSE 0 END) AS returned
             FROM documents WHERE user_id = :user_id",
            [':user_id' => $userId]
        );

        $recentActivity = fetch(
            "SELECT COUNT(*) AS docs_this_week,
                    COALESCE(SUM(word_count), 0) AS words_this_week
             FROM documents
             WHERE user_id = :user_id AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)",
            [':user_id' => $userId]
        );

        $versionCount = fetch(
            "SELECT COUNT(*) AS total_versions
             FROM document_versions dv
             JOIN documents d ON d.id = dv.document_id
             WHERE d.user_id = :user_id",
            [':user_id' => $userId]
        );

        return [
            'total_documents'  => (int) ($totals['total_documents'] ?? 0),
            'total_words'      => (int) ($totals['total_words'] ?? 0),
            'avg_word_count'   => round((float) ($totals['avg_word_count'] ?? 0)),
            'max_word_count'   => (int) ($totals['max_word_count'] ?? 0),
            'by_status'        => [
                'draft'       => (int) ($totals['drafts'] ?? 0),
                'in_progress' => (int) ($totals['in_progress'] ?? 0),
                'submitted'   => (int) ($totals['submitted'] ?? 0),
                'graded'      => (int) ($totals['graded'] ?? 0),
                'returned'    => (int) ($totals['returned'] ?? 0),
            ],
            'docs_this_week'   => (int) ($recentActivity['docs_this_week'] ?? 0),
            'words_this_week'  => (int) ($recentActivity['words_this_week'] ?? 0),
            'total_versions'   => (int) ($versionCount['total_versions'] ?? 0),
        ];
    }

    /**
     * Search user's documents.
     */
    public function searchDocuments(int $userId, string $query, array $filters = []): array
    {
        if (mb_strlen(trim($query)) < 2) {
            throw new InvalidArgumentException('Search query must be at least 2 characters.');
        }

        $searchTerm = '%' . trim($query) . '%';
        $sql = "SELECT d.id, d.title, d.status, d.word_count, d.updated_at, d.created_at,
                       a.title AS assignment_title
                FROM documents d
                LEFT JOIN assignments a ON a.id = d.assignment_id
                WHERE d.user_id = :user_id
                  AND (d.title LIKE :search_title OR d.content LIKE :search_content)";
        $params = [
            ':user_id'        => $userId,
            ':search_title'   => $searchTerm,
            ':search_content' => $searchTerm,
        ];

        if (!empty($filters['status'])) {
            $sql .= " AND d.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['assignment_id'])) {
            $sql .= " AND d.assignment_id = :assignment_id";
            $params[':assignment_id'] = $filters['assignment_id'];
        }

        $sql .= " ORDER BY d.updated_at DESC LIMIT 50";

        return fetchAll($sql, $params);
    }

    /**
     * Export a document in the specified format.
     */
    public function exportDocument(int $id, string $format = 'html'): array
    {
        $doc = $this->getById($id);
        if (!$doc) {
            throw new RuntimeException('Document not found.');
        }

        $validFormats = ['html', 'text', 'markdown'];
        if (!in_array($format, $validFormats, true)) {
            throw new InvalidArgumentException('Unsupported export format. Use: html, text, or markdown.');
        }

        $title = $doc['title'];
        $content = $doc['content'] ?? '';

        switch ($format) {
            case 'html':
                $exported = "<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n<meta charset=\"UTF-8\">\n"
                    . "<title>" . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . "</title>\n"
                    . "<style>body{font-family:Georgia,serif;max-width:800px;margin:2em auto;line-height:1.6;color:#333;padding:0 1em;}"
                    . "h1{border-bottom:2px solid #ddd;padding-bottom:.3em;}</style>\n"
                    . "</head>\n<body>\n"
                    . "<h1>" . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . "</h1>\n"
                    . "<p><em>Word count: " . number_format($doc['word_count']) . "</em></p>\n"
                    . "<div class=\"content\">" . nl2br(htmlspecialchars($content, ENT_QUOTES, 'UTF-8')) . "</div>\n"
                    . "</body>\n</html>";
                $mimeType = 'text/html';
                $extension = 'html';
                break;

            case 'text':
                $exported = $title . "\n" . str_repeat('=', mb_strlen($title)) . "\n\n"
                    . "Word count: " . number_format($doc['word_count']) . "\n\n"
                    . strip_tags($content);
                $mimeType = 'text/plain';
                $extension = 'txt';
                break;

            case 'markdown':
                $exported = "# " . $title . "\n\n"
                    . "*Word count: " . number_format($doc['word_count']) . "*\n\n"
                    . "---\n\n"
                    . strip_tags($content);
                $mimeType = 'text/markdown';
                $extension = 'md';
                break;
        }

        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $title) . '.' . $extension;

        return [
            'filename'  => $filename,
            'content'   => $exported,
            'mime_type' => $mimeType,
            'size'      => strlen($exported),
        ];
    }
}
