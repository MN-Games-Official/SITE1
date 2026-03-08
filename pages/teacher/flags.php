<?php
$pageTitle = 'AI Integrity Flags';
$currentPage = 'teacher-flags';
$breadcrumbs = [
    ['label' => 'Home', 'url' => '/dashboard'],
    ['label' => 'Teacher', 'url' => '/teacher/dashboard'],
    ['label' => 'AI Flags']
];

$flags = [
    [
        'id' => 1, 'student' => 'Emma Wilson', 'email' => 'ewilson@university.edu',
        'assignment' => 'Essay: Climate Change', 'class' => 'English 101 - Section B',
        'severity' => 'high', 'ai_score' => 88, 'status' => 'pending',
        'type' => 'ai_content', 'date' => '2024-01-14 14:55',
        'evidence' => 'Multiple paragraphs show high similarity to known AI-generated text patterns. Sentence structure uniformity detected at 92%. Vocabulary complexity inconsistent with prior submissions.',
        'details' => ['Pattern match: 88%', 'Perplexity score: 12.3 (low)', 'Burstiness: 0.15 (very low)', 'Style deviation: 4.2σ from baseline']
    ],
    [
        'id' => 2, 'student' => 'Bob Martinez', 'email' => 'bmartinez@university.edu',
        'assignment' => 'Research Paper Draft', 'class' => 'History 201',
        'severity' => 'high', 'ai_score' => 78, 'status' => 'pending',
        'type' => 'ai_content', 'date' => '2024-01-15 08:45',
        'evidence' => 'Research paper introduction and conclusion sections show strong AI-generation indicators. Middle sections appear human-written with noticeable style shift.',
        'details' => ['Pattern match: 78%', 'Perplexity score: 18.7 (low)', 'Burstiness: 0.22 (low)', 'Style deviation: 3.8σ from baseline']
    ],
    [
        'id' => 3, 'student' => 'Henry Patel', 'email' => 'hpatel@university.edu',
        'assignment' => 'History Analysis', 'class' => 'History 201',
        'severity' => 'medium', 'ai_score' => 62, 'status' => 'pending',
        'type' => 'ai_content', 'date' => '2024-01-13 17:45',
        'evidence' => 'Several paragraphs show characteristics of AI-assisted writing. The analysis sections contain unusually consistent sentence structures compared to student baseline.',
        'details' => ['Pattern match: 62%', 'Perplexity score: 24.1 (moderate)', 'Burstiness: 0.31 (moderate)', 'Style deviation: 2.5σ from baseline']
    ],
    [
        'id' => 4, 'student' => 'Grace Kim', 'email' => 'gkim@university.edu',
        'assignment' => 'Argument Essay', 'class' => 'English 101 - Section B',
        'severity' => 'medium', 'ai_score' => 45, 'status' => 'reviewed',
        'type' => 'style_anomaly', 'date' => '2024-01-14 12:00',
        'evidence' => 'Writing style shows moderate deviation from previous submissions. Vocabulary and sentence complexity elevated compared to baseline. May indicate AI assistance for specific sections.',
        'details' => ['Pattern match: 45%', 'Perplexity score: 30.5 (moderate)', 'Burstiness: 0.38 (moderate)', 'Style deviation: 2.1σ from baseline']
    ],
    [
        'id' => 5, 'student' => 'David Lee', 'email' => 'dlee@university.edu',
        'assignment' => 'Lab Report #3', 'class' => 'Biology 110',
        'severity' => 'low', 'ai_score' => 22, 'status' => 'dismissed',
        'type' => 'style_anomaly', 'date' => '2024-01-14 15:10',
        'evidence' => 'Minor stylistic anomalies detected in the methodology section. Likely due to using template language from lab manual rather than AI generation.',
        'details' => ['Pattern match: 22%', 'Perplexity score: 42.8 (normal)', 'Burstiness: 0.55 (normal)', 'Style deviation: 1.2σ from baseline']
    ],
    [
        'id' => 6, 'student' => 'Mia Garcia', 'email' => 'mgarcia@university.edu',
        'assignment' => 'Persuasive Essay', 'class' => 'English 101 - Section B',
        'severity' => 'high', 'ai_score' => 91, 'status' => 'pending',
        'type' => 'ai_content', 'date' => '2024-01-15 07:30',
        'evidence' => 'Entire essay exhibits hallmarks of AI generation. Extremely low perplexity and burstiness scores. Writing quality significantly exceeds prior baseline established over 8 submissions.',
        'details' => ['Pattern match: 91%', 'Perplexity score: 9.1 (very low)', 'Burstiness: 0.11 (very low)', 'Style deviation: 5.1σ from baseline']
    ],
    [
        'id' => 7, 'student' => 'Karen White', 'email' => 'kwhite@university.edu',
        'assignment' => 'Reflection Journal', 'class' => 'Psychology 101',
        'severity' => 'low', 'ai_score' => 28, 'status' => 'pending',
        'type' => 'paraphrase', 'date' => '2024-01-14 09:15',
        'evidence' => 'Some passages appear to be paraphrased from online sources or AI-generated text. The personal reflection portions appear authentic.',
        'details' => ['Pattern match: 28%', 'Perplexity score: 38.2 (moderate)', 'Burstiness: 0.48 (moderate)', 'Style deviation: 1.5σ from baseline']
    ],
    [
        'id' => 8, 'student' => 'Olivia Turner', 'email' => 'oturner@university.edu',
        'assignment' => 'Historical Essay', 'class' => 'History 201',
        'severity' => 'medium', 'ai_score' => 55, 'status' => 'pending',
        'type' => 'ai_content', 'date' => '2024-01-14 16:00',
        'evidence' => 'Introduction and thesis statement sections show AI-generation patterns. Body paragraphs appear partially human-written with AI enhancement.',
        'details' => ['Pattern match: 55%', 'Perplexity score: 27.3 (moderate)', 'Burstiness: 0.33 (moderate)', 'Style deviation: 2.3σ from baseline']
    ],
];

$typeLabels = [
    'ai_content' => 'AI Content',
    'style_anomaly' => 'Style Anomaly',
    'paraphrase' => 'Paraphrase Detection',
];

$trendSummary = [
    ['period' => 'This Week', 'high' => 3, 'medium' => 2, 'low' => 1, 'total' => 6],
    ['period' => 'Last Week', 'high' => 1, 'medium' => 3, 'low' => 2, 'total' => 6],
    ['period' => '2 Weeks Ago', 'high' => 2, 'medium' => 1, 'low' => 3, 'total' => 6],
    ['period' => '3 Weeks Ago', 'high' => 0, 'medium' => 2, 'low' => 1, 'total' => 3],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- Navigation Breadcrumbs -->
    <nav class="bg-white border-b border-gray-200 px-6 py-3">
        <div class="max-w-7xl mx-auto flex items-center space-x-2 text-sm text-gray-500">
            <?php foreach ($breadcrumbs as $i => $crumb): ?>
                <?php if ($i > 0): ?><span>/</span><?php endif; ?>
                <?php if (isset($crumb['url'])): ?>
                    <a href="<?= $crumb['url'] ?>" class="hover:text-blue-600"><?= $crumb['label'] ?></a>
                <?php else: ?>
                    <span class="text-gray-800 font-medium"><?= $crumb['label'] ?></span>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
         x-data="{
            filterType: 'all',
            filterSeverity: 'all',
            filterStatus: 'all',
            expandedFlag: null,
            toggleExpand(id) {
                this.expandedFlag = this.expandedFlag === id ? null : id;
            }
         }">

        <!-- Page Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">🚩 <?= htmlspecialchars($pageTitle) ?></h1>
                <p class="mt-1 text-sm text-gray-500">Review and manage AI integrity flags across all assignments.</p>
            </div>
            <div class="flex space-x-3">
                <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    📊 Generate Report
                </button>
                <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    ⚙️ Detection Settings
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-lg border border-gray-200 p-4 text-center">
                <p class="text-2xl font-bold text-gray-900"><?= count($flags) ?></p>
                <p class="text-xs text-gray-500">Total Flags</p>
            </div>
            <div class="bg-white rounded-lg border-2 border-red-200 p-4 text-center">
                <p class="text-2xl font-bold text-red-600"><?= count(array_filter($flags, fn($f) => $f['severity'] === 'high')) ?></p>
                <p class="text-xs text-red-500">High Severity</p>
            </div>
            <div class="bg-white rounded-lg border-2 border-yellow-200 p-4 text-center">
                <p class="text-2xl font-bold text-yellow-600"><?= count(array_filter($flags, fn($f) => $f['severity'] === 'medium')) ?></p>
                <p class="text-xs text-yellow-500">Medium Severity</p>
            </div>
            <div class="bg-white rounded-lg border-2 border-blue-200 p-4 text-center">
                <p class="text-2xl font-bold text-blue-600"><?= count(array_filter($flags, fn($f) => $f['severity'] === 'low')) ?></p>
                <p class="text-xs text-blue-500">Low Severity</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4 text-center">
                <p class="text-2xl font-bold text-orange-600"><?= count(array_filter($flags, fn($f) => $f['status'] === 'pending')) ?></p>
                <p class="text-xs text-gray-500">Pending Review</p>
            </div>
        </div>

        <!-- Trend Summary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Trend Summary</h2>
                <p class="text-sm text-gray-500">Flag frequency over recent weeks</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">High</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Medium</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Low</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trend</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($trendSummary as $i => $t): ?>
                            <tr class="hover:bg-gray-50 <?= $i === 0 ? 'bg-blue-50/50' : '' ?>">
                                <td class="px-6 py-3 text-sm font-medium text-gray-900"><?= $t['period'] ?></td>
                                <td class="px-6 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800"><?= $t['high'] ?></span></td>
                                <td class="px-6 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800"><?= $t['medium'] ?></span></td>
                                <td class="px-6 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800"><?= $t['low'] ?></span></td>
                                <td class="px-6 py-3 text-sm font-semibold text-gray-900"><?= $t['total'] ?></td>
                                <td class="px-6 py-3">
                                    <?php if ($i === 0): ?>
                                        <span class="text-red-500 text-sm">↑ Same as last week</span>
                                    <?php elseif ($i < count($trendSummary) - 1 && $t['total'] > $trendSummary[$i + 1]['total']): ?>
                                        <span class="text-red-500 text-sm">↑ Increased</span>
                                    <?php elseif ($i < count($trendSummary) - 1 && $t['total'] < $trendSummary[$i + 1]['total']): ?>
                                        <span class="text-green-500 text-sm">↓ Decreased</span>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-sm">— Baseline</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Type:</label>
                    <select x-model="filterType" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="all">All Types</option>
                        <option value="ai_content">AI Content</option>
                        <option value="style_anomaly">Style Anomaly</option>
                        <option value="paraphrase">Paraphrase</option>
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Severity:</label>
                    <select x-model="filterSeverity" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="all">All Severities</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Status:</label>
                    <select x-model="filterStatus" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="all">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="reviewed">Reviewed</option>
                        <option value="dismissed">Dismissed</option>
                    </select>
                </div>
                <div class="flex-1"></div>
                <button @click="filterType = 'all'; filterSeverity = 'all'; filterStatus = 'all'" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Clear Filters</button>
            </div>
        </div>

        <!-- Flag Cards -->
        <div class="space-y-4 mb-8">
            <?php foreach ($flags as $flag):
                $sevColors = ['high' => 'red', 'medium' => 'yellow', 'low' => 'blue'];
                $sevColor = $sevColors[$flag['severity']];
                $statusColors = ['pending' => 'orange', 'reviewed' => 'blue', 'dismissed' => 'gray'];
                $statusColor = $statusColors[$flag['status']];
            ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden"
                     x-show="(filterType === 'all' || filterType === '<?= $flag['type'] ?>') && (filterSeverity === 'all' || filterSeverity === '<?= $flag['severity'] ?>') && (filterStatus === 'all' || filterStatus === '<?= $flag['status'] ?>')">

                    <!-- Flag Card Header -->
                    <div class="px-6 py-4 flex items-start justify-between cursor-pointer hover:bg-gray-50 transition" @click="toggleExpand(<?= $flag['id'] ?>)">
                        <div class="flex items-start space-x-4">
                            <!-- Severity Indicator -->
                            <div class="w-10 h-10 bg-<?= $sevColor ?>-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <?php if ($flag['severity'] === 'high'): ?>
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                                <?php elseif ($flag['severity'] === 'medium'): ?>
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <?php else: ?>
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <?php endif; ?>
                            </div>
                            <!-- Flag Info -->
                            <div>
                                <div class="flex items-center space-x-2 mb-1">
                                    <h3 class="text-sm font-semibold text-gray-900"><?= $flag['student'] ?></h3>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-<?= $sevColor ?>-100 text-<?= $sevColor ?>-800"><?= ucfirst($flag['severity']) ?></span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-<?= $statusColor ?>-100 text-<?= $statusColor ?>-800"><?= ucfirst($flag['status']) ?></span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700"><?= $typeLabels[$flag['type']] ?></span>
                                </div>
                                <p class="text-sm text-gray-600"><?= $flag['assignment'] ?> — <span class="text-gray-400"><?= $flag['class'] ?></span></p>
                                <p class="text-xs text-gray-400 mt-1">AI Score: <span class="font-semibold text-<?= $sevColor ?>-600"><?= $flag['ai_score'] ?>%</span> · Flagged: <?= date('M j, Y g:ia', strtotime($flag['date'])) ?></p>
                            </div>
                        </div>
                        <!-- Expand Arrow -->
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <div class="w-12 h-12 relative">
                                    <svg class="w-12 h-12" viewBox="0 0 36 36">
                                        <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#e5e7eb" stroke-width="3"/>
                                        <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="<?= $flag['severity'] === 'high' ? '#ef4444' : ($flag['severity'] === 'medium' ? '#f59e0b' : '#3b82f6') ?>" stroke-width="3" stroke-dasharray="<?= $flag['ai_score'] ?>, 100" stroke-linecap="round"/>
                                    </svg>
                                    <span class="absolute inset-0 flex items-center justify-center text-xs font-bold text-gray-900"><?= $flag['ai_score'] ?>%</span>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="expandedFlag === <?= $flag['id'] ?> ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>

                    <!-- Expandable Evidence Details -->
                    <div x-show="expandedFlag === <?= $flag['id'] ?>" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100" class="border-t border-gray-200 bg-gray-50">
                        <div class="px-6 py-5 space-y-4">
                            <!-- Evidence Summary -->
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 mb-2">Evidence Summary</h4>
                                <p class="text-sm text-gray-700 bg-white p-4 rounded-lg border border-gray-200"><?= $flag['evidence'] ?></p>
                            </div>

                            <!-- Detection Details -->
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 mb-2">Detection Metrics</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    <?php foreach ($flag['details'] as $detail):
                                        $parts = explode(':', $detail, 2);
                                    ?>
                                        <div class="bg-white p-3 rounded-lg border border-gray-200">
                                            <p class="text-xs text-gray-500"><?= trim($parts[0]) ?></p>
                                            <p class="text-sm font-semibold text-gray-900 mt-0.5"><?= trim($parts[1]) ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Student Info -->
                            <div class="flex items-center justify-between bg-white p-4 rounded-lg border border-gray-200">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-700 font-semibold"><?= strtoupper(substr($flag['student'], 0, 1)) ?></div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900"><?= $flag['student'] ?></p>
                                        <p class="text-xs text-gray-500"><?= $flag['email'] ?> · <?= $flag['class'] ?></p>
                                    </div>
                                </div>
                                <a href="/teacher/students" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View Student Profile →</a>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-between pt-2">
                                <div class="flex space-x-2">
                                    <button class="text-sm text-gray-500 hover:text-gray-700 font-medium">📄 View Submission</button>
                                    <span class="text-gray-300">|</span>
                                    <button class="text-sm text-gray-500 hover:text-gray-700 font-medium">📧 Contact Student</button>
                                    <span class="text-gray-300">|</span>
                                    <button class="text-sm text-gray-500 hover:text-gray-700 font-medium">📋 View History</button>
                                </div>
                                <div class="flex space-x-3">
                                    <?php if ($flag['status'] === 'pending'): ?>
                                        <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                                            ❌ Dismiss Flag
                                        </button>
                                        <button class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition">
                                            ✅ Confirm & Approve Flag
                                        </button>
                                    <?php elseif ($flag['status'] === 'reviewed'): ?>
                                        <span class="px-4 py-2 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium">
                                            ✓ Reviewed
                                        </span>
                                    <?php else: ?>
                                        <span class="px-4 py-2 bg-gray-50 text-gray-500 rounded-lg text-sm font-medium">
                                            Dismissed
                                        </span>
                                        <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                                            🔄 Reopen
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Empty state for filters -->
        <div x-show="document.querySelectorAll('[x-show*=filterType]').length === 0" class="text-center py-12 bg-white rounded-xl border border-gray-200">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm0 0h9"></path></svg>
            <p class="text-gray-500 text-sm">No flags match your current filters.</p>
            <button @click="filterType = 'all'; filterSeverity = 'all'; filterStatus = 'all'" class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">Clear All Filters</button>
        </div>

        <!-- Bulk Actions Footer -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <p class="text-sm font-medium text-gray-900">Bulk Actions</p>
                    <button class="px-3 py-1.5 bg-green-50 text-green-700 rounded-lg text-xs font-medium hover:bg-green-100 transition">✅ Approve All Pending</button>
                    <button class="px-3 py-1.5 bg-gray-50 text-gray-600 rounded-lg text-xs font-medium hover:bg-gray-100 transition">❌ Dismiss All Low</button>
                    <button class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-medium hover:bg-blue-100 transition">📊 Export Flags Report</button>
                </div>
                <p class="text-xs text-gray-400"><?= count(array_filter($flags, fn($f) => $f['status'] === 'pending')) ?> flags pending review</p>
            </div>
        </div>

        <!-- Detection Info Banner -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-xl p-6 text-white">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-semibold">About AI Detection</h3>
                    <p class="text-indigo-100 text-sm mt-2 max-w-2xl">Our AI detection system uses multiple signals including perplexity analysis, burstiness scoring, pattern matching, and writing style comparison against each student's established baseline. No single metric should be used in isolation—always review the full evidence before taking action.</p>
                    <div class="mt-4 flex space-x-4 text-sm">
                        <div class="flex items-center"><span class="w-3 h-3 bg-red-400 rounded-full mr-2"></span>High: 60%+ AI probability</div>
                        <div class="flex items-center"><span class="w-3 h-3 bg-yellow-400 rounded-full mr-2"></span>Medium: 30-59% AI probability</div>
                        <div class="flex items-center"><span class="w-3 h-3 bg-blue-400 rounded-full mr-2"></span>Low: &lt;30% AI probability</div>
                    </div>
                </div>
                <button class="px-4 py-2 bg-white/20 backdrop-blur rounded-lg text-sm font-medium hover:bg-white/30 transition flex-shrink-0">Learn More</button>
            </div>
        </div>

    </div>
</body>
</html>
