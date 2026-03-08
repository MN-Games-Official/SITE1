<?php
/**
 * EduWrite AI - Input Validation Helper Functions
 * Data validation, sanitization, CSRF protection, and file upload validation.
 */

require_once __DIR__ . '/../../config/app.php';

/**
 * Validate data against a set of rules.
 *
 * @param array $data   Associative array of field => value
 * @param array $rules  Associative array of field => rule string (pipe-delimited)
 * @return array Associative array of field => error messages (empty if valid)
 *
 * Supported rules:
 *   required, email, min:N, max:N, numeric, alpha, alphanumeric,
 *   in:val1,val2, unique:table,column, date, url, regex:pattern,
 *   confirmed, same:field, different:field, between:min,max,
 *   integer, string, boolean, array, json, file, image, max_file_size:N
 */
function validate(array $data, array $rules): array
{
    $errors = [];

    foreach ($rules as $field => $ruleString) {
        $fieldRules = is_array($ruleString) ? $ruleString : explode('|', $ruleString);
        $value      = $data[$field] ?? null;
        $isRequired = in_array('required', $fieldRules, true);

        foreach ($fieldRules as $rule) {
            $rule = trim($rule);
            if ($rule === '') {
                continue;
            }

            // Parse rule name and parameters
            $params   = [];
            $ruleName = $rule;
            if (str_contains($rule, ':')) {
                [$ruleName, $paramStr] = explode(':', $rule, 2);
                $params = explode(',', $paramStr);
            }

            $label = ucfirst(str_replace('_', ' ', $field));

            // Skip non-required empty values (except 'required' itself)
            if (!$isRequired && ($value === null || $value === '') && $ruleName !== 'required') {
                continue;
            }

            $error = applyRule($ruleName, $value, $params, $field, $label, $data);
            if ($error !== null) {
                $errors[$field] = $errors[$field] ?? [];
                $errors[$field][] = $error;
            }
        }

        // Flatten single-error arrays to a string
        if (isset($errors[$field]) && count($errors[$field]) === 1) {
            $errors[$field] = $errors[$field][0];
        }
    }

    return $errors;
}

/**
 * Apply a single validation rule and return an error string or null.
 *
 * @param string      $rule
 * @param mixed       $value
 * @param array       $params
 * @param string      $field
 * @param string      $label Human-readable field name
 * @param array       $data  Full data array (for cross-field rules)
 * @return string|null
 */
function applyRule(string $rule, mixed $value, array $params, string $field, string $label, array $data): ?string
{
    switch ($rule) {
        case 'required':
            if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                return "{$label} is required.";
            }
            break;

        case 'email':
            if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return "{$label} must be a valid email address.";
            }
            break;

        case 'min':
            $min = (int)($params[0] ?? 0);
            if (is_string($value) && mb_strlen($value) < $min) {
                return "{$label} must be at least {$min} characters.";
            }
            if (is_numeric($value) && (float)$value < $min) {
                return "{$label} must be at least {$min}.";
            }
            if (is_array($value) && count($value) < $min) {
                return "{$label} must have at least {$min} items.";
            }
            break;

        case 'max':
            $max = (int)($params[0] ?? 0);
            if (is_string($value) && mb_strlen($value) > $max) {
                return "{$label} must not exceed {$max} characters.";
            }
            if (is_numeric($value) && (float)$value > $max) {
                return "{$label} must not exceed {$max}.";
            }
            if (is_array($value) && count($value) > $max) {
                return "{$label} must not have more than {$max} items.";
            }
            break;

        case 'numeric':
            if (!empty($value) && !is_numeric($value)) {
                return "{$label} must be a number.";
            }
            break;

        case 'integer':
            if (!empty($value) && !filter_var($value, FILTER_VALIDATE_INT) && $value !== '0' && $value !== 0) {
                return "{$label} must be an integer.";
            }
            break;

        case 'alpha':
            if (!empty($value) && !preg_match('/^[\pL\s]+$/u', (string)$value)) {
                return "{$label} must only contain letters.";
            }
            break;

        case 'alphanumeric':
            if (!empty($value) && !preg_match('/^[\pL\pN\s]+$/u', (string)$value)) {
                return "{$label} must only contain letters and numbers.";
            }
            break;

        case 'in':
            if (!empty($value) && !in_array((string)$value, $params, true)) {
                return "{$label} must be one of: " . implode(', ', $params) . '.';
            }
            break;

        case 'unique':
            if (!empty($value) && count($params) >= 2) {
                $table  = $params[0];
                $column = $params[1];
                $exceptId = $params[2] ?? null;

                require_once __DIR__ . '/database.php';

                $sql    = "SELECT COUNT(*) AS cnt FROM `{$table}` WHERE `{$column}` = :val";
                $qParams = [':val' => $value];

                if ($exceptId !== null) {
                    $sql .= ' AND id != :except';
                    $qParams[':except'] = $exceptId;
                }

                $row = fetch($sql, $qParams);
                if ($row && (int)$row['cnt'] > 0) {
                    return "{$label} has already been taken.";
                }
            }
            break;

        case 'date':
            if (!empty($value) && strtotime((string)$value) === false) {
                return "{$label} must be a valid date.";
            }
            break;

        case 'url':
            if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                return "{$label} must be a valid URL.";
            }
            break;

        case 'regex':
            $pattern = $params[0] ?? '';
            if (!empty($value) && $pattern !== '' && !preg_match($pattern, (string)$value)) {
                return "{$label} format is invalid.";
            }
            break;

        case 'confirmed':
            $confirmField = $field . '_confirmation';
            if (!isset($data[$confirmField]) || $value !== $data[$confirmField]) {
                return "{$label} confirmation does not match.";
            }
            break;

        case 'same':
            $otherField = $params[0] ?? '';
            if ($value !== ($data[$otherField] ?? null)) {
                $otherLabel = ucfirst(str_replace('_', ' ', $otherField));
                return "{$label} must match {$otherLabel}.";
            }
            break;

        case 'different':
            $otherField = $params[0] ?? '';
            if ($value === ($data[$otherField] ?? null)) {
                $otherLabel = ucfirst(str_replace('_', ' ', $otherField));
                return "{$label} must be different from {$otherLabel}.";
            }
            break;

        case 'between':
            $min = (float)($params[0] ?? 0);
            $max = (float)($params[1] ?? 0);
            if (is_numeric($value)) {
                $v = (float)$value;
                if ($v < $min || $v > $max) {
                    return "{$label} must be between {$min} and {$max}.";
                }
            } elseif (is_string($value)) {
                $len = mb_strlen($value);
                if ($len < (int)$min || $len > (int)$max) {
                    return "{$label} must be between {$min} and {$max} characters.";
                }
            }
            break;

        case 'string':
            if (!is_string($value) && $value !== null) {
                return "{$label} must be a string.";
            }
            break;

        case 'boolean':
            $allowed = [true, false, 0, 1, '0', '1', 'true', 'false'];
            if (!in_array($value, $allowed, true)) {
                return "{$label} must be a boolean value.";
            }
            break;

        case 'array':
            if (!is_array($value)) {
                return "{$label} must be an array.";
            }
            break;

        case 'json':
            if (is_string($value) && json_decode($value) === null && json_last_error() !== JSON_ERROR_NONE) {
                return "{$label} must be valid JSON.";
            }
            break;

        case 'file':
            if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
                return "{$label} must be a valid uploaded file.";
            }
            break;

        case 'image':
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                $mimeType = mime_content_type($_FILES[$field]['tmp_name']);
                if (!str_starts_with($mimeType, 'image/')) {
                    return "{$label} must be an image file.";
                }
            }
            break;

        case 'max_file_size':
            $maxBytes = (int)($params[0] ?? 0);
            if (isset($_FILES[$field]) && $_FILES[$field]['size'] > $maxBytes) {
                $maxMb = round($maxBytes / 1048576, 2);
                return "{$label} must not exceed {$maxMb} MB.";
            }
            break;
    }

    return null;
}

/**
 * Sanitize a value based on the specified type.
 *
 * @param mixed  $value
 * @param string $type  One of: string, email, int, float, html, url
 * @return mixed
 */
function sanitize(mixed $value, string $type = 'string'): mixed
{
    return match ($type) {
        'string' => is_string($value) ? htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8') : '',
        'email'  => is_string($value) ? filter_var(strtolower(trim($value)), FILTER_SANITIZE_EMAIL) : '',
        'int'    => (int)$value,
        'float'  => (float)$value,
        'html'   => is_string($value) ? strip_tags(trim($value), '<p><br><b><i><u><strong><em><ul><ol><li><a><h1><h2><h3><h4><h5><h6><blockquote><code><pre>') : '',
        'url'    => is_string($value) ? filter_var(trim($value), FILTER_SANITIZE_URL) : '',
        default  => is_string($value) ? htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8') : (string)$value,
    };
}

/**
 * Escape a string for safe HTML output.
 */
function sanitizeOutput(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Filter an associative array to only include allowed keys.
 *
 * @param array    $array       Input array
 * @param string[] $allowedKeys List of allowed key names
 * @return array Filtered array
 */
function sanitizeArray(array $array, array $allowedKeys): array
{
    return array_intersect_key($array, array_flip($allowedKeys));
}

/**
 * Validate the submitted CSRF token against the session token.
 */
function validateCSRF(string $token): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return false;
    }

    if (empty($_SESSION['csrf_token'])) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate a CSRF token and store it in the session.
 *
 * @return string The generated token
 */
function generateCSRF(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        if (php_sapi_name() !== 'cli') {
            session_start();
        } else {
            return bin2hex(random_bytes(32));
        }
    }

    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token']      = $token;
    $_SESSION['csrf_token_time'] = time();

    return $token;
}

/**
 * Return an HTML hidden input field containing the CSRF token.
 */
function csrfField(): string
{
    $token = $_SESSION['csrf_token'] ?? generateCSRF();
    return '<input type="hidden" name="csrf_token" value="' . sanitizeOutput($token) . '">';
}

/**
 * Validate an uploaded file against a set of options.
 *
 * @param array $file    The $_FILES entry for the upload
 * @param array $options Validation options:
 *                       - max_size: int (bytes)
 *                       - allowed_types: string[] (MIME types)
 *                       - allowed_extensions: string[]
 * @return array{valid: bool, errors: string[]}
 */
function validateFileUpload(array $file, array $options = []): array
{
    $errors  = [];
    $maxSize = $options['max_size'] ?? UPLOAD_MAX_SIZE;
    $allowedTypes = $options['allowed_types'] ?? UPLOAD_ALLOWED_TYPES;
    $allowedExts  = $options['allowed_extensions'] ?? UPLOAD_ALLOWED_EXTENSIONS;

    // Check for upload errors
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds the server upload limit.',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds the form upload limit.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary upload folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the upload.',
        ];
        $code = $file['error'] ?? -1;
        $errors[] = $errorMessages[$code] ?? 'Unknown upload error.';
        return ['valid' => false, 'errors' => $errors];
    }

    // Verify the file was actually uploaded via HTTP POST
    if (!is_uploaded_file($file['tmp_name'])) {
        $errors[] = 'Invalid upload attempt.';
        return ['valid' => false, 'errors' => $errors];
    }

    // File size check
    if ($file['size'] > $maxSize) {
        $maxMb = round($maxSize / 1048576, 2);
        $errors[] = "File must not exceed {$maxMb} MB.";
    }

    // Extension check
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExts, true)) {
        $errors[] = 'File type .' . $extension . ' is not allowed.';
    }

    // MIME type check (using file contents, not client-reported type)
    $mimeType = mime_content_type($file['tmp_name']);
    if (!in_array($mimeType, $allowedTypes, true)) {
        $errors[] = 'File MIME type ' . $mimeType . ' is not allowed.';
    }

    return ['valid' => empty($errors), 'errors' => $errors];
}

/**
 * Sanitize a filename for safe filesystem storage.
 * Removes path traversal sequences, special characters, and normalises the name.
 */
function cleanFileName(string $filename): string
{
    // Remove path components
    $filename = basename($filename);

    // Separate name and extension
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $name      = pathinfo($filename, PATHINFO_FILENAME);

    // Replace unsafe characters
    $name = preg_replace('/[^\w\-.]/', '_', $name);
    $name = preg_replace('/_+/', '_', $name);
    $name = trim($name, '_.');

    if ($name === '') {
        $name = 'file_' . time();
    }

    // Limit length
    $name = mb_substr($name, 0, 200);

    return $extension !== '' ? $name . '.' . $extension : $name;
}
