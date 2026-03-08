<?php
/**
 * EduWrite AI - Formatting Utility Functions
 * Date/time formatting, file sizes, word counts, text manipulation, and display helpers.
 */

/**
 * Format a datetime string into a specified format.
 *
 * @param string|int|null $date   Datetime string, Unix timestamp, or null for now
 * @param string          $format PHP date() format string
 * @return string Formatted date
 */
function formatDate(string|int|null $date = null, string $format = 'M j, Y'): string
{
    if ($date === null) {
        return date($format);
    }

    $timestamp = is_int($date) ? $date : strtotime($date);

    if ($timestamp === false) {
        return '';
    }

    return date($format, $timestamp);
}

/**
 * Format a datetime as a human-readable relative time string.
 * Example outputs: "just now", "5 minutes ago", "2 hours ago", "3 days ago"
 *
 * @param string|int $datetime Datetime string or Unix timestamp
 * @return string
 */
function formatRelativeTime(string|int $datetime): string
{
    $timestamp = is_int($datetime) ? $datetime : strtotime($datetime);

    if ($timestamp === false) {
        return '';
    }

    $now  = time();
    $diff = $now - $timestamp;

    if ($diff < 0) {
        $diff = abs($diff);
        return formatRelativeFuture($diff);
    }

    if ($diff < 10) {
        return 'just now';
    }
    if ($diff < 60) {
        return $diff . ' seconds ago';
    }
    if ($diff < 3600) {
        $minutes = (int)floor($diff / 60);
        return $minutes . ' ' . pluralize($minutes, 'minute', 'minutes') . ' ago';
    }
    if ($diff < 86400) {
        $hours = (int)floor($diff / 3600);
        return $hours . ' ' . pluralize($hours, 'hour', 'hours') . ' ago';
    }
    if ($diff < 604800) {
        $days = (int)floor($diff / 86400);
        return $days . ' ' . pluralize($days, 'day', 'days') . ' ago';
    }
    if ($diff < 2592000) {
        $weeks = (int)floor($diff / 604800);
        return $weeks . ' ' . pluralize($weeks, 'week', 'weeks') . ' ago';
    }
    if ($diff < 31536000) {
        $months = (int)floor($diff / 2592000);
        return $months . ' ' . pluralize($months, 'month', 'months') . ' ago';
    }

    $years = (int)floor($diff / 31536000);
    return $years . ' ' . pluralize($years, 'year', 'years') . ' ago';
}

/**
 * Format a future time difference.
 */
function formatRelativeFuture(int $diff): string
{
    if ($diff < 60) {
        return 'in ' . $diff . ' seconds';
    }
    if ($diff < 3600) {
        $minutes = (int)floor($diff / 60);
        return 'in ' . $minutes . ' ' . pluralize($minutes, 'minute', 'minutes');
    }
    if ($diff < 86400) {
        $hours = (int)floor($diff / 3600);
        return 'in ' . $hours . ' ' . pluralize($hours, 'hour', 'hours');
    }

    $days = (int)floor($diff / 86400);
    return 'in ' . $days . ' ' . pluralize($days, 'day', 'days');
}

/**
 * Format a byte count as a human-readable file size.
 *
 * @param int $bytes    Size in bytes
 * @param int $decimals Decimal precision
 * @return string e.g. "1.5 MB", "256 KB"
 */
function formatFileSize(int $bytes, int $decimals = 1): string
{
    if ($bytes < 0) {
        return '0 B';
    }

    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    $index = 0;
    $size  = (float)$bytes;

    while ($size >= 1024 && $index < count($units) - 1) {
        $size /= 1024;
        $index++;
    }

    if ($index === 0) {
        return $bytes . ' B';
    }

    return number_format($size, $decimals) . ' ' . $units[$index];
}

/**
 * Format a number with locale-appropriate separators.
 *
 * @param float|int $number   The number to format
 * @param int       $decimals Decimal places
 * @return string
 */
function formatNumber(float|int $number, int $decimals = 0): string
{
    return number_format($number, $decimals, '.', ',');
}

/**
 * Format a word count with comma separators.
 *
 * @param int $count Word count
 * @return string e.g. "1,234 words"
 */
function formatWordCount(int $count): string
{
    return number_format($count) . ' ' . pluralize($count, 'word', 'words');
}

/**
 * Format seconds as a human-readable duration.
 *
 * @param int $seconds Duration in seconds
 * @return string e.g. "2h 15m", "45s", "1d 3h"
 */
function formatDuration(int $seconds): string
{
    if ($seconds < 0) {
        return '0s';
    }

    if ($seconds < 60) {
        return $seconds . 's';
    }

    $days    = (int)floor($seconds / 86400);
    $hours   = (int)floor(($seconds % 86400) / 3600);
    $minutes = (int)floor(($seconds % 3600) / 60);
    $secs    = $seconds % 60;

    $parts = [];

    if ($days > 0) {
        $parts[] = $days . 'd';
    }
    if ($hours > 0) {
        $parts[] = $hours . 'h';
    }
    if ($minutes > 0) {
        $parts[] = $minutes . 'm';
    }
    if ($secs > 0 && $days === 0) {
        $parts[] = $secs . 's';
    }

    return implode(' ', $parts);
}

/**
 * Truncate text to a maximum length, appending a suffix.
 *
 * @param string $text   Text to truncate
 * @param int    $length Maximum character length
 * @param string $suffix Suffix to append when truncated
 * @return string
 */
function truncateText(string $text, int $length = 100, string $suffix = '…'): string
{
    if (mb_strlen($text) <= $length) {
        return $text;
    }

    // Truncate at word boundary
    $truncated = mb_substr($text, 0, $length);
    $lastSpace = mb_strrpos($truncated, ' ');

    if ($lastSpace !== false && $lastSpace > $length * 0.75) {
        $truncated = mb_substr($truncated, 0, $lastSpace);
    }

    return rtrim($truncated) . $suffix;
}

/**
 * Generate a URL-safe slug from text.
 *
 * @param string $text Input text
 * @return string URL-safe slug
 */
function slugify(string $text): string
{
    // Transliterate non-ASCII characters
    $text = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $text)
        ?? mb_strtolower($text, 'UTF-8');

    // Replace non-alphanumeric characters with hyphens
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    $text = preg_replace('/-+/', '-', $text);

    return $text ?: 'untitled';
}

/**
 * Extract an excerpt from longer content.
 *
 * @param string $text   Full text content
 * @param int    $length Maximum excerpt length
 * @return string Excerpt ending at a word boundary with ellipsis
 */
function excerpt(string $text, int $length = 200): string
{
    // Strip HTML and normalise whitespace
    $text = strip_tags($text);
    $text = preg_replace('/\s+/', ' ', trim($text));

    return truncateText($text, $length);
}

/**
 * Highlight search query matches within text.
 *
 * @param string $text  The text to search within
 * @param string $query The search query to highlight
 * @return string Text with matches wrapped in <mark> tags
 */
function highlightText(string $text, string $query): string
{
    if ($query === '') {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    $escaped = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    $pattern = '/(' . preg_quote(htmlspecialchars($query, ENT_QUOTES, 'UTF-8'), '/') . ')/iu';

    return preg_replace($pattern, '<mark class="bg-yellow-200">$1</mark>', $escaped);
}

/**
 * Convert basic Markdown to HTML.
 * Handles headings, bold, italic, links, code, lists, and paragraphs.
 */
function markdownToHtml(string $markdown): string
{
    $html = htmlspecialchars($markdown, ENT_QUOTES, 'UTF-8');

    // Code blocks (fenced)
    $html = preg_replace('/```([\s\S]*?)```/m', '<pre><code>$1</code></pre>', $html);

    // Inline code
    $html = preg_replace('/`([^`]+)`/', '<code class="bg-gray-100 px-1 rounded">$1</code>', $html);

    // Headings
    $html = preg_replace('/^######\s+(.+)$/m', '<h6 class="text-sm font-semibold mt-4 mb-1">$1</h6>', $html);
    $html = preg_replace('/^#####\s+(.+)$/m', '<h5 class="text-base font-semibold mt-4 mb-1">$1</h5>', $html);
    $html = preg_replace('/^####\s+(.+)$/m', '<h4 class="text-lg font-semibold mt-4 mb-2">$1</h4>', $html);
    $html = preg_replace('/^###\s+(.+)$/m', '<h3 class="text-xl font-semibold mt-5 mb-2">$1</h3>', $html);
    $html = preg_replace('/^##\s+(.+)$/m', '<h2 class="text-2xl font-bold mt-6 mb-3">$1</h2>', $html);
    $html = preg_replace('/^#\s+(.+)$/m', '<h1 class="text-3xl font-bold mt-6 mb-4">$1</h1>', $html);

    // Bold and italic
    $html = preg_replace('/\*\*\*(.+?)\*\*\*/s', '<strong><em>$1</em></strong>', $html);
    $html = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $html);
    $html = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $html);

    // Links
    $html = preg_replace(
        '/\[([^\]]+)\]\(([^)]+)\)/',
        '<a href="$2" class="text-indigo-600 underline hover:text-indigo-800" rel="noopener noreferrer">$1</a>',
        $html
    );

    // Unordered lists
    $html = preg_replace('/^[\-\*]\s+(.+)$/m', '<li class="ml-4">$1</li>', $html);
    $html = preg_replace('/(<li[^>]*>.*<\/li>\n?)+/s', '<ul class="list-disc pl-5 my-2">$0</ul>', $html);

    // Ordered lists
    $html = preg_replace('/^\d+\.\s+(.+)$/m', '<li class="ml-4">$1</li>', $html);

    // Horizontal rules
    $html = preg_replace('/^---+$/m', '<hr class="my-4 border-gray-300">', $html);

    // Blockquotes
    $html = preg_replace('/^&gt;\s*(.+)$/m', '<blockquote class="border-l-4 border-gray-300 pl-4 italic text-gray-600 my-2">$1</blockquote>', $html);

    // Paragraphs (double newlines)
    $html = preg_replace('/\n{2,}/', '</p><p class="my-2">', $html);

    // Single line breaks
    $html = preg_replace('/\n/', '<br>', $html);

    // Wrap in paragraph tag if not already wrapped in block element
    if (!preg_match('/^<(h[1-6]|ul|ol|pre|blockquote|hr|div|p)/', trim($html))) {
        $html = '<p class="my-2">' . $html . '</p>';
    }

    return $html;
}

/**
 * Remove Markdown formatting from text, returning plain text.
 */
function stripMarkdown(string $markdown): string
{
    // Remove headings
    $text = preg_replace('/^#{1,6}\s+/m', '', $markdown);

    // Remove bold/italic markers
    $text = preg_replace('/\*{1,3}(.+?)\*{1,3}/', '$1', $text);
    $text = preg_replace('/_{1,3}(.+?)_{1,3}/', '$1', $text);

    // Remove links, keep text
    $text = preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $text);

    // Remove images
    $text = preg_replace('/!\[([^\]]*)\]\([^)]+\)/', '$1', $text);

    // Remove inline code
    $text = preg_replace('/`([^`]+)`/', '$1', $text);

    // Remove code blocks
    $text = preg_replace('/```[\s\S]*?```/', '', $text);

    // Remove horizontal rules
    $text = preg_replace('/^---+$/m', '', $text);

    // Remove blockquote markers
    $text = preg_replace('/^>\s*/m', '', $text);

    // Remove list markers
    $text = preg_replace('/^[\*\-+]\s+/m', '', $text);
    $text = preg_replace('/^\d+\.\s+/m', '', $text);

    // Normalise whitespace
    return trim(preg_replace('/\s+/', ' ', $text));
}

/**
 * Format a percentage value.
 *
 * @param float|int $value    Numerator
 * @param float|int $total    Denominator
 * @param int       $decimals Decimal precision
 * @return string e.g. "85.5%"
 */
function formatPercentage(float|int $value, float|int $total, int $decimals = 1): string
{
    if ($total == 0) {
        return '0%';
    }

    return number_format(($value / $total) * 100, $decimals) . '%';
}

/**
 * Format a numeric grade into a letter grade with styling info.
 *
 * @param float|int $grade Numeric grade (0-100)
 * @return array{letter: string, color: string, label: string}
 */
function formatGrade(float|int $grade): array
{
    return match (true) {
        $grade >= 97 => ['letter' => 'A+', 'color' => 'text-green-700', 'label' => 'Excellent'],
        $grade >= 93 => ['letter' => 'A',  'color' => 'text-green-600', 'label' => 'Excellent'],
        $grade >= 90 => ['letter' => 'A-', 'color' => 'text-green-500', 'label' => 'Great'],
        $grade >= 87 => ['letter' => 'B+', 'color' => 'text-blue-600',  'label' => 'Good'],
        $grade >= 83 => ['letter' => 'B',  'color' => 'text-blue-500',  'label' => 'Good'],
        $grade >= 80 => ['letter' => 'B-', 'color' => 'text-blue-400',  'label' => 'Above Average'],
        $grade >= 77 => ['letter' => 'C+', 'color' => 'text-yellow-600','label' => 'Average'],
        $grade >= 73 => ['letter' => 'C',  'color' => 'text-yellow-500','label' => 'Average'],
        $grade >= 70 => ['letter' => 'C-', 'color' => 'text-yellow-400','label' => 'Below Average'],
        $grade >= 67 => ['letter' => 'D+', 'color' => 'text-orange-500','label' => 'Poor'],
        $grade >= 63 => ['letter' => 'D',  'color' => 'text-orange-600','label' => 'Poor'],
        $grade >= 60 => ['letter' => 'D-', 'color' => 'text-orange-700','label' => 'Very Poor'],
        default      => ['letter' => 'F',  'color' => 'text-red-600',   'label' => 'Failing'],
    };
}

/**
 * Return the correct singular or plural form based on count.
 *
 * @param int    $count    Item count
 * @param string $singular Singular form
 * @param string $plural   Plural form
 * @return string
 */
function pluralize(int $count, string $singular, string $plural = ''): string
{
    if ($plural === '') {
        $plural = $singular . 's';
    }

    return $count === 1 ? $singular : $plural;
}

/**
 * Extract initials from a person's name.
 *
 * @param string $name Full name
 * @return string Uppercase initials (max 2 characters)
 */
function initials(string $name): string
{
    $name  = trim($name);
    $parts = preg_split('/\s+/', $name);

    if (empty($parts) || $parts[0] === '') {
        return '?';
    }

    if (count($parts) === 1) {
        return mb_strtoupper(mb_substr($parts[0], 0, 1));
    }

    return mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr(end($parts), 0, 1));
}

/**
 * Generate a consistent HSL color string from an input string.
 * Useful for avatar backgrounds, tags, etc.
 *
 * @param string $string Input string (e.g. a username or email)
 * @return string CSS HSL color value
 */
function colorFromString(string $string): string
{
    $hash = crc32($string);
    $hue  = abs($hash) % 360;

    return "hsl({$hue}, 65%, 55%)";
}

/**
 * Convert an activity action key into a human-readable description.
 *
 * @param string $action Action key (e.g. "document_created", "login_success")
 * @return string Human-readable action
 */
function formatActivityAction(string $action): string
{
    $map = [
        'login_success'      => 'Logged in',
        'login_failed'       => 'Failed login attempt',
        'logout'             => 'Logged out',
        'password_changed'   => 'Changed password',
        'profile_updated'    => 'Updated profile',
        'document_created'   => 'Created a document',
        'document_updated'   => 'Updated a document',
        'document_deleted'   => 'Deleted a document',
        'document_submitted' => 'Submitted a document',
        'document_shared'    => 'Shared a document',
        'comment_added'      => 'Added a comment',
        'comment_deleted'    => 'Deleted a comment',
        'assignment_created' => 'Created an assignment',
        'assignment_updated' => 'Updated an assignment',
        'assignment_graded'  => 'Graded an assignment',
        'class_created'      => 'Created a class',
        'class_updated'      => 'Updated a class',
        'student_enrolled'   => 'Enrolled a student',
        'student_removed'    => 'Removed a student',
        'ai_request'         => 'Used AI assistant',
        'ai_request_refused' => 'AI request was refused',
        'policy_violation'   => 'Policy violation detected',
        'export_started'     => 'Started an export',
        'export_completed'   => 'Completed an export',
        'import_started'     => 'Started an import',
        'import_completed'   => 'Completed an import',
        'settings_updated'   => 'Updated settings',
        'security_csrf_failure'       => 'CSRF validation failed',
        'security_unauthorized_access' => 'Unauthorized access attempt',
    ];

    if (isset($map[$action])) {
        return $map[$action];
    }

    // Generate a readable label from the action key
    $label = str_replace(['_', '-'], ' ', $action);
    return ucfirst($label);
}
