<?php
/**
 * Student Dashboard — LearnAI
 *
 * Rendered inside layouts/app.php (sidebar + topnav chrome).
 * Displays an overview of recent documents, assignment progress,
 * AI-usage analytics, writing insights, activity timeline, and
 * notifications.  Uses Chart.js for the AI-usage doughnut chart
 * and Alpine.js for all client-side interactivity.
 */

$pageTitle = 'Dashboard';

/* ──────────────────────────────────────────────
 * Fake data arrays – used to populate every section
 * ────────────────────────────────────────────── */

$recentDocuments = [
    [
        'id'         => 1,
        'title'      => 'Persuasive Essay: Climate Action',
        'updated_at' => date('Y-m-d H:i:s', strtotime('-22 minutes')),
        'words'      => 1842,
        'status'     => 'draft',
    ],
    [
        'id'         => 2,
        'title'      => 'Lab Report — Organic Chemistry',
        'updated_at' => date('Y-m-d H:i:s', strtotime('-3 hours')),
        'words'      => 3210,
        'status'     => 'submitted',
    ],
    [
        'id'         => 3,
        'title'      => 'Literary Analysis: The Great Gatsby',
        'updated_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
        'words'      => 2475,
        'status'     => 'graded',
    ],
    [
        'id'         => 4,
        'title'      => 'Research Paper: Machine Learning Ethics',
        'updated_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
        'words'      => 4518,
        'status'     => 'published',
    ],
    [
        'id'         => 5,
        'title'      => 'Creative Writing: Short Story Draft',
        'updated_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
        'words'      => 1105,
        'status'     => 'draft',
    ],
];

$assignments = [
    [
        'id'        => 101,
        'title'     => 'Argumentative Essay — Social Media Impact',
        'class'     => 'English Composition 201',
        'due'       => date('Y-m-d', strtotime('+2 days')),
        'progress'  => 65,
        'status'    => 'in_progress',
        'grade'     => null,
    ],
    [
        'id'        => 102,
        'title'     => 'Annotated Bibliography — Renewable Energy',
        'class'     => 'Environmental Science 110',
        'due'       => date('Y-m-d', strtotime('+5 days')),
        'progress'  => 0,
        'status'    => 'not_started',
        'grade'     => null,
    ],
    [
        'id'        => 103,
        'title'     => 'Reflective Journal — Week 6',
        'class'     => 'Psychology 101',
        'due'       => date('Y-m-d', strtotime('-1 day')),
        'progress'  => 100,
        'status'    => 'graded',
        'grade'     => 'A',
    ],
    [
        'id'        => 104,
        'title'     => 'Case Study Analysis — Tesla Inc.',
        'class'     => 'Business Management 305',
        'due'       => date('Y-m-d', strtotime('-3 days')),
        'progress'  => 100,
        'status'    => 'submitted',
        'grade'     => null,
    ],
];

$activities = [
    [
        'type'        => 'document_edit',
        'description' => 'Edited "Persuasive Essay: Climate Action"',
        'timestamp'   => date('Y-m-d H:i:s', strtotime('-22 minutes')),
    ],
    [
        'type'        => 'ai_query',
        'description' => 'Asked AI to improve thesis statement clarity',
        'timestamp'   => date('Y-m-d H:i:s', strtotime('-1 hour')),
    ],
    [
        'type'        => 'assignment_submit',
        'description' => 'Submitted "Case Study Analysis — Tesla Inc."',
        'timestamp'   => date('Y-m-d H:i:s', strtotime('-3 hours')),
    ],
    [
        'type'        => 'grade_received',
        'description' => 'Received grade A on "Reflective Journal — Week 6"',
        'timestamp'   => date('Y-m-d H:i:s', strtotime('-8 hours')),
    ],
    [
        'type'        => 'document_edit',
        'description' => 'Created "Lab Report — Organic Chemistry"',
        'timestamp'   => date('Y-m-d H:i:s', strtotime('-1 day')),
    ],
    [
        'type'        => 'ai_query',
        'description' => 'Used AI grammar checker on research paper',
        'timestamp'   => date('Y-m-d H:i:s', strtotime('-1 day')),
    ],
    [
        'type'        => 'assignment_submit',
        'description' => 'Submitted "Reflective Journal — Week 6"',
        'timestamp'   => date('Y-m-d H:i:s', strtotime('-2 days')),
    ],
    [
        'type'        => 'grade_received',
        'description' => 'Received grade B+ on "Comparative Literature Review"',
        'timestamp'   => date('Y-m-d H:i:s', strtotime('-3 days')),
    ],
    [
        'type'        => 'document_edit',
        'description' => 'Edited "Research Paper: Machine Learning Ethics"',
        'timestamp'   => date('Y-m-d H:i:s', strtotime('-4 days')),
    ],
    [
        'type'        => 'ai_query',
        'description' => 'Generated outline for argumentative essay',
        'timestamp'   => date('Y-m-d H:i:s', strtotime('-5 days')),
    ],
];

$notifications = [
    [
        'id'      => 1,
        'type'    => 'grade',
        'title'   => 'New Grade Posted',
        'message' => 'You received an A on Reflective Journal — Week 6 in Psychology 101.',
        'time'    => date('Y-m-d H:i:s', strtotime('-8 hours')),
        'read'    => false,
    ],
    [
        'id'      => 2,
        'type'    => 'assignment',
        'title'   => 'Assignment Due Soon',
        'message' => 'Argumentative Essay — Social Media Impact is due in 2 days.',
        'time'    => date('Y-m-d H:i:s', strtotime('-12 hours')),
        'read'    => false,
    ],
    [
        'id'      => 3,
        'type'    => 'feedback',
        'title'   => 'Teacher Feedback',
        'message' => 'Prof. Williams left feedback on your Lab Report draft.',
        'time'    => date('Y-m-d H:i:s', strtotime('-1 day')),
        'read'    => false,
    ],
    [
        'id'      => 4,
        'type'    => 'system',
        'title'   => 'New AI Feature Available',
        'message' => 'Try our new citation generator — supports APA, MLA, and Chicago formats.',
        'time'    => date('Y-m-d H:i:s', strtotime('-2 days')),
        'read'    => true,
    ],
    [
        'id'      => 5,
        'type'    => 'assignment',
        'title'   => 'New Assignment Posted',
        'message' => 'Annotated Bibliography — Renewable Energy has been assigned in Environmental Science 110.',
        'time'    => date('Y-m-d H:i:s', strtotime('-3 days')),
        'read'    => true,
    ],
];

$motivationalMessages = [
    'Keep up the great work — consistency is the key to mastery!',
    'Your writing has improved 12 % this month. Impressive progress!',
    'You&rsquo;re on a 5-day streak! Keep the momentum going.',
    'Small steps every day lead to big results. You&rsquo;ve got this!',
];
$motivationalMessage = $motivationalMessages[array_rand($motivationalMessages)];

$aiPromptTypes = [
    ['label' => 'Grammar & Style Check',   'count' => 34, 'color' => 'indigo'],
    ['label' => 'Thesis Improvement',       'count' => 18, 'color' => 'sky'],
    ['label' => 'Outline Generation',       'count' => 12, 'color' => 'amber'],
    ['label' => 'Citation Formatting',      'count' => 9,  'color' => 'emerald'],
    ['label' => 'Paraphrasing Assistance',  'count' => 7,  'color' => 'rose'],
];

$writingStrengths = [
    ['label' => 'Argument Structure',   'score' => 88],
    ['label' => 'Grammar & Mechanics',  'score' => 76],
    ['label' => 'Vocabulary Range',     'score' => 82],
    ['label' => 'Citation Accuracy',    'score' => 91],
];

$writingTips = [
    'Try varying your sentence length to improve readability.',
    'Use more transition words to strengthen paragraph flow.',
    'Consider adding a counter-argument section in your essays.',
    'Your introductions are strong — apply the same hook technique to conclusions.',
];

/* ──────────────────────────────────────────────
 * Assignment status → display config map
 * ────────────────────────────────────────────── */
$statusConfig = [
    'not_started' => ['label' => 'Not Started', 'bg' => 'bg-slate-100',   'text' => 'text-slate-700',   'bar' => 'bg-slate-300'],
    'in_progress' => ['label' => 'In Progress', 'bg' => 'bg-blue-100',    'text' => 'text-blue-700',    'bar' => 'bg-blue-500'],
    'submitted'   => ['label' => 'Submitted',   'bg' => 'bg-amber-100',   'text' => 'text-amber-700',   'bar' => 'bg-amber-500'],
    'graded'      => ['label' => 'Graded',      'bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'bar' => 'bg-emerald-500'],
    'overdue'     => ['label' => 'Overdue',      'bg' => 'bg-rose-100',   'text' => 'text-rose-700',    'bar' => 'bg-rose-500'],
];

/* activity type → icon / colour */
$activityConfig = [
    'document_edit'     => ['color' => 'indigo', 'icon' => 'pencil'],
    'assignment_submit' => ['color' => 'amber',  'icon' => 'paper-airplane'],
    'ai_query'          => ['color' => 'sky',    'icon' => 'sparkles'],
    'grade_received'    => ['color' => 'emerald', 'icon' => 'academic-cap'],
];
?>

<!-- ═══════════════════════════════════════════════
     1. WELCOME BANNER
     ═══════════════════════════════════════════════ -->
<section class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-indigo-500 to-sky-500 p-6 sm:p-8 text-white shadow-lg shadow-indigo-200/50">
    <div class="absolute -right-10 -top-10 h-52 w-52 rounded-full bg-white/10 blur-2xl"></div>
    <div class="absolute -left-16 -bottom-16 h-64 w-64 rounded-full bg-white/5 blur-3xl"></div>

    <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                Good <?= date('H') < 12 ? 'morning' : (date('H') < 17 ? 'afternoon' : 'evening') ?>, <?= e(userName()) ?>!
            </h1>
            <p class="mt-1 text-indigo-100 text-sm sm:text-base max-w-xl"><?= $motivationalMessage ?></p>
        </div>

        <div class="flex flex-wrap gap-2 shrink-0">
            <a href="<?= url('/documents/new') ?>" class="inline-flex items-center gap-1.5 rounded-lg bg-white/20 backdrop-blur px-4 py-2 text-sm font-medium text-white hover:bg-white/30 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                New Document
            </a>
            <a href="<?= url('/assignments') ?>" class="inline-flex items-center gap-1.5 rounded-lg bg-white/20 backdrop-blur px-4 py-2 text-sm font-medium text-white hover:bg-white/30 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15a2.251 2.251 0 0 1 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V19.5a2.25 2.25 0 0 0 2.25 2.25h.75"/></svg>
                Assignments
            </a>
            <a href="<?= url('/ai-assistant') ?>" class="inline-flex items-center gap-1.5 rounded-lg bg-white/20 backdrop-blur px-4 py-2 text-sm font-medium text-white hover:bg-white/30 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456Z"/></svg>
                AI Assistant
            </a>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════
     2. STATS ROW (4 Cards)
     ═══════════════════════════════════════════════ -->
<section class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Documents -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-start gap-4">
        <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
        </span>
        <div class="min-w-0">
            <p class="text-2xl font-bold text-slate-900">24</p>
            <p class="text-sm text-slate-500">Documents</p>
            <p class="mt-1 inline-flex items-center gap-0.5 text-xs font-medium text-emerald-600">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/></svg>
                +3 this week
            </p>
        </div>
    </div>

    <!-- Assignments -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-start gap-4">
        <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-amber-50 text-amber-600 shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15a2.251 2.251 0 0 1 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V19.5a2.25 2.25 0 0 0 2.25 2.25h.75"/></svg>
        </span>
        <div class="min-w-0">
            <p class="text-2xl font-bold text-slate-900">7<span class="text-base font-normal text-slate-400">/12</span></p>
            <p class="text-sm text-slate-500">Assignments Done</p>
            <div class="mt-2 w-full h-1.5 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-full rounded-full bg-amber-500" style="width: 58%"></div>
            </div>
        </div>
    </div>

    <!-- AI Sessions -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-start gap-4">
        <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-sky-50 text-sky-600 shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456Z"/></svg>
        </span>
        <div class="min-w-0">
            <p class="text-2xl font-bold text-slate-900">18</p>
            <p class="text-sm text-slate-500">AI Sessions This Week</p>
            <p class="mt-1 inline-flex items-center gap-0.5 text-xs font-medium text-emerald-600">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/></svg>
                +5 vs last week
            </p>
        </div>
    </div>

    <!-- Writing Score -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-start gap-4">
        <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>
        </span>
        <div class="min-w-0">
            <p class="text-2xl font-bold text-slate-900">B+</p>
            <p class="text-sm text-slate-500">Avg Writing Score</p>
            <p class="mt-1 inline-flex items-center gap-0.5 text-xs font-medium text-rose-500">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 4.5l-15 15m0 0h11.25M4.5 19.5V8.25"/></svg>
                -2 pts vs last month
            </p>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════
     3. RECENT DOCUMENTS
     ═══════════════════════════════════════════════ -->
<section class="mt-6 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-slate-900">Recent Documents</h2>
        <a href="<?= url('/documents') ?>" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 transition">
            View All Documents &rarr;
        </a>
    </div>

    <div class="overflow-x-auto -mx-6">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden sm:table-cell">Words</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Last Edited</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($recentDocuments as $doc): ?>
                <tr class="group hover:bg-slate-50 transition">
                    <td class="px-6 py-4">
                        <a href="<?= url('/documents/' . (int) $doc['id']) ?>" class="font-medium text-slate-900 group-hover:text-indigo-600 transition">
                            <?= e(truncate($doc['title'], 45)) ?>
                        </a>
                    </td>
                    <td class="px-6 py-4 text-slate-500 hidden sm:table-cell">
                        <?= e(formatNumber($doc['words'])) ?>
                    </td>
                    <td class="px-6 py-4 text-slate-400 hidden md:table-cell">
                        <?= e(timeAgo($doc['updated_at'])) ?>
                    </td>
                    <td class="px-6 py-4">
                        <?= statusBadge($doc['status']) ?>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="<?= url('/documents/' . (int) $doc['id']) ?>" class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 hover:text-indigo-800 transition">
                            Open
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- ═══════════════════════════════════════════════
     4. ASSIGNMENT PROGRESS
     ═══════════════════════════════════════════════ -->
<section class="mt-6 space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-slate-900">Assignment Progress</h2>
        <a href="<?= url('/assignments') ?>" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 transition">
            View All &rarr;
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php foreach ($assignments as $assignment):
            $cfg = $statusConfig[$assignment['status']] ?? $statusConfig['not_started'];
        ?>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="font-semibold text-slate-900 truncate"><?= e($assignment['title']) ?></h3>
                    <p class="text-sm text-slate-500 mt-0.5"><?= e($assignment['class']) ?></p>
                </div>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium shrink-0 <?= e($cfg['bg']) ?> <?= e($cfg['text']) ?>">
                    <?= e($cfg['label']) ?>
                </span>
            </div>

            <div class="mt-4 flex items-center justify-between text-xs text-slate-500">
                <span class="inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                    Due <?= e(formatDate($assignment['due'])) ?>
                </span>
                <?php if ($assignment['grade']): ?>
                    <span class="inline-flex items-center gap-1 font-semibold text-emerald-600">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342"/></svg>
                        Grade: <?= e($assignment['grade']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <div class="mt-3">
                <div class="flex items-center justify-between text-xs mb-1">
                    <span class="text-slate-500">Progress</span>
                    <span class="font-medium text-slate-700"><?= (int) $assignment['progress'] ?>%</span>
                </div>
                <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500 <?= e($cfg['bar']) ?>" style="width: <?= (int) $assignment['progress'] ?>%"></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ═══════════════════════════════════════════════
     5. AI ASSISTANCE STATS  &  6. WRITING INSIGHTS
     (Two-column on large screens)
     ═══════════════════════════════════════════════ -->
<section class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- 5 · AI Assistance Stats -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">AI Assistance Usage</h2>

        <div class="flex flex-col sm:flex-row gap-6 items-start">
            <!-- Chart canvas -->
            <div class="w-44 h-44 shrink-0 mx-auto sm:mx-0">
                <canvas id="aiUsageChart" width="176" height="176"></canvas>
            </div>

            <!-- Prompt type breakdown -->
            <div class="flex-1 space-y-3 w-full">
                <?php foreach ($aiPromptTypes as $pt): ?>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-2.5 h-2.5 rounded-full bg-<?= e($pt['color']) ?>-500 shrink-0"></span>
                        <span class="text-sm text-slate-700 truncate"><?= e($pt['label']) ?></span>
                    </div>
                    <span class="text-sm font-semibold text-slate-900 tabular-nums"><?= (int) $pt['count'] ?></span>
                </div>
                <?php endforeach; ?>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-sm font-medium text-slate-700">Total Sessions</span>
                    <span class="text-sm font-bold text-indigo-600 tabular-nums">80</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 6 · Writing Insights Panel -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Writing Insights</h2>

        <!-- Readability gauge -->
        <div class="flex items-center gap-5 mb-6">
            <div class="relative w-24 h-24 shrink-0">
                <svg class="w-24 h-24 -rotate-90" viewBox="0 0 36 36">
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke-width="3" class="stroke-slate-100"/>
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke-width="3" stroke-dasharray="74, 100" stroke-linecap="round" class="stroke-indigo-500"/>
                </svg>
                <span class="absolute inset-0 flex items-center justify-center text-lg font-bold text-slate-900">74</span>
            </div>
            <div>
                <p class="font-semibold text-slate-900">Readability Score</p>
                <p class="text-sm text-slate-500 mt-0.5">Flesch-Kincaid Grade Level 9.2</p>
                <p class="text-xs text-emerald-600 mt-1 inline-flex items-center gap-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/></svg>
                    +4 from last month
                </p>
            </div>
        </div>

        <!-- Word count trend -->
        <div class="mb-6 rounded-lg bg-slate-50 p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-slate-700">Monthly Word Count</span>
                <span class="text-xs text-slate-400">Last 4 months</span>
            </div>
            <div class="flex items-end gap-2 h-16">
                <div class="flex-1 bg-indigo-200 rounded-t" style="height: 40%"></div>
                <div class="flex-1 bg-indigo-300 rounded-t" style="height: 60%"></div>
                <div class="flex-1 bg-indigo-400 rounded-t" style="height: 75%"></div>
                <div class="flex-1 bg-indigo-500 rounded-t" style="height: 100%"></div>
            </div>
            <div class="flex justify-between mt-1 text-[10px] text-slate-400">
                <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span>
            </div>
        </div>

        <!-- Strength areas -->
        <div class="space-y-3">
            <p class="text-sm font-medium text-slate-700">Strength Areas</p>
            <?php foreach ($writingStrengths as $strength): ?>
            <div>
                <div class="flex items-center justify-between text-xs mb-1">
                    <span class="text-slate-600"><?= e($strength['label']) ?></span>
                    <span class="font-medium text-slate-900"><?= (int) $strength['score'] ?>%</span>
                </div>
                <div class="w-full h-1.5 rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full rounded-full bg-indigo-500 transition-all duration-500" style="width: <?= (int) $strength['score'] ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════
     Writing Improvement Tips
     ═══════════════════════════════════════════════ -->
<section class="mt-6 bg-gradient-to-r from-indigo-50 to-sky-50 rounded-xl border border-indigo-100 p-6">
    <div class="flex items-center gap-2 mb-4">
        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18"/></svg>
        <h2 class="text-lg font-semibold text-slate-900">Writing Tips for You</h2>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <?php foreach ($writingTips as $index => $tip): ?>
        <div class="flex items-start gap-3 bg-white/70 backdrop-blur rounded-lg p-4">
            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold shrink-0"><?= $index + 1 ?></span>
            <p class="text-sm text-slate-700"><?= e($tip) ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ═══════════════════════════════════════════════
     7. ACTIVITY TIMELINE  &  8. NOTIFICATIONS
     (Two-column on large screens)
     ═══════════════════════════════════════════════ -->
<section class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- 7 · Activity Timeline (takes 2 cols) -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-5">Recent Activity</h2>

        <div class="relative">
            <!-- vertical line -->
            <div class="absolute left-4 top-2 bottom-2 w-px bg-slate-200"></div>

            <div class="space-y-6">
                <?php foreach ($activities as $act):
                    $ac = $activityConfig[$act['type']] ?? $activityConfig['document_edit'];
                ?>
                <div class="relative flex items-start gap-4 pl-10">
                    <!-- dot -->
                    <span class="absolute left-2.5 top-1 w-3 h-3 rounded-full border-2 border-white bg-<?= e($ac['color']) ?>-500 ring-2 ring-<?= e($ac['color']) ?>-100 shrink-0"></span>

                    <!-- icon -->
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-<?= e($ac['color']) ?>-50 text-<?= e($ac['color']) ?>-600 shrink-0">
                        <?php if ($ac['icon'] === 'pencil'): ?>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                        <?php elseif ($ac['icon'] === 'paper-airplane'): ?>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                        <?php elseif ($ac['icon'] === 'sparkles'): ?>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456Z"/></svg>
                        <?php elseif ($ac['icon'] === 'academic-cap'): ?>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342"/></svg>
                        <?php endif; ?>
                    </span>

                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-slate-700"><?= e($act['description']) ?></p>
                        <p class="text-xs text-slate-400 mt-0.5"><?= e(timeAgo($act['timestamp'])) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- 8 · Notifications Panel -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6" x-data="{
        notifications: <?= e(json_encode(array_map(function ($n) {
            return [
                'id'      => $n['id'],
                'type'    => $n['type'],
                'title'   => $n['title'],
                'message' => $n['message'],
                'time'    => timeAgo($n['time']),
                'read'    => $n['read'],
            ];
        }, $notifications))) ?>,

        markAsRead(id) {
            this.notifications = this.notifications.map(n => n.id === id ? { ...n, read: true } : n);
        },

        markAllRead() {
            this.notifications = this.notifications.map(n => ({ ...n, read: true }));
        },

        get unreadCount() {
            return this.notifications.filter(n => !n.read).length;
        }
    }">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <h2 class="text-lg font-semibold text-slate-900">Notifications</h2>
                <span x-show="unreadCount > 0"
                      x-transition
                      class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-indigo-600 text-white text-[10px] font-bold"
                      x-text="unreadCount"></span>
            </div>
            <button @click="markAllRead()" x-show="unreadCount > 0" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition">
                Mark all read
            </button>
        </div>

        <div class="space-y-2">
            <template x-for="n in notifications" :key="n.id">
                <div @click="markAsRead(n.id)"
                     class="rounded-lg p-3 cursor-pointer transition-all duration-200"
                     :class="n.read ? 'bg-white hover:bg-slate-50' : 'bg-indigo-50 hover:bg-indigo-100/70'">
                    <div class="flex items-start gap-3">
                        <!-- Icon per type -->
                        <span class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0"
                              :class="{
                                  'bg-emerald-100 text-emerald-600': n.type === 'grade',
                                  'bg-amber-100 text-amber-600':    n.type === 'assignment',
                                  'bg-sky-100 text-sky-600':        n.type === 'feedback',
                                  'bg-slate-100 text-slate-600':    n.type === 'system',
                              }">
                            <!-- grade icon -->
                            <svg x-show="n.type === 'grade'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342"/></svg>
                            <!-- assignment icon -->
                            <svg x-show="n.type === 'assignment'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15a2.251 2.251 0 0 1 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V19.5a2.25 2.25 0 0 0 2.25 2.25h.75"/></svg>
                            <!-- feedback icon -->
                            <svg x-show="n.type === 'feedback'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/></svg>
                            <!-- system icon -->
                            <svg x-show="n.type === 'system'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456Z"/></svg>
                        </span>

                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-900" x-text="n.title"></p>
                            <p class="text-xs text-slate-500 mt-0.5 line-clamp-2" x-text="n.message"></p>
                            <p class="text-[11px] text-slate-400 mt-1" x-text="n.time"></p>
                        </div>

                        <!-- unread indicator -->
                        <span x-show="!n.read" class="w-2 h-2 rounded-full bg-indigo-500 shrink-0 mt-2"></span>
                    </div>
                </div>
            </template>
        </div>

        <a href="<?= url('/notifications') ?>" class="block mt-4 text-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition">
            View All Notifications &rarr;
        </a>
    </div>
</section>

<!-- ═══════════════════════════════════════════════
     Upcoming Deadlines Quick Strip
     ═══════════════════════════════════════════════ -->
<section class="mt-6 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
    <div class="flex items-center gap-2 mb-4">
        <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
        <h2 class="text-lg font-semibold text-slate-900">Upcoming Deadlines</h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Deadline 1 -->
        <div class="flex items-center gap-4 rounded-lg border border-rose-100 bg-rose-50/50 p-4">
            <div class="flex flex-col items-center justify-center w-12 h-12 rounded-lg bg-rose-100 text-rose-700 shrink-0">
                <span class="text-xs font-semibold uppercase leading-none"><?= e(date('M', strtotime('+2 days'))) ?></span>
                <span class="text-lg font-bold leading-none mt-0.5"><?= e(date('d', strtotime('+2 days'))) ?></span>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium text-slate-900 truncate">Argumentative Essay</p>
                <p class="text-xs text-slate-500">English Composition 201</p>
            </div>
        </div>

        <!-- Deadline 2 -->
        <div class="flex items-center gap-4 rounded-lg border border-amber-100 bg-amber-50/50 p-4">
            <div class="flex flex-col items-center justify-center w-12 h-12 rounded-lg bg-amber-100 text-amber-700 shrink-0">
                <span class="text-xs font-semibold uppercase leading-none"><?= e(date('M', strtotime('+5 days'))) ?></span>
                <span class="text-lg font-bold leading-none mt-0.5"><?= e(date('d', strtotime('+5 days'))) ?></span>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium text-slate-900 truncate">Annotated Bibliography</p>
                <p class="text-xs text-slate-500">Environmental Science 110</p>
            </div>
        </div>

        <!-- Deadline 3 -->
        <div class="flex items-center gap-4 rounded-lg border border-blue-100 bg-blue-50/50 p-4">
            <div class="flex flex-col items-center justify-center w-12 h-12 rounded-lg bg-blue-100 text-blue-700 shrink-0">
                <span class="text-xs font-semibold uppercase leading-none"><?= e(date('M', strtotime('+9 days'))) ?></span>
                <span class="text-lg font-bold leading-none mt-0.5"><?= e(date('d', strtotime('+9 days'))) ?></span>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium text-slate-900 truncate">Midterm Research Paper</p>
                <p class="text-xs text-slate-500">History 210</p>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════
     Quick-Access Resources
     ═══════════════════════════════════════════════ -->
<section class="mt-6 mb-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <a href="<?= url('/writing-lab') ?>" class="group flex items-center gap-4 bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md hover:border-indigo-200 transition">
        <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 group-hover:bg-indigo-100 transition shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
        </span>
        <div>
            <p class="text-sm font-semibold text-slate-900 group-hover:text-indigo-700 transition">Writing Lab</p>
            <p class="text-xs text-slate-500">Practice &amp; drills</p>
        </div>
    </a>

    <a href="<?= url('/ai-assistant') ?>" class="group flex items-center gap-4 bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md hover:border-sky-200 transition">
        <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-sky-50 text-sky-600 group-hover:bg-sky-100 transition shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456Z"/></svg>
        </span>
        <div>
            <p class="text-sm font-semibold text-slate-900 group-hover:text-sky-700 transition">AI Assistant</p>
            <p class="text-xs text-slate-500">Get writing help</p>
        </div>
    </a>

    <a href="<?= url('/library') ?>" class="group flex items-center gap-4 bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md hover:border-amber-200 transition">
        <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-amber-50 text-amber-600 group-hover:bg-amber-100 transition shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
        </span>
        <div>
            <p class="text-sm font-semibold text-slate-900 group-hover:text-amber-700 transition">Resource Library</p>
            <p class="text-xs text-slate-500">Guides &amp; templates</p>
        </div>
    </a>

    <a href="<?= url('/progress') ?>" class="group flex items-center gap-4 bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md hover:border-emerald-200 transition">
        <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-100 transition shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/></svg>
        </span>
        <div>
            <p class="text-sm font-semibold text-slate-900 group-hover:text-emerald-700 transition">My Progress</p>
            <p class="text-xs text-slate-500">Stats &amp; analytics</p>
        </div>
    </a>
</section>

<!-- ═══════════════════════════════════════════════
     Chart.js Initialization
     ═══════════════════════════════════════════════ -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    var ctx = document.getElementById('aiUsageChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: [
                'Grammar & Style Check',
                'Thesis Improvement',
                'Outline Generation',
                'Citation Formatting',
                'Paraphrasing Assistance'
            ],
            datasets: [{
                data: [34, 18, 12, 9, 7],
                backgroundColor: [
                    'rgba(99,  102, 241, 0.85)',   // indigo-500
                    'rgba(14,  165, 233, 0.85)',   // sky-500
                    'rgba(245, 158, 11,  0.85)',   // amber-500
                    'rgba(16,  185, 129, 0.85)',   // emerald-500
                    'rgba(244, 63,  94,  0.85)'    // rose-500
                ],
                borderColor: '#ffffff',
                borderWidth: 3,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { family: 'Inter, sans-serif', size: 13 },
                    bodyFont:  { family: 'Inter, sans-serif', size: 12 },
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function (context) {
                            var total = context.dataset.data.reduce(function (a, b) { return a + b; }, 0);
                            var pct   = Math.round((context.parsed / total) * 100);
                            return ' ' + context.parsed + ' sessions (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });
});
</script>
