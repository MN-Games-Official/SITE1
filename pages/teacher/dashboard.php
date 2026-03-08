<?php
$pageTitle = 'Teacher Dashboard';
$currentPage = 'teacher-dashboard';
$breadcrumbs = [
    ['label' => 'Home', 'url' => '/dashboard'],
    ['label' => 'Teacher Dashboard']
];

$stats = [
    ['title' => 'Total Students', 'value' => 142, 'icon' => 'users', 'color' => 'blue', 'change' => '+12 this month'],
    ['title' => 'Active Classes', 'value' => 6, 'icon' => 'book-open', 'color' => 'green', 'change' => '2 new this semester'],
    ['title' => 'Assignments', 'value' => 38, 'icon' => 'clipboard-list', 'color' => 'purple', 'change' => '5 pending review'],
    ['title' => 'AI Flags', 'value' => 14, 'icon' => 'flag', 'color' => 'red', 'change' => '3 high severity'],
];

$recentSubmissions = [
    ['student' => 'Alice Johnson', 'assignment' => 'Essay: Climate Change', 'class' => 'English 101', 'date' => '2024-01-15 09:30', 'status' => 'Submitted', 'ai_score' => 12],
    ['student' => 'Bob Martinez', 'assignment' => 'Research Paper Draft', 'class' => 'History 201', 'date' => '2024-01-15 08:45', 'status' => 'Late', 'ai_score' => 78],
    ['student' => 'Carol Davis', 'assignment' => 'Book Report Ch.5', 'class' => 'English 101', 'date' => '2024-01-14 16:20', 'status' => 'Submitted', 'ai_score' => 5],
    ['student' => 'David Lee', 'assignment' => 'Lab Report #3', 'class' => 'Biology 110', 'date' => '2024-01-14 15:10', 'status' => 'Graded', 'ai_score' => 22],
    ['student' => 'Emma Wilson', 'assignment' => 'Essay: Climate Change', 'class' => 'English 101', 'date' => '2024-01-14 14:55', 'status' => 'Submitted', 'ai_score' => 88],
    ['student' => 'Frank Thomas', 'assignment' => 'Reflection Journal', 'class' => 'Psychology 101', 'date' => '2024-01-14 13:30', 'status' => 'Submitted', 'ai_score' => 3],
    ['student' => 'Grace Kim', 'assignment' => 'Argument Essay', 'class' => 'English 101', 'date' => '2024-01-14 12:00', 'status' => 'Graded', 'ai_score' => 45],
    ['student' => 'Henry Patel', 'assignment' => 'History Analysis', 'class' => 'History 201', 'date' => '2024-01-13 17:45', 'status' => 'Submitted', 'ai_score' => 62],
];

$flaggedItems = [
    ['student' => 'Bob Martinez', 'assignment' => 'Research Paper Draft', 'severity' => 'high', 'reason' => 'AI content detected (78%)', 'date' => '2024-01-15'],
    ['student' => 'Emma Wilson', 'assignment' => 'Essay: Climate Change', 'severity' => 'high', 'reason' => 'AI content detected (88%)', 'date' => '2024-01-14'],
    ['student' => 'Henry Patel', 'assignment' => 'History Analysis', 'severity' => 'medium', 'reason' => 'AI content detected (62%)', 'date' => '2024-01-13'],
    ['student' => 'Grace Kim', 'assignment' => 'Argument Essay', 'severity' => 'medium', 'reason' => 'AI content detected (45%)', 'date' => '2024-01-14'],
    ['student' => 'David Lee', 'assignment' => 'Lab Report #3', 'severity' => 'low', 'reason' => 'Stylistic anomaly detected', 'date' => '2024-01-14'],
];

$classOverview = [
    ['name' => 'English 101 - Section A', 'students' => 28, 'pending' => 12, 'avg_grade' => 'B+', 'color' => 'blue'],
    ['name' => 'English 101 - Section B', 'students' => 30, 'pending' => 8, 'avg_grade' => 'B', 'color' => 'indigo'],
    ['name' => 'History 201', 'students' => 25, 'pending' => 15, 'avg_grade' => 'B-', 'color' => 'green'],
    ['name' => 'Biology 110', 'students' => 32, 'pending' => 5, 'avg_grade' => 'A-', 'color' => 'yellow'],
    ['name' => 'Psychology 101', 'students' => 27, 'pending' => 3, 'avg_grade' => 'A', 'color' => 'purple'],
];

$aiChartLabels = json_encode(['Week 1','Week 2','Week 3','Week 4','Week 5','Week 6','Week 7','Week 8']);
$aiChartHigh = json_encode([2, 3, 1, 4, 2, 5, 3, 3]);
$aiChartMedium = json_encode([5, 4, 6, 3, 7, 4, 5, 6]);
$aiChartLow = json_encode([8, 6, 9, 7, 5, 8, 6, 7]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Page Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900"><?= htmlspecialchars($pageTitle) ?></h1>
                <p class="mt-1 text-sm text-gray-500">Welcome back! Here's an overview of your classes and students.</p>
            </div>
            <div class="flex space-x-3">
                <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    <span class="mr-1">📊</span> Export Report
                </button>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                    <span class="mr-1">➕</span> New Assignment
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <?php foreach ($stats as $stat): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500"><?= $stat['title'] ?></p>
                            <p class="mt-2 text-3xl font-bold text-gray-900"><?= $stat['value'] ?></p>
                            <p class="mt-1 text-xs text-<?= $stat['color'] ?>-600"><?= $stat['change'] ?></p>
                        </div>
                        <div class="w-12 h-12 bg-<?= $stat['color'] ?>-100 rounded-xl flex items-center justify-center">
                            <?php if ($stat['icon'] === 'users'): ?>
                                <svg class="w-6 h-6 text-<?= $stat['color'] ?>-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg>
                            <?php elseif ($stat['icon'] === 'book-open'): ?>
                                <svg class="w-6 h-6 text-<?= $stat['color'] ?>-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            <?php elseif ($stat['icon'] === 'clipboard-list'): ?>
                                <svg class="w-6 h-6 text-<?= $stat['color'] ?>-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            <?php else: ?>
                                <svg class="w-6 h-6 text-<?= $stat['color'] ?>-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm0 0h9"></path></svg>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

            <!-- Recent Submissions Table -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Submissions</h2>
                    <a href="/teacher/assignments" class="text-sm text-blue-600 hover:text-blue-800">View All →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assignment</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AI Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($recentSubmissions as $sub): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-sm mr-3">
                                                <?= strtoupper(substr($sub['student'], 0, 1)) ?>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900"><?= $sub['student'] ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= $sub['assignment'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $sub['class'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                            $statusColors = ['Submitted' => 'green', 'Late' => 'yellow', 'Graded' => 'blue'];
                                            $color = $statusColors[$sub['status']] ?? 'gray';
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-<?= $color ?>-100 text-<?= $color ?>-800">
                                            <?= $sub['status'] ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                            $aiColor = $sub['ai_score'] >= 60 ? 'red' : ($sub['ai_score'] >= 30 ? 'yellow' : 'green');
                                        ?>
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-<?= $aiColor ?>-500 h-2 rounded-full" style="width: <?= $sub['ai_score'] ?>%"></div>
                                            </div>
                                            <span class="text-sm text-<?= $aiColor ?>-700 font-medium"><?= $sub['ai_score'] ?>%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('M j, g:ia', strtotime($sub['date'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Flagged Items Sidebar -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">🚩 Flagged Items</h2>
                    <a href="/teacher/flags" class="text-sm text-blue-600 hover:text-blue-800">View All →</a>
                </div>
                <div class="divide-y divide-gray-200">
                    <?php foreach ($flaggedItems as $flag): ?>
                        <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900"><?= $flag['student'] ?></p>
                                    <p class="text-xs text-gray-500 mt-0.5"><?= $flag['assignment'] ?></p>
                                    <p class="text-xs text-gray-400 mt-1"><?= $flag['reason'] ?></p>
                                </div>
                                <?php
                                    $sevColors = ['high' => 'red', 'medium' => 'yellow', 'low' => 'blue'];
                                    $sevColor = $sevColors[$flag['severity']];
                                ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-<?= $sevColor ?>-100 text-<?= $sevColor ?>-800 ml-2">
                                    <?= ucfirst($flag['severity']) ?>
                                </span>
                            </div>
                            <div class="mt-2 flex items-center space-x-2">
                                <button class="text-xs text-blue-600 hover:text-blue-800 font-medium">Review</button>
                                <span class="text-gray-300">|</span>
                                <button class="text-xs text-gray-500 hover:text-gray-700">Dismiss</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- AI Usage Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">AI Usage Trends</h2>
                    <p class="text-sm text-gray-500">Weekly AI detection flags across all classes</p>
                </div>
                <div class="flex items-center space-x-4 text-xs">
                    <span class="flex items-center"><span class="w-3 h-3 bg-red-500 rounded-full mr-1"></span> High</span>
                    <span class="flex items-center"><span class="w-3 h-3 bg-yellow-500 rounded-full mr-1"></span> Medium</span>
                    <span class="flex items-center"><span class="w-3 h-3 bg-blue-500 rounded-full mr-1"></span> Low</span>
                </div>
            </div>
            <div class="p-6">
                <canvas id="aiUsageChart" height="100"></canvas>
            </div>
        </div>

        <!-- Class Overview Cards -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Class Overview</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($classOverview as $cls): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-10 h-10 bg-<?= $cls['color'] ?>-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-<?= $cls['color'] ?>-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                            <span class="text-2xl font-bold text-gray-900"><?= $cls['avg_grade'] ?></span>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900"><?= $cls['name'] ?></h3>
                        <div class="mt-3 flex items-center justify-between text-sm text-gray-500">
                            <span><?= $cls['students'] ?> students</span>
                            <span><?= $cls['pending'] ?> pending</span>
                        </div>
                        <div class="mt-3 w-full bg-gray-200 rounded-full h-2">
                            <?php $pct = round((($cls['students'] - $cls['pending']) / $cls['students']) * 100); ?>
                            <div class="bg-<?= $cls['color'] ?>-500 h-2 rounded-full" style="width: <?= $pct ?>%"></div>
                        </div>
                        <p class="mt-1 text-xs text-gray-400"><?= $pct ?>% assignments graded</p>
                        <div class="mt-4 flex space-x-2">
                            <a href="/teacher/classes/<?= strtolower(str_replace(' ', '-', $cls['name'])) ?>" class="flex-1 text-center px-3 py-1.5 bg-<?= $cls['color'] ?>-50 text-<?= $cls['color'] ?>-700 rounded-lg text-xs font-medium hover:bg-<?= $cls['color'] ?>-100 transition">View Class</a>
                            <button class="px-3 py-1.5 bg-gray-50 text-gray-600 rounded-lg text-xs font-medium hover:bg-gray-100 transition">Manage</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Quick Actions Footer -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Quick Actions</h3>
                    <p class="text-blue-100 text-sm mt-1">Common tasks to manage your classes efficiently</p>
                </div>
                <div class="flex space-x-3">
                    <button class="px-4 py-2 bg-white/20 backdrop-blur rounded-lg text-sm font-medium hover:bg-white/30 transition">📝 Create Assignment</button>
                    <button class="px-4 py-2 bg-white/20 backdrop-blur rounded-lg text-sm font-medium hover:bg-white/30 transition">👥 Add Students</button>
                    <button class="px-4 py-2 bg-white/20 backdrop-blur rounded-lg text-sm font-medium hover:bg-white/30 transition">📊 Generate Report</button>
                    <button class="px-4 py-2 bg-white/20 backdrop-blur rounded-lg text-sm font-medium hover:bg-white/30 transition">⚙️ Settings</button>
                </div>
            </div>
        </div>

    </div>

    <!-- Chart.js Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('aiUsageChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?= $aiChartLabels ?>,
                    datasets: [
                        {
                            label: 'High Severity',
                            data: <?= $aiChartHigh ?>,
                            backgroundColor: 'rgba(239, 68, 68, 0.8)',
                            borderColor: 'rgba(239, 68, 68, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                        },
                        {
                            label: 'Medium Severity',
                            data: <?= $aiChartMedium ?>,
                            backgroundColor: 'rgba(245, 158, 11, 0.8)',
                            borderColor: 'rgba(245, 158, 11, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                        },
                        {
                            label: 'Low Severity',
                            data: <?= $aiChartLow ?>,
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            grid: { display: false },
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { stepSize: 5 }
                        }
                    }
                }
            });
        });
    </script>

</body>
</html>
