<?php
/**
 * EduWrite AI - Sidebar Navigation Partial
 */
$currentUser = $currentUser ?? null;
$currentPage = $currentPage ?? '';
$userRole = $currentUser['role'] ?? 'student';

function sidebarLink($label, $icon, $href, $currentPage, $page, $badge = null) {
    $active = ($currentPage === $page);
    $cls = $active
        ? 'bg-indigo-50 text-indigo-700 border-l-3 border-indigo-600'
        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-3 border-transparent';
    $html = '<a href="' . htmlspecialchars($href) . '" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-r-lg transition ' . $cls . '">';
    $html .= $icon;
    $html .= '<span>' . htmlspecialchars($label) . '</span>';
    if ($badge !== null && $badge > 0) {
        $html .= '<span class="ml-auto inline-flex items-center justify-center w-5 h-5 text-xs font-semibold bg-red-100 text-red-700 rounded-full">' . (int)$badge . '</span>';
    }
    $html .= '</a>';
    return $html;
}

$icons = [
    'dashboard' => '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>',
    'documents' => '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
    'assignments' => '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>',
    'classes' => '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>',
    'ai' => '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>',
    'students' => '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>',
    'analytics' => '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
    'flags' => '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>',
    'users' => '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
    'policies' => '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>',
    'audit' => '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    'settings' => '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.573-1.066z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
    'school' => '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>',
];
?>
<aside class="fixed left-0 top-16 bottom-0 w-64 bg-white border-r border-gray-200 overflow-y-auto z-20 transition-transform duration-300 lg:translate-x-0"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
    <nav class="py-4 space-y-1">
        <?php if ($userRole === 'student'): ?>
            <div class="px-4 mb-2"><p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Main</p></div>
            <?= sidebarLink('Dashboard', $icons['dashboard'], '/pages/student/dashboard.php', $currentPage, 'dashboard') ?>
            <?= sidebarLink('My Documents', $icons['documents'], '/pages/student/documents.php', $currentPage, 'documents') ?>
            <?= sidebarLink('Assignments', $icons['assignments'], '/pages/student/assignments.php', $currentPage, 'assignments') ?>
            <?= sidebarLink('My Classes', $icons['classes'], '/pages/student/classes.php', $currentPage, 'classes') ?>
            <div class="px-4 mt-5 mb-2"><p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tools</p></div>
            <?= sidebarLink('AI Assistant', $icons['ai'], '/pages/student/ai-assistant.php', $currentPage, 'ai-assistant') ?>
            <?= sidebarLink('Analytics', $icons['analytics'], '/pages/student/analytics.php', $currentPage, 'analytics') ?>

        <?php elseif ($userRole === 'teacher'): ?>
            <div class="px-4 mb-2"><p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Teaching</p></div>
            <?= sidebarLink('Dashboard', $icons['dashboard'], '/pages/teacher/dashboard.php', $currentPage, 'dashboard') ?>
            <?= sidebarLink('Classes', $icons['classes'], '/pages/teacher/classes.php', $currentPage, 'classes') ?>
            <?= sidebarLink('Assignments', $icons['assignments'], '/pages/teacher/assignments.php', $currentPage, 'assignments') ?>
            <?= sidebarLink('Students', $icons['students'], '/pages/teacher/students.php', $currentPage, 'students') ?>
            <div class="px-4 mt-5 mb-2"><p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Review</p></div>
            <?= sidebarLink('Documents', $icons['documents'], '/pages/teacher/documents.php', $currentPage, 'documents') ?>
            <?= sidebarLink('Integrity Flags', $icons['flags'], '/pages/teacher/flags.php', $currentPage, 'flags') ?>
            <?= sidebarLink('Analytics', $icons['analytics'], '/pages/teacher/analytics.php', $currentPage, 'analytics') ?>

        <?php elseif ($userRole === 'admin' || $userRole === 'super_admin'): ?>
            <div class="px-4 mb-2"><p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Administration</p></div>
            <?= sidebarLink('Dashboard', $icons['dashboard'], '/pages/admin/dashboard.php', $currentPage, 'dashboard') ?>
            <?= sidebarLink('Users', $icons['users'], '/pages/admin/users.php', $currentPage, 'users') ?>
            <?= sidebarLink('Schools', $icons['school'], '/pages/admin/schools.php', $currentPage, 'schools') ?>
            <?= sidebarLink('Classes', $icons['classes'], '/pages/admin/classes.php', $currentPage, 'classes') ?>
            <div class="px-4 mt-5 mb-2"><p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Governance</p></div>
            <?= sidebarLink('AI Policies', $icons['policies'], '/pages/admin/policies.php', $currentPage, 'policies') ?>
            <?= sidebarLink('Violations', $icons['flags'], '/pages/admin/violations.php', $currentPage, 'violations') ?>
            <?= sidebarLink('Analytics', $icons['analytics'], '/pages/admin/analytics.php', $currentPage, 'analytics') ?>
            <?= sidebarLink('Audit Log', $icons['audit'], '/pages/admin/audit-log.php', $currentPage, 'audit-log') ?>
            <?= sidebarLink('Settings', $icons['settings'], '/pages/admin/settings.php', $currentPage, 'settings') ?>
        <?php endif; ?>
    </nav>

    <!-- User Info at Bottom -->
    <?php if ($currentUser): ?>
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 bg-gray-50">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-semibold flex-shrink-0">
                <?= htmlspecialchars(strtoupper(substr($currentUser['first_name'] ?? 'U', 0, 1))) ?>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate"><?= htmlspecialchars(($currentUser['first_name'] ?? '') . ' ' . ($currentUser['last_name'] ?? '')) ?></p>
                <p class="text-xs text-gray-500 capitalize"><?= htmlspecialchars($currentUser['role'] ?? '') ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</aside>
