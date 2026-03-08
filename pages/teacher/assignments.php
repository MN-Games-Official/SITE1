<?php
$pageTitle = 'Assignment Management';
$currentPage = 'teacher-assignments';
$breadcrumbs = [
    ['label' => 'Home', 'url' => '/dashboard'],
    ['label' => 'Teacher', 'url' => '/teacher/dashboard'],
    ['label' => 'Assignments']
];

$classes = [
    ['id' => 1, 'name' => 'English 101 - Section A'],
    ['id' => 2, 'name' => 'English 101 - Section B'],
    ['id' => 3, 'name' => 'History 201'],
    ['id' => 4, 'name' => 'Biology 110'],
    ['id' => 5, 'name' => 'Psychology 101'],
];

$assignments = [
    ['id' => 1, 'title' => 'Essay: Climate Change', 'class' => 'English 101 - Section A', 'due' => '2024-01-20', 'points' => 100, 'submissions' => 24, 'total' => 28, 'graded' => 18, 'status' => 'Active', 'ai_flags' => 3, 'avg_score' => 82],
    ['id' => 2, 'title' => 'Research Paper Draft', 'class' => 'History 201', 'due' => '2024-01-25', 'points' => 150, 'submissions' => 10, 'total' => 25, 'graded' => 0, 'status' => 'Active', 'ai_flags' => 2, 'avg_score' => null],
    ['id' => 3, 'title' => 'Book Report Ch.5', 'class' => 'English 101 - Section A', 'due' => '2024-01-10', 'points' => 50, 'submissions' => 28, 'total' => 28, 'graded' => 28, 'status' => 'Graded', 'ai_flags' => 1, 'avg_score' => 88],
    ['id' => 4, 'title' => 'Lab Report #3', 'class' => 'Biology 110', 'due' => '2024-01-18', 'points' => 75, 'submissions' => 30, 'total' => 32, 'graded' => 25, 'status' => 'Active', 'ai_flags' => 1, 'avg_score' => 76],
    ['id' => 5, 'title' => 'Reflection Journal', 'class' => 'Psychology 101', 'due' => '2024-01-22', 'points' => 25, 'submissions' => 22, 'total' => 27, 'graded' => 0, 'status' => 'Active', 'ai_flags' => 0, 'avg_score' => null],
    ['id' => 6, 'title' => 'Argument Essay', 'class' => 'English 101 - Section B', 'due' => '2024-01-15', 'points' => 100, 'submissions' => 30, 'total' => 30, 'graded' => 30, 'status' => 'Graded', 'ai_flags' => 4, 'avg_score' => 79],
    ['id' => 7, 'title' => 'Grammar Quiz #4', 'class' => 'English 101 - Section A', 'due' => '2024-01-05', 'points' => 20, 'submissions' => 27, 'total' => 28, 'graded' => 27, 'status' => 'Graded', 'ai_flags' => 0, 'avg_score' => 91],
    ['id' => 8, 'title' => 'History Analysis', 'class' => 'History 201', 'due' => '2024-01-28', 'points' => 120, 'submissions' => 5, 'total' => 25, 'graded' => 0, 'status' => 'Active', 'ai_flags' => 1, 'avg_score' => null],
    ['id' => 9, 'title' => 'Cell Biology Worksheet', 'class' => 'Biology 110', 'due' => '2024-02-01', 'points' => 30, 'submissions' => 0, 'total' => 32, 'graded' => 0, 'status' => 'Draft', 'ai_flags' => 0, 'avg_score' => null],
    ['id' => 10, 'title' => 'Persuasive Speech Outline', 'class' => 'English 101 - Section B', 'due' => '2024-02-05', 'points' => 50, 'submissions' => 0, 'total' => 30, 'graded' => 0, 'status' => 'Scheduled', 'ai_flags' => 0, 'avg_score' => null],
];

$gradingOverview = [
    ['class' => 'English 101 - Section A', 'pending' => 6, 'graded' => 73, 'total' => 79, 'avg' => 85],
    ['class' => 'English 101 - Section B', 'pending' => 0, 'graded' => 60, 'total' => 60, 'avg' => 79],
    ['class' => 'History 201', 'pending' => 15, 'graded' => 35, 'total' => 50, 'avg' => 77],
    ['class' => 'Biology 110', 'pending' => 7, 'graded' => 53, 'total' => 60, 'avg' => 83],
    ['class' => 'Psychology 101', 'pending' => 22, 'graded' => 20, 'total' => 42, 'avg' => 90],
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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ showCreateModal: false, filterClass: 'all', filterStatus: 'all' }">

        <!-- Page Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900"><?= htmlspecialchars($pageTitle) ?></h1>
                <p class="mt-1 text-sm text-gray-500">Create, manage, and grade assignments across all your classes.</p>
            </div>
            <button @click="showCreateModal = true" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create Assignment
            </button>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6 flex flex-wrap items-center gap-4">
            <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-700">Class:</label>
                <select x-model="filterClass" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="all">All Classes</option>
                    <?php foreach ($classes as $cls): ?>
                        <option value="<?= $cls['name'] ?>"><?= $cls['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-700">Status:</label>
                <select x-model="filterStatus" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="all">All Statuses</option>
                    <option value="Active">Active</option>
                    <option value="Graded">Graded</option>
                    <option value="Draft">Draft</option>
                    <option value="Scheduled">Scheduled</option>
                </select>
            </div>
            <div class="flex-1"></div>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <span>Showing</span>
                <span class="font-semibold text-gray-900"><?= count($assignments) ?></span>
                <span>assignments</span>
            </div>
        </div>

        <!-- Assignments Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assignment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Points</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submissions</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Graded</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AI Flags</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($assignments as $a): ?>
                            <tr class="hover:bg-gray-50 transition-colors"
                                x-show="(filterClass === 'all' || filterClass === '<?= addslashes($a['class']) ?>') && (filterStatus === 'all' || filterStatus === '<?= $a['status'] ?>')">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900"><?= $a['title'] ?></p>
                                        <p class="text-xs text-gray-400">ID: <?= $a['id'] ?></p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= $a['class'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php
                                        $dueDate = strtotime($a['due']);
                                        $isPast = $dueDate < time();
                                    ?>
                                    <span class="<?= $isPast ? 'text-gray-400' : 'text-gray-700' ?>"><?= date('M j, Y', $dueDate) ?></span>
                                    <?php if (!$isPast && $a['status'] === 'Active'): ?>
                                        <p class="text-xs text-orange-500 mt-0.5"><?= ceil(($dueDate - time()) / 86400) ?> days left</p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $a['points'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <?php $subPct = $a['total'] > 0 ? round(($a['submissions'] / $a['total']) * 100) : 0; ?>
                                            <div class="bg-blue-500 h-2 rounded-full" style="width: <?= $subPct ?>%"></div>
                                        </div>
                                        <span class="text-sm text-gray-700"><?= $a['submissions'] ?>/<?= $a['total'] ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-700"><?= $a['graded'] ?>/<?= $a['submissions'] ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                        $statusColors = ['Active' => 'green', 'Graded' => 'blue', 'Draft' => 'gray', 'Scheduled' => 'yellow'];
                                        $sColor = $statusColors[$a['status']] ?? 'gray';
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-<?= $sColor ?>-100 text-<?= $sColor ?>-800"><?= $a['status'] ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($a['ai_flags'] > 0): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                            🚩 <?= $a['ai_flags'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-xs text-green-600">✓ None</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php if ($a['avg_score'] !== null): ?>
                                        <span class="font-semibold text-gray-900"><?= $a['avg_score'] ?>%</span>
                                    <?php else: ?>
                                        <span class="text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">View</button>
                                        <button class="text-gray-500 hover:text-gray-700 text-sm font-medium">Edit</button>
                                        <?php if ($a['graded'] < $a['submissions']): ?>
                                            <button class="text-green-600 hover:text-green-800 text-sm font-medium">Grade</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Grading Overview Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Grading Overview</h2>
                <p class="text-sm text-gray-500">Summary of grading progress across all classes</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php
                        $gradingColors = ['blue', 'indigo', 'green', 'yellow', 'purple'];
                        foreach ($gradingOverview as $i => $g):
                            $color = $gradingColors[$i % count($gradingColors)];
                            $pct = $g['total'] > 0 ? round(($g['graded'] / $g['total']) * 100) : 0;
                    ?>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-semibold text-gray-900"><?= $g['class'] ?></h4>
                                <span class="text-xs bg-<?= $color ?>-100 text-<?= $color ?>-700 px-2 py-0.5 rounded font-medium">Avg: <?= $g['avg'] ?>%</span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                <span><?= $g['graded'] ?> of <?= $g['total'] ?> graded</span>
                                <span><?= $pct ?>%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-3">
                                <div class="bg-<?= $color ?>-500 h-2 rounded-full" style="width: <?= $pct ?>%"></div>
                            </div>
                            <?php if ($g['pending'] > 0): ?>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-orange-600 font-medium"><?= $g['pending'] ?> pending review</span>
                                    <button class="text-xs text-blue-600 hover:text-blue-800 font-medium">Grade Now →</button>
                                </div>
                            <?php else: ?>
                                <div class="text-xs text-green-600 font-medium">✓ All graded</div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-5 text-white">
                <p class="text-blue-100 text-sm">Total Assignments</p>
                <p class="text-3xl font-bold mt-1"><?= count($assignments) ?></p>
                <p class="text-blue-200 text-xs mt-2">Across <?= count($classes) ?> classes</p>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-5 text-white">
                <p class="text-green-100 text-sm">Active Now</p>
                <p class="text-3xl font-bold mt-1"><?= count(array_filter($assignments, fn($a) => $a['status'] === 'Active')) ?></p>
                <p class="text-green-200 text-xs mt-2">Currently accepting submissions</p>
            </div>
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-5 text-white">
                <p class="text-purple-100 text-sm">Needs Grading</p>
                <p class="text-3xl font-bold mt-1"><?= array_sum(array_map(fn($a) => $a['submissions'] - $a['graded'], $assignments)) ?></p>
                <p class="text-purple-200 text-xs mt-2">Submissions awaiting review</p>
            </div>
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-5 text-white">
                <p class="text-red-100 text-sm">AI Flags</p>
                <p class="text-3xl font-bold mt-1"><?= array_sum(array_column($assignments, 'ai_flags')) ?></p>
                <p class="text-red-200 text-xs mt-2">Across all assignments</p>
            </div>
        </div>

        <!-- Create Assignment Modal -->
        <div x-show="showCreateModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div class="fixed inset-0 bg-black/50" @click="showCreateModal = false"></div>
                <div class="relative bg-white rounded-2xl shadow-xl max-w-2xl w-full p-6 z-10 max-h-[90vh] overflow-y-auto" @click.away="showCreateModal = false">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-gray-900">Create New Assignment</h3>
                        <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <form class="space-y-5" x-data="{ assignmentType: 'essay' }">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                            <input type="text" placeholder="e.g., Essay: The Impact of Technology on Society" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea rows="4" placeholder="Provide detailed instructions for the assignment..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Class *</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Select a class...</option>
                                    <?php foreach ($classes as $cls): ?>
                                        <option value="<?= $cls['id'] ?>"><?= $cls['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Assignment Type</label>
                                <select x-model="assignmentType" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="essay">Essay</option>
                                    <option value="research">Research Paper</option>
                                    <option value="report">Report</option>
                                    <option value="quiz">Quiz</option>
                                    <option value="journal">Journal / Reflection</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Due Date *</label>
                                <input type="datetime-local" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Points *</label>
                                <input type="number" min="1" max="1000" placeholder="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rubric</label>
                            <textarea rows="6" placeholder="Define grading criteria...&#10;&#10;Example:&#10;- Content & Argument (40 pts): Clear thesis, supporting evidence, logical structure&#10;- Writing Quality (30 pts): Grammar, clarity, vocabulary, flow&#10;- Research & Sources (20 pts): Quality and variety of sources, proper citations&#10;- Formatting (10 pts): MLA/APA format, title page, works cited" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono"></textarea>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">AI Detection Settings</h4>
                            <div class="space-y-2">
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">Enable AI content detection</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">Auto-flag submissions above threshold</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">Allow AI-assisted writing (with disclosure)</span>
                                </label>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Late Submission Policy</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="none">No late submissions</option>
                                    <option value="penalty" selected>Accept with penalty</option>
                                    <option value="accept">Accept without penalty</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Penalty per Day (%)</label>
                                <input type="number" min="0" max="100" value="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                            <button type="button" @click="showCreateModal = false" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Cancel</button>
                            <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Save as Draft</button>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">Create & Publish</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
