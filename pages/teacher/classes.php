<?php
$pageTitle = 'Class Management';
$currentPage = 'teacher-classes';
$breadcrumbs = [
    ['label' => 'Home', 'url' => '/dashboard'],
    ['label' => 'Teacher', 'url' => '/teacher/dashboard'],
    ['label' => 'Classes']
];

$classes = [
    ['id' => 1, 'name' => 'English 101 - Section A', 'code' => 'ENG101A', 'students' => 28, 'assignments' => 12, 'avg_grade' => 87, 'letter' => 'B+', 'color' => 'blue', 'semester' => 'Spring 2024', 'schedule' => 'MWF 9:00-9:50 AM', 'room' => 'Hall 204', 'description' => 'Introduction to college-level writing focusing on essay composition, critical reading, and research skills.'],
    ['id' => 2, 'name' => 'English 101 - Section B', 'code' => 'ENG101B', 'students' => 30, 'assignments' => 10, 'avg_grade' => 82, 'letter' => 'B', 'color' => 'indigo', 'semester' => 'Spring 2024', 'schedule' => 'MWF 10:00-10:50 AM', 'room' => 'Hall 204', 'description' => 'Introduction to college-level writing with emphasis on argumentative essays and source evaluation.'],
    ['id' => 3, 'name' => 'History 201', 'code' => 'HIST201', 'students' => 25, 'assignments' => 8, 'avg_grade' => 79, 'letter' => 'B-', 'color' => 'green', 'semester' => 'Spring 2024', 'schedule' => 'TTh 1:00-2:15 PM', 'room' => 'Liberal Arts 310', 'description' => 'Survey of American history from the Civil War to the present, exploring political, social, and cultural developments.'],
    ['id' => 4, 'name' => 'Biology 110', 'code' => 'BIO110', 'students' => 32, 'assignments' => 15, 'avg_grade' => 91, 'letter' => 'A-', 'color' => 'yellow', 'semester' => 'Spring 2024', 'schedule' => 'TTh 9:30-10:45 AM', 'room' => 'Science 105', 'description' => 'Introductory biology covering cell biology, genetics, evolution, and ecology with weekly lab sessions.'],
    ['id' => 5, 'name' => 'Psychology 101', 'code' => 'PSY101', 'students' => 27, 'assignments' => 9, 'avg_grade' => 93, 'letter' => 'A', 'color' => 'purple', 'semester' => 'Spring 2024', 'schedule' => 'MWF 2:00-2:50 PM', 'room' => 'Behavioral Sci 201', 'description' => 'Introduction to psychological science covering cognition, development, personality, and social behavior.'],
];

$classStudents = [
    ['name' => 'Alice Johnson', 'email' => 'ajohnson@edu.com', 'grade' => 'A', 'completed' => 11, 'flags' => 0],
    ['name' => 'Bob Martinez', 'email' => 'bmartinez@edu.com', 'grade' => 'C+', 'completed' => 9, 'flags' => 2],
    ['name' => 'Carol Davis', 'email' => 'cdavis@edu.com', 'grade' => 'B+', 'completed' => 12, 'flags' => 0],
    ['name' => 'David Lee', 'email' => 'dlee@edu.com', 'grade' => 'B', 'completed' => 10, 'flags' => 1],
    ['name' => 'Emma Wilson', 'email' => 'ewilson@edu.com', 'grade' => 'A-', 'completed' => 12, 'flags' => 1],
];

$classAssignments = [
    ['title' => 'Essay: Climate Change', 'due' => '2024-01-20', 'submissions' => 24, 'total' => 28, 'status' => 'Active'],
    ['title' => 'Research Paper Draft', 'due' => '2024-01-25', 'submissions' => 10, 'total' => 28, 'status' => 'Active'],
    ['title' => 'Book Report Ch.5', 'due' => '2024-01-10', 'submissions' => 28, 'total' => 28, 'status' => 'Closed'],
    ['title' => 'Grammar Quiz #4', 'due' => '2024-01-05', 'submissions' => 27, 'total' => 28, 'status' => 'Graded'],
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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ showCreateModal: false, expandedClass: null }">

        <!-- Page Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900"><?= htmlspecialchars($pageTitle) ?></h1>
                <p class="mt-1 text-sm text-gray-500">Manage your classes, students, and course materials.</p>
            </div>
            <button @click="showCreateModal = true" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create Class
            </button>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg border border-gray-200 p-4 text-center">
                <p class="text-2xl font-bold text-blue-600"><?= count($classes) ?></p>
                <p class="text-sm text-gray-500">Total Classes</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4 text-center">
                <p class="text-2xl font-bold text-green-600"><?= array_sum(array_column($classes, 'students')) ?></p>
                <p class="text-sm text-gray-500">Total Students</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4 text-center">
                <p class="text-2xl font-bold text-purple-600"><?= array_sum(array_column($classes, 'assignments')) ?></p>
                <p class="text-sm text-gray-500">Total Assignments</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4 text-center">
                <p class="text-2xl font-bold text-indigo-600"><?= round(array_sum(array_column($classes, 'avg_grade')) / count($classes)) ?>%</p>
                <p class="text-sm text-gray-500">Overall Avg Grade</p>
            </div>
        </div>

        <!-- Class Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php foreach ($classes as $cls): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    <!-- Card Header -->
                    <div class="bg-gradient-to-r from-<?= $cls['color'] ?>-500 to-<?= $cls['color'] ?>-600 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <span class="text-white/80 text-xs font-medium bg-white/20 px-2 py-0.5 rounded"><?= $cls['code'] ?></span>
                            <span class="text-white/80 text-xs"><?= $cls['semester'] ?></span>
                        </div>
                        <h3 class="text-white font-semibold text-lg mt-2"><?= $cls['name'] ?></h3>
                        <p class="text-white/70 text-xs mt-1"><?= $cls['schedule'] ?> · <?= $cls['room'] ?></p>
                    </div>
                    <!-- Card Body -->
                    <div class="px-6 py-4">
                        <p class="text-sm text-gray-600 mb-4 line-clamp-2"><?= $cls['description'] ?></p>
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="text-center">
                                <p class="text-xl font-bold text-gray-900"><?= $cls['students'] ?></p>
                                <p class="text-xs text-gray-500">Students</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xl font-bold text-gray-900"><?= $cls['assignments'] ?></p>
                                <p class="text-xs text-gray-500">Assignments</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xl font-bold text-gray-900"><?= $cls['letter'] ?></p>
                                <p class="text-xs text-gray-500">Avg Grade</p>
                            </div>
                        </div>
                        <!-- Grade Progress Bar -->
                        <div class="mb-4">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>Class Average</span>
                                <span><?= $cls['avg_grade'] ?>%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-<?= $cls['color'] ?>-500 h-2 rounded-full" style="width: <?= $cls['avg_grade'] ?>%"></div>
                            </div>
                        </div>
                        <!-- Actions -->
                        <div class="flex space-x-2">
                            <button @click="expandedClass = expandedClass === <?= $cls['id'] ?> ? null : <?= $cls['id'] ?>" class="flex-1 px-3 py-2 bg-<?= $cls['color'] ?>-50 text-<?= $cls['color'] ?>-700 rounded-lg text-sm font-medium hover:bg-<?= $cls['color'] ?>-100 transition">
                                <span x-text="expandedClass === <?= $cls['id'] ?> ? 'Hide Details' : 'View Details'">View Details</span>
                            </button>
                            <button class="px-3 py-2 bg-gray-50 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Class Detail Expandable Sections -->
        <?php foreach ($classes as $cls): ?>
            <div x-show="expandedClass === <?= $cls['id'] ?>" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" class="mb-8 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-<?= $cls['color'] ?>-50 px-6 py-4 border-b border-<?= $cls['color'] ?>-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900"><?= $cls['name'] ?> — Details</h3>
                        <p class="text-sm text-gray-500"><?= $cls['schedule'] ?> · <?= $cls['room'] ?> · <?= $cls['semester'] ?></p>
                    </div>
                    <button @click="expandedClass = null" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Tabs within expanded section -->
                <div x-data="{ activeTab: 'students' }" class="px-6 py-4">
                    <div class="flex space-x-4 border-b border-gray-200 mb-4">
                        <button @click="activeTab = 'students'" :class="activeTab === 'students' ? 'border-<?= $cls['color'] ?>-500 text-<?= $cls['color'] ?>-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-2 border-b-2 text-sm font-medium transition">Students (<?= $cls['students'] ?>)</button>
                        <button @click="activeTab = 'assignments'" :class="activeTab === 'assignments' ? 'border-<?= $cls['color'] ?>-500 text-<?= $cls['color'] ?>-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-2 border-b-2 text-sm font-medium transition">Assignments (<?= $cls['assignments'] ?>)</button>
                        <button @click="activeTab = 'settings'" :class="activeTab === 'settings' ? 'border-<?= $cls['color'] ?>-500 text-<?= $cls['color'] ?>-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-2 border-b-2 text-sm font-medium transition">Settings</button>
                    </div>

                    <!-- Students Tab -->
                    <div x-show="activeTab === 'students'">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Completed</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Flags</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($classStudents as $stu): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900"><?= $stu['name'] ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-500"><?= $stu['email'] ?></td>
                                        <td class="px-4 py-3 text-sm font-semibold text-gray-900"><?= $stu['grade'] ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-700"><?= $stu['completed'] ?>/<?= $cls['assignments'] ?></td>
                                        <td class="px-4 py-3">
                                            <?php if ($stu['flags'] > 0): ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800"><?= $stu['flags'] ?> flags</span>
                                            <?php else: ?>
                                                <span class="text-xs text-green-600">None</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">View</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Assignments Tab -->
                    <div x-show="activeTab === 'assignments'">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assignment</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submissions</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($classAssignments as $asgn): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900"><?= $asgn['title'] ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-500"><?= date('M j, Y', strtotime($asgn['due'])) ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-700"><?= $asgn['submissions'] ?>/<?= $asgn['total'] ?></td>
                                        <td class="px-4 py-3">
                                            <?php
                                                $sc = ['Active' => 'green', 'Closed' => 'gray', 'Graded' => 'blue'];
                                                $sColor = $sc[$asgn['status']] ?? 'gray';
                                            ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-<?= $sColor ?>-100 text-<?= $sColor ?>-800"><?= $asgn['status'] ?></span>
                                        </td>
                                        <td class="px-4 py-3 flex space-x-2">
                                            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</button>
                                            <button class="text-gray-500 hover:text-gray-700 text-sm font-medium">Grade</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Settings Tab -->
                    <div x-show="activeTab === 'settings'" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Class Name</label>
                                <input type="text" value="<?= $cls['name'] ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Class Code</label>
                                <input type="text" value="<?= $cls['code'] ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Schedule</label>
                                <input type="text" value="<?= $cls['schedule'] ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Room</label>
                                <input type="text" value="<?= $cls['room'] ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= $cls['description'] ?></textarea>
                        </div>
                        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                            <button class="px-4 py-2 text-red-600 hover:text-red-800 text-sm font-medium">Archive Class</button>
                            <button class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Create Class Modal -->
        <div x-show="showCreateModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50" @click="showCreateModal = false"></div>
                <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 z-10" @click.away="showCreateModal = false">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-gray-900">Create New Class</h3>
                        <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <form class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Class Name *</label>
                            <input type="text" placeholder="e.g., English 102 - Section A" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Class Code *</label>
                                <input type="text" placeholder="e.g., ENG102A" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Semester *</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option>Spring 2024</option>
                                    <option>Summer 2024</option>
                                    <option>Fall 2024</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Schedule</label>
                                <input type="text" placeholder="e.g., MWF 9:00-9:50 AM" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Room</label>
                                <input type="text" placeholder="e.g., Hall 204" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea rows="3" placeholder="Brief description of the class..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">AI Detection Sensitivity</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="high">High — Flag at 30%+ AI probability</option>
                                <option value="medium" selected>Medium — Flag at 50%+ AI probability</option>
                                <option value="low">Low — Flag at 70%+ AI probability</option>
                            </select>
                        </div>
                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                            <button type="button" @click="showCreateModal = false" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Cancel</button>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">Create Class</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
