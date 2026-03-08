<?php
/**
 * EduWrite AI - Import/Export Service
 *
 * Handles document export (HTML/TXT/MD), student CSV import,
 * grade exports, roster exports, analytics reports, and
 * async job management for large operations.
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../helpers/database.php';
require_once __DIR__ . '/../helpers/security.php';

class ImportExportService
{
    /**
     * Export a document in the specified format.
     */
    public function exportDocument(int $documentId, string $format = 'html'): array
    {
        $doc = fetch(
            "SELECT d.*, u.first_name, u.last_name, u.email,
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

        $validFormats = ['html', 'txt', 'md', 'pdf'];
        if (!in_array($format, $validFormats, true)) {
            throw new InvalidArgumentException('Unsupported format. Use: html, txt, md, or pdf.');
        }

        $title = $doc['title'];
        $content = $doc['content'] ?? '';
        $author = trim($doc['first_name'] . ' ' . $doc['last_name']);
        $date = date('F j, Y', strtotime($doc['created_at']));
        $wordCount = (int) $doc['word_count'];

        switch ($format) {
            case 'html':
                $exported = $this->generateHTML(['document' => $doc], 'document');
                $mimeType = 'text/html';
                $extension = 'html';
                break;

            case 'txt':
                $plainContent = strip_tags($content);
                $plainContent = html_entity_decode($plainContent, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $exported = "{$title}\n"
                    . str_repeat('=', mb_strlen($title)) . "\n\n"
                    . "Author: {$author}\n"
                    . "Date: {$date}\n"
                    . "Word Count: " . number_format($wordCount) . "\n";
                if ($doc['assignment_title']) {
                    $exported .= "Assignment: {$doc['assignment_title']}\n";
                }
                $exported .= "\n" . str_repeat('-', 60) . "\n\n" . $plainContent;
                $mimeType = 'text/plain';
                $extension = 'txt';
                break;

            case 'md':
                $plainContent = strip_tags($content);
                $exported = "# {$title}\n\n"
                    . "**Author:** {$author}  \n"
                    . "**Date:** {$date}  \n"
                    . "**Word Count:** " . number_format($wordCount) . "  \n";
                if ($doc['assignment_title']) {
                    $exported .= "**Assignment:** {$doc['assignment_title']}  \n";
                }
                $exported .= "\n---\n\n" . $plainContent;
                $mimeType = 'text/markdown';
                $extension = 'md';
                break;

            case 'pdf':
                $exported = $this->generateHTML(['document' => $doc], 'document_print');
                $mimeType = 'text/html';
                $extension = 'html';
                break;
        }

        $safeTitle = preg_replace('/[^a-zA-Z0-9_-]/', '_', $title);
        $filename = mb_substr($safeTitle, 0, 100) . '.' . $extension;

        return [
            'filename'  => $filename,
            'content'   => $exported,
            'mime_type' => $mimeType,
            'size'      => strlen($exported),
            'format'    => $format,
        ];
    }

    /**
     * Import students from a CSV file.
     */
    public function importStudents(int $classId, array $file): array
    {
        $class = fetch(
            "SELECT id, school_id FROM classes WHERE id = :id AND is_active = 1",
            [':id' => $classId]
        );
        if (!$class) {
            throw new RuntimeException('Class not found or inactive.');
        }

        $validation = $this->validateCSV($file, ['email']);
        if (!$validation['valid']) {
            throw new InvalidArgumentException('CSV validation failed: ' . implode(', ', $validation['errors']));
        }

        $rows = $this->parseCSV($file);
        $results = [
            'enrolled'         => [],
            'already_enrolled' => [],
            'not_found'        => [],
            'errors'           => [],
            'total_rows'       => count($rows),
        ];

        foreach ($rows as $index => $row) {
            $email = trim(strtolower($row['email'] ?? $row[0] ?? ''));
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $results['errors'][] = ['row' => $index + 2, 'email' => $email, 'reason' => 'Invalid email'];
                continue;
            }

            $student = fetch(
                "SELECT id, school_id FROM users WHERE email = :email AND is_active = 1",
                [':email' => $email]
            );

            if (!$student) {
                $results['not_found'][] = ['row' => $index + 2, 'email' => $email];
                continue;
            }

            if ((int) $student['school_id'] !== (int) $class['school_id']) {
                $results['errors'][] = ['row' => $index + 2, 'email' => $email, 'reason' => 'Different school'];
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
                $results['errors'][] = ['row' => $index + 2, 'email' => $email, 'reason' => 'Database error'];
            }
        }

        $results['summary'] = [
            'total_processed'  => count($rows),
            'enrolled'         => count($results['enrolled']),
            'already_enrolled' => count($results['already_enrolled']),
            'not_found'        => count($results['not_found']),
            'errors'           => count($results['errors']),
        ];

        return $results;
    }

    /**
     * Export class roster.
     */
    public function exportClassRoster(int $classId, string $format = 'csv'): array
    {
        $class = fetch(
            "SELECT c.name, c.code, c.subject, u.first_name AS teacher_first, u.last_name AS teacher_last
             FROM classes c JOIN users u ON u.id = c.teacher_id WHERE c.id = :id",
            [':id' => $classId]
        );
        if (!$class) {
            throw new RuntimeException('Class not found.');
        }

        $students = fetchAll(
            "SELECT u.first_name, u.last_name, u.email, u.username, e.enrolled_at, e.status
             FROM enrollments e
             JOIN users u ON u.id = e.student_id
             WHERE e.class_id = :cid
             ORDER BY u.last_name ASC, u.first_name ASC",
            [':cid' => $classId]
        );

        $headers = ['Last Name', 'First Name', 'Email', 'Username', 'Enrolled At', 'Status'];
        $rows = [];
        foreach ($students as $s) {
            $rows[] = [
                $s['last_name'], $s['first_name'], $s['email'], $s['username'],
                $s['enrolled_at'], $s['status'],
            ];
        }

        if ($format === 'csv') {
            $content = $this->generateCSV($rows, $headers);
            $mimeType = 'text/csv';
            $extension = 'csv';
        } else {
            $content = $this->generateHTML([
                'title'   => "Class Roster: {$class['name']}",
                'headers' => $headers,
                'rows'    => $rows,
                'meta'    => [
                    'Class' => $class['name'],
                    'Code'  => $class['code'],
                    'Subject' => $class['subject'] ?? 'N/A',
                    'Teacher' => trim($class['teacher_first'] . ' ' . $class['teacher_last']),
                    'Total Students' => count($students),
                ],
            ], 'table');
            $mimeType = 'text/html';
            $extension = 'html';
        }

        $safeClassName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $class['name']);
        return [
            'filename'  => "roster_{$safeClassName}.{$extension}",
            'content'   => $content,
            'mime_type' => $mimeType,
            'size'      => strlen($content),
        ];
    }

    /**
     * Export grades for an assignment.
     */
    public function exportGrades(int $assignmentId, string $format = 'csv'): array
    {
        $assignment = fetch(
            "SELECT a.title, a.class_id, c.name AS class_name
             FROM assignments a JOIN classes c ON c.id = a.class_id WHERE a.id = :id",
            [':id' => $assignmentId]
        );
        if (!$assignment) {
            throw new RuntimeException('Assignment not found.');
        }

        $data = fetchAll(
            "SELECT u.last_name, u.first_name, u.email, u.username,
                    d.word_count, d.status AS doc_status, d.submitted_at,
                    tc.grade, tc.comment
             FROM enrollments e
             JOIN users u ON u.id = e.student_id
             LEFT JOIN documents d ON d.assignment_id = :aid AND d.user_id = u.id
             LEFT JOIN teacher_comments tc ON tc.assignment_id = :aid2 AND tc.student_id = u.id AND tc.comment_type = 'grade'
             WHERE e.class_id = :cid AND e.status = 'active'
             ORDER BY u.last_name ASC, u.first_name ASC",
            [':aid' => $assignmentId, ':aid2' => $assignmentId, ':cid' => $assignment['class_id']]
        );

        $headers = ['Last Name', 'First Name', 'Email', 'Username', 'Status', 'Word Count', 'Submitted', 'Grade', 'Comments'];
        $rows = [];
        foreach ($data as $row) {
            $rows[] = [
                $row['last_name'], $row['first_name'], $row['email'], $row['username'],
                $row['doc_status'] ?? 'Not Started',
                $row['word_count'] ?? 0,
                $row['submitted_at'] ?? '',
                $row['grade'] ?? '',
                $row['comment'] ?? '',
            ];
        }

        if ($format === 'csv') {
            $content = $this->generateCSV($rows, $headers);
            $mimeType = 'text/csv';
            $extension = 'csv';
        } else {
            $content = $this->generateHTML([
                'title'   => "Grades: {$assignment['title']}",
                'headers' => $headers,
                'rows'    => $rows,
                'meta'    => [
                    'Assignment' => $assignment['title'],
                    'Class'      => $assignment['class_name'],
                    'Total Students' => count($data),
                ],
            ], 'table');
            $mimeType = 'text/html';
            $extension = 'html';
        }

        $safeTitle = preg_replace('/[^a-zA-Z0-9_-]/', '_', $assignment['title']);
        return [
            'filename'  => "grades_{$safeTitle}.{$extension}",
            'content'   => $content,
            'mime_type' => $mimeType,
            'size'      => strlen($content),
        ];
    }

    /**
     * Export analytics report.
     */
    public function exportAnalyticsReport(array $params, string $format = 'csv'): array
    {
        $reportType = $params['type'] ?? 'class';

        switch ($reportType) {
            case 'class':
                return $this->exportClassAnalyticsReport((int) ($params['class_id'] ?? 0), $params['period'] ?? '30d', $format);
            case 'school':
                return $this->exportSchoolAnalyticsReport((int) ($params['school_id'] ?? 0), $params['period'] ?? '30d', $format);
            default:
                throw new InvalidArgumentException('Unsupported report type: ' . $reportType);
        }
    }

    /**
     * Export audit log.
     */
    public function exportAuditLog(array $filters, string $format = 'csv'): array
    {
        $sql = "SELECT al.*, u.first_name, u.last_name, u.email
                FROM activity_logs al
                JOIN users u ON u.id = al.user_id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['user_id'])) {
            $sql .= " AND al.user_id = :uid";
            $params[':uid'] = $filters['user_id'];
        }
        if (!empty($filters['action'])) {
            $sql .= " AND al.action = :action";
            $params[':action'] = $filters['action'];
        }
        if (!empty($filters['entity_type'])) {
            $sql .= " AND al.entity_type = :etype";
            $params[':etype'] = $filters['entity_type'];
        }
        if (!empty($filters['date_from'])) {
            $sql .= " AND al.created_at >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND al.created_at <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }
        if (!empty($filters['school_id'])) {
            $sql .= " AND u.school_id = :sid";
            $params[':sid'] = $filters['school_id'];
        }

        $sql .= " ORDER BY al.created_at DESC LIMIT 10000";
        $data = fetchAll($sql, $params);

        $headers = ['Date', 'User', 'Email', 'Action', 'Entity Type', 'Entity ID', 'IP Address', 'Details'];
        $rows = [];
        foreach ($data as $log) {
            $rows[] = [
                $log['created_at'],
                trim($log['first_name'] . ' ' . $log['last_name']),
                $log['email'],
                $log['action'],
                $log['entity_type'] ?? '',
                $log['entity_id'] ?? '',
                $log['ip_address'] ?? '',
                $log['details'] ?? '',
            ];
        }

        if ($format === 'csv') {
            $content = $this->generateCSV($rows, $headers);
            $mimeType = 'text/csv';
            $extension = 'csv';
        } else {
            $content = $this->generateHTML([
                'title'   => 'Audit Log Export',
                'headers' => $headers,
                'rows'    => $rows,
                'meta'    => ['Total Records' => count($data), 'Generated' => date('Y-m-d H:i:s')],
            ], 'table');
            $mimeType = 'text/html';
            $extension = 'html';
        }

        return [
            'filename'  => "audit_log_" . date('Y-m-d_His') . ".{$extension}",
            'content'   => $content,
            'mime_type' => $mimeType,
            'size'      => strlen($content),
        ];
    }

    /**
     * Create an async export job.
     */
    public function createExportJob(int $userId, string $type, array $params = []): int
    {
        $validTypes = ['document', 'class_report', 'student_report', 'analytics', 'grades', 'audit_log'];
        if (!in_array($type, $validTypes, true)) {
            throw new InvalidArgumentException('Invalid export type.');
        }

        $jobId = insert('export_jobs', [
            'user_id'     => $userId,
            'export_type' => $type,
            'status'      => 'pending',
            'parameters'  => json_encode($params),
        ]);

        return (int) $jobId;
    }

    /**
     * Process an export job.
     */
    public function processExportJob(int $jobId): array
    {
        $job = fetch("SELECT * FROM export_jobs WHERE id = :id", [':id' => $jobId]);
        if (!$job) {
            throw new RuntimeException('Export job not found.');
        }
        if ($job['status'] !== 'pending') {
            throw new RuntimeException('Job is not in pending status.');
        }

        update('export_jobs', [
            'status'     => 'processing',
            'started_at' => date('Y-m-d H:i:s'),
        ], 'id = :id', [':id' => $jobId]);

        try {
            $params = $job['parameters'] ? json_decode($job['parameters'], true) : [];
            $format = $params['format'] ?? 'csv';

            switch ($job['export_type']) {
                case 'document':
                    $result = $this->exportDocument((int) ($params['document_id'] ?? 0), $format);
                    break;
                case 'grades':
                    $result = $this->exportGrades((int) ($params['assignment_id'] ?? 0), $format);
                    break;
                case 'analytics':
                    $result = $this->exportAnalyticsReport($params, $format);
                    break;
                case 'audit_log':
                    $result = $this->exportAuditLog($params, $format);
                    break;
                case 'class_report':
                    $result = $this->exportClassRoster((int) ($params['class_id'] ?? 0), $format);
                    break;
                default:
                    throw new RuntimeException('Unsupported export type: ' . $job['export_type']);
            }

            $exportPath = defined('EXPORT_PATH') ? EXPORT_PATH : __DIR__ . '/../../storage/exports';
            if (!is_dir($exportPath)) {
                mkdir($exportPath, 0755, true);
            }

            $safeFilename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $result['filename']);
            $uniqueFilename = uniqid() . '_' . $safeFilename;
            $filePath = $exportPath . '/' . $uniqueFilename;

            file_put_contents($filePath, $result['content']);

            update('export_jobs', [
                'status'       => 'completed',
                'file_path'    => $filePath,
                'file_size'    => strlen($result['content']),
                'completed_at' => date('Y-m-d H:i:s'),
            ], 'id = :id', [':id' => $jobId]);

            return [
                'job_id'   => $jobId,
                'status'   => 'completed',
                'filename' => $result['filename'],
                'size'     => strlen($result['content']),
            ];
        } catch (Exception $e) {
            update('export_jobs', [
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at'  => date('Y-m-d H:i:s'),
            ], 'id = :id', [':id' => $jobId]);

            throw $e;
        }
    }

    /**
     * Get export jobs for a user.
     */
    public function getExportJobs(int $userId): array
    {
        return fetchAll(
            "SELECT id, export_type, status, file_path, file_size, error_message, started_at, completed_at, created_at
             FROM export_jobs WHERE user_id = :uid ORDER BY created_at DESC LIMIT 50",
            [':uid' => $userId]
        );
    }

    /**
     * Download a completed export.
     */
    public function downloadExport(int $jobId, int $userId): array
    {
        $job = fetch(
            "SELECT * FROM export_jobs WHERE id = :id AND user_id = :uid",
            [':id' => $jobId, ':uid' => $userId]
        );
        if (!$job) {
            throw new RuntimeException('Export job not found.');
        }
        if ($job['status'] !== 'completed') {
            throw new RuntimeException('Export is not ready for download.');
        }
        if (empty($job['file_path']) || !file_exists($job['file_path'])) {
            throw new RuntimeException('Export file not found on disk.');
        }

        $exportDir = realpath(defined('EXPORT_PATH') ? EXPORT_PATH : __DIR__ . '/../../storage/exports');
        $filePath = realpath($job['file_path']);
        if ($filePath === false || $exportDir === false || strpos($filePath, $exportDir) !== 0) {
            throw new RuntimeException('Invalid file path.');
        }

        $content = file_get_contents($filePath);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $mimeMap = [
            'csv' => 'text/csv', 'html' => 'text/html', 'txt' => 'text/plain',
            'md' => 'text/markdown', 'json' => 'application/json',
        ];

        return [
            'content'   => $content,
            'filename'  => basename($job['file_path']),
            'mime_type' => $mimeMap[$extension] ?? 'application/octet-stream',
            'size'      => strlen($content),
        ];
    }

    /**
     * Create an import job.
     */
    public function createImportJob(int $userId, string $type, array $file): int
    {
        $validTypes = ['students', 'assignments', 'classes', 'documents'];
        if (!in_array($type, $validTypes, true)) {
            throw new InvalidArgumentException('Invalid import type.');
        }

        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new InvalidArgumentException('No valid file uploaded.');
        }

        $storagePath = defined('UPLOAD_STORAGE_PATH') ? UPLOAD_STORAGE_PATH : __DIR__ . '/../../storage/uploads';
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $safeFilename = uniqid('import_') . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($file['name']));
        $destination = $storagePath . '/' . $safeFilename;
        move_uploaded_file($file['tmp_name'], $destination);

        $jobId = insert('import_jobs', [
            'user_id'     => $userId,
            'import_type' => $type,
            'status'      => 'pending',
            'file_path'   => $destination,
        ]);

        return (int) $jobId;
    }

    /**
     * Process an import job.
     */
    public function processImportJob(int $jobId): array
    {
        $job = fetch("SELECT * FROM import_jobs WHERE id = :id", [':id' => $jobId]);
        if (!$job) {
            throw new RuntimeException('Import job not found.');
        }
        if ($job['status'] !== 'pending') {
            throw new RuntimeException('Job is not in pending status.');
        }

        update('import_jobs', [
            'status'     => 'processing',
            'started_at' => date('Y-m-d H:i:s'),
        ], 'id = :id', [':id' => $jobId]);

        try {
            $file = ['tmp_name' => $job['file_path'], 'name' => basename($job['file_path'])];
            $rows = $this->parseCSV($file);

            $totalRows = count($rows);
            $processedRows = 0;
            $errorRows = 0;
            $errors = [];

            switch ($job['import_type']) {
                case 'students':
                    foreach ($rows as $index => $row) {
                        $email = trim($row['email'] ?? $row[0] ?? '');
                        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $errorRows++;
                            $errors[] = ['row' => $index + 2, 'error' => 'Invalid email: ' . $email];
                            continue;
                        }
                        $processedRows++;
                    }
                    break;

                default:
                    $processedRows = $totalRows;
                    break;
            }

            $status = $errorRows > 0 ? ($processedRows > 0 ? 'partial' : 'failed') : 'completed';

            update('import_jobs', [
                'status'         => $status,
                'total_rows'     => $totalRows,
                'processed_rows' => $processedRows,
                'error_rows'     => $errorRows,
                'errors'         => !empty($errors) ? json_encode($errors) : null,
                'completed_at'   => date('Y-m-d H:i:s'),
            ], 'id = :id', [':id' => $jobId]);

            return [
                'job_id'         => $jobId,
                'status'         => $status,
                'total_rows'     => $totalRows,
                'processed_rows' => $processedRows,
                'error_rows'     => $errorRows,
                'errors'         => $errors,
            ];
        } catch (Exception $e) {
            update('import_jobs', [
                'status'       => 'failed',
                'errors'       => json_encode([['error' => $e->getMessage()]]),
                'completed_at' => date('Y-m-d H:i:s'),
            ], 'id = :id', [':id' => $jobId]);

            throw $e;
        }
    }

    /**
     * Get import jobs for a user.
     */
    public function getImportJobs(int $userId): array
    {
        return fetchAll(
            "SELECT id, import_type, status, file_path, total_rows, processed_rows, error_rows,
                    started_at, completed_at, created_at
             FROM import_jobs WHERE user_id = :uid ORDER BY created_at DESC LIMIT 50",
            [':uid' => $userId]
        );
    }

    /**
     * Validate a CSV file.
     */
    public function validateCSV(array $file, array $expectedColumns = []): array
    {
        $errors = [];

        if (empty($file['tmp_name'])) {
            return ['valid' => false, 'errors' => ['No file provided']];
        }

        $filePath = $file['tmp_name'];
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return ['valid' => false, 'errors' => ['File is not readable']];
        }

        $size = filesize($filePath);
        $maxSize = 10 * 1024 * 1024; // 10 MB
        if ($size > $maxSize) {
            $errors[] = 'File too large (max 10 MB)';
        }
        if ($size === 0) {
            $errors[] = 'File is empty';
        }

        if (!empty($errors)) {
            return ['valid' => false, 'errors' => $errors];
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return ['valid' => false, 'errors' => ['Could not open file']];
        }

        $header = fgetcsv($handle);
        fclose($handle);

        if ($header === false || empty($header)) {
            return ['valid' => false, 'errors' => ['Could not read CSV header']];
        }

        $header = array_map(function ($col) {
            return strtolower(trim(preg_replace('/[\x{FEFF}]/u', '', $col)));
        }, $header);

        foreach ($expectedColumns as $col) {
            if (!in_array(strtolower($col), $header, true)) {
                $errors[] = "Missing required column: {$col}";
            }
        }

        return ['valid' => empty($errors), 'errors' => $errors, 'columns' => $header];
    }

    /**
     * Parse a CSV file into an array.
     */
    public function parseCSV(array $file): array
    {
        $filePath = $file['tmp_name'] ?? '';
        if (empty($filePath) || !file_exists($filePath)) {
            throw new RuntimeException('CSV file not found.');
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            throw new RuntimeException('Could not open CSV file.');
        }

        $header = fgetcsv($handle);
        if ($header === false || empty($header)) {
            fclose($handle);
            throw new RuntimeException('CSV file has no header row.');
        }

        $header = array_map(function ($col) {
            return strtolower(trim(preg_replace('/[\x{FEFF}]/u', '', $col)));
        }, $header);

        $rows = [];
        $maxRows = 10000;
        $count = 0;

        while (($row = fgetcsv($handle)) !== false && $count < $maxRows) {
            if (count($row) === 1 && empty(trim($row[0]))) {
                continue;
            }

            $assocRow = [];
            foreach ($header as $i => $col) {
                $assocRow[$col] = $row[$i] ?? '';
            }
            $rows[] = $assocRow;
            $count++;
        }

        fclose($handle);
        return $rows;
    }

    /**
     * Generate a CSV string from data.
     */
    public function generateCSV(array $data, array $headers = []): string
    {
        $handle = fopen('php://temp', 'r+');
        if ($handle === false) {
            throw new RuntimeException('Could not create temporary stream.');
        }

        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

        if (!empty($headers)) {
            fputcsv($handle, $headers);
        }

        foreach ($data as $row) {
            fputcsv($handle, is_array($row) ? array_values($row) : [$row]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return $content;
    }

    /**
     * Generate HTML output for exports.
     */
    public function generateHTML(array $data, string $template = 'table'): string
    {
        $html = "<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n<meta charset=\"UTF-8\">\n";

        switch ($template) {
            case 'document':
            case 'document_print':
                $doc = $data['document'];
                $title = htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8');
                $author = htmlspecialchars(trim(($doc['first_name'] ?? '') . ' ' . ($doc['last_name'] ?? '')), ENT_QUOTES, 'UTF-8');
                $content = nl2br(htmlspecialchars($doc['content'] ?? '', ENT_QUOTES, 'UTF-8'));
                $date = date('F j, Y', strtotime($doc['created_at']));
                $wordCount = number_format((int) ($doc['word_count'] ?? 0));

                $html .= "<title>{$title}</title>\n"
                    . "<style>\n"
                    . "body{font-family:Georgia,'Times New Roman',serif;max-width:800px;margin:2em auto;line-height:1.8;color:#222;padding:0 1.5em;}\n"
                    . "h1{font-size:1.8em;margin-bottom:0.2em;border-bottom:2px solid #ccc;padding-bottom:0.3em;}\n"
                    . ".meta{color:#666;font-size:0.9em;margin-bottom:2em;}\n"
                    . ".content{text-align:justify;}\n"
                    . ($template === 'document_print' ? "@media print{body{margin:0;max-width:100%;font-size:12pt;}}\n" : '')
                    . "</style>\n</head>\n<body>\n"
                    . "<h1>{$title}</h1>\n"
                    . "<div class=\"meta\">\n"
                    . "<p>Author: {$author} | Date: {$date} | Words: {$wordCount}</p>\n";
                if (!empty($doc['assignment_title'])) {
                    $html .= "<p>Assignment: " . htmlspecialchars($doc['assignment_title'], ENT_QUOTES, 'UTF-8') . "</p>\n";
                }
                $html .= "</div>\n"
                    . "<div class=\"content\">{$content}</div>\n"
                    . "</body>\n</html>";
                break;

            case 'table':
            default:
                $title = htmlspecialchars($data['title'] ?? 'Export', ENT_QUOTES, 'UTF-8');
                $html .= "<title>{$title}</title>\n"
                    . "<style>\n"
                    . "body{font-family:Arial,Helvetica,sans-serif;margin:2em;color:#333;}\n"
                    . "h1{font-size:1.5em;color:#1a1a1a;}\n"
                    . ".meta{background:#f5f5f5;padding:1em;border-radius:4px;margin-bottom:1.5em;font-size:0.9em;}\n"
                    . ".meta span{margin-right:2em;}\n"
                    . "table{border-collapse:collapse;width:100%;margin-top:1em;}\n"
                    . "th{background:#2563eb;color:#fff;padding:10px 12px;text-align:left;font-weight:600;}\n"
                    . "td{padding:8px 12px;border-bottom:1px solid #e5e7eb;}\n"
                    . "tr:nth-child(even){background:#f9fafb;}\n"
                    . "tr:hover{background:#eff6ff;}\n"
                    . ".footer{margin-top:2em;font-size:0.8em;color:#999;}\n"
                    . "</style>\n</head>\n<body>\n"
                    . "<h1>{$title}</h1>\n";

                if (!empty($data['meta'])) {
                    $html .= "<div class=\"meta\">\n";
                    foreach ($data['meta'] as $key => $val) {
                        $html .= "<span><strong>" . htmlspecialchars($key, ENT_QUOTES, 'UTF-8')
                            . ":</strong> " . htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') . "</span>\n";
                    }
                    $html .= "</div>\n";
                }

                $html .= "<table>\n<thead>\n<tr>\n";
                foreach (($data['headers'] ?? []) as $header) {
                    $html .= "<th>" . htmlspecialchars($header, ENT_QUOTES, 'UTF-8') . "</th>\n";
                }
                $html .= "</tr>\n</thead>\n<tbody>\n";

                foreach (($data['rows'] ?? []) as $row) {
                    $html .= "<tr>\n";
                    foreach ($row as $cell) {
                        $html .= "<td>" . htmlspecialchars((string) $cell, ENT_QUOTES, 'UTF-8') . "</td>\n";
                    }
                    $html .= "</tr>\n";
                }

                $html .= "</tbody>\n</table>\n"
                    . "<div class=\"footer\">Generated on " . date('F j, Y g:i A') . " by EduWrite AI</div>\n"
                    . "</body>\n</html>";
                break;
        }

        return $html;
    }

    /**
     * Export class analytics report.
     */
    private function exportClassAnalyticsReport(int $classId, string $period, string $format): array
    {
        $class = fetch("SELECT name FROM classes WHERE id = :id", [':id' => $classId]);
        if (!$class) {
            throw new RuntimeException('Class not found.');
        }

        $students = fetchAll(
            "SELECT u.first_name, u.last_name, u.email,
                    COUNT(d.id) AS doc_count,
                    COALESCE(SUM(d.word_count), 0) AS total_words,
                    SUM(CASE WHEN d.status IN ('submitted','graded') THEN 1 ELSE 0 END) AS completed,
                    (SELECT COUNT(*) FROM ai_requests ar WHERE ar.user_id = u.id
                        AND ar.document_id IN (SELECT d2.id FROM documents d2 WHERE d2.assignment_id IN (SELECT id FROM assignments WHERE class_id = :cid2))) AS ai_requests
             FROM enrollments e
             JOIN users u ON u.id = e.student_id
             LEFT JOIN documents d ON d.user_id = u.id
                 AND d.assignment_id IN (SELECT id FROM assignments WHERE class_id = :cid)
             WHERE e.class_id = :cid3 AND e.status = 'active'
             GROUP BY u.id, u.first_name, u.last_name, u.email
             ORDER BY u.last_name ASC",
            [':cid' => $classId, ':cid2' => $classId, ':cid3' => $classId]
        );

        $headers = ['Last Name', 'First Name', 'Email', 'Documents', 'Total Words', 'Completed', 'AI Requests'];
        $rows = [];
        foreach ($students as $s) {
            $rows[] = [
                $s['last_name'], $s['first_name'], $s['email'],
                $s['doc_count'], $s['total_words'], $s['completed'], $s['ai_requests'],
            ];
        }

        if ($format === 'csv') {
            $content = $this->generateCSV($rows, $headers);
            $mimeType = 'text/csv';
            $extension = 'csv';
        } else {
            $content = $this->generateHTML([
                'title'   => "Analytics: {$class['name']}",
                'headers' => $headers,
                'rows'    => $rows,
                'meta'    => ['Class' => $class['name'], 'Period' => $period, 'Students' => count($students)],
            ], 'table');
            $mimeType = 'text/html';
            $extension = 'html';
        }

        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $class['name']);
        return [
            'filename'  => "analytics_{$safeName}_{$period}.{$extension}",
            'content'   => $content,
            'mime_type' => $mimeType,
            'size'      => strlen($content),
        ];
    }

    /**
     * Export school analytics report.
     */
    private function exportSchoolAnalyticsReport(int $schoolId, string $period, string $format): array
    {
        $school = fetch("SELECT name FROM schools WHERE id = :id", [':id' => $schoolId]);
        if (!$school) {
            throw new RuntimeException('School not found.');
        }

        $classes = fetchAll(
            "SELECT c.name,
                    u.first_name AS teacher_first, u.last_name AS teacher_last,
                    (SELECT COUNT(*) FROM enrollments e WHERE e.class_id = c.id AND e.status = 'active') AS students,
                    (SELECT COUNT(*) FROM assignments a WHERE a.class_id = c.id AND a.is_archived = 0) AS assignments,
                    (SELECT COUNT(*) FROM documents d WHERE d.assignment_id IN (SELECT id FROM assignments WHERE class_id = c.id)) AS documents
             FROM classes c
             JOIN users u ON u.id = c.teacher_id
             WHERE c.school_id = :sid AND c.is_active = 1 AND c.is_archived = 0
             ORDER BY c.name ASC",
            [':sid' => $schoolId]
        );

        $headers = ['Class Name', 'Teacher', 'Students', 'Assignments', 'Documents'];
        $rows = [];
        foreach ($classes as $c) {
            $rows[] = [
                $c['name'],
                trim($c['teacher_first'] . ' ' . $c['teacher_last']),
                $c['students'], $c['assignments'], $c['documents'],
            ];
        }

        if ($format === 'csv') {
            $content = $this->generateCSV($rows, $headers);
            $mimeType = 'text/csv';
            $extension = 'csv';
        } else {
            $content = $this->generateHTML([
                'title'   => "School Analytics: {$school['name']}",
                'headers' => $headers,
                'rows'    => $rows,
                'meta'    => ['School' => $school['name'], 'Period' => $period, 'Classes' => count($classes)],
            ], 'table');
            $mimeType = 'text/html';
            $extension = 'html';
        }

        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $school['name']);
        return [
            'filename'  => "school_analytics_{$safeName}_{$period}.{$extension}",
            'content'   => $content,
            'mime_type' => $mimeType,
            'size'      => strlen($content),
        ];
    }
}
