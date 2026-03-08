<?php
$pageTitle = 'Student Management';
$currentPage = 'teacher-students';
$breadcrumbs = [
    ['label' => 'Home', 'url' => '/dashboard'],
    ['label' => 'Teacher', 'url' => '/teacher/dashboard'],
    ['label' => 'Students']
];

$classOptions = [
    'English 101 - Section A',
    'English 101 - Section B',
    'History 201',
    'Biology 110',
    'Psychology 101',
];

$students = [
    ['id' => 1, 'name' => 'Alice Johnson', 'email' => 'ajohnson@university.edu', 'class' => 'English 101 - Section A', 'completed' => 11, 'total' => 12, 'ai_usage' => 8, 'writing_score' => 92, 'flags' => 0, 'status' => 'Active', 'enrolled' => '2024-01-08', 'last_active' => '2024-01-15 09:30'],
    ['id' => 2, 'name' => 'Bob Martinez', 'email' => 'bmartinez@university.edu', 'class' => 'English 101 - Section A', 'completed' => 9, 'total' => 12, 'ai_usage' => 72, 'writing_score' => 65, 'flags' => 2, 'status' => 'Active', 'enrolled' => '2024-01-08', 'last_active' => '2024-01-15 08:45'],
    ['id' => 3, 'name' => 'Carol Davis', 'email' => 'cdavis@university.edu', 'class' => 'English 101 - Section A', 'completed' => 12, 'total' => 12, 'ai_usage' => 3, 'writing_score' => 88, 'flags' => 0, 'status' => 'Active', 'enrolled' => '2024-01-08', 'last_active' => '2024-01-14 16:20'],
    ['id' => 4, 'name' => 'David Lee', 'email' => 'dlee@university.edu', 'class' => 'Biology 110', 'completed' => 13, 'total' => 15, 'ai_usage' => 25, 'writing_score' => 78, 'flags' => 1, 'status' => 'Active', 'enrolled' => '2024-01-08', 'last_active' => '2024-01-14 15:10'],
    ['id' => 5, 'name' => 'Emma Wilson', 'email' => 'ewilson@university.edu', 'class' => 'English 101 - Section B', 'completed' => 10, 'total' => 10, 'ai_usage' => 85, 'writing_score' => 71, 'flags' => 3, 'status' => 'Active', 'enrolled' => '2024-01-08', 'last_active' => '2024-01-14 14:55'],
    ['id' => 6, 'name' => 'Frank Thomas', 'email' => 'fthomas@university.edu', 'class' => 'Psychology 101', 'completed' => 8, 'total' => 9, 'ai_usage' => 5, 'writing_score' => 95, 'flags' => 0, 'status' => 'Active', 'enrolled' => '2024-01-08', 'last_active' => '2024-01-14 13:30'],
    ['id' => 7, 'name' => 'Grace Kim', 'email' => 'gkim@university.edu', 'class' => 'English 101 - Section B', 'completed' => 10, 'total' => 10, 'ai_usage' => 42, 'writing_score' => 74, 'flags' => 1, 'status' => 'Active', 'enrolled' => '2024-01-08', 'last_active' => '2024-01-14 12:00'],
    ['id' => 8, 'name' => 'Henry Patel', 'email' => 'hpatel@university.edu', 'class' => 'History 201', 'completed' => 6, 'total' => 8, 'ai_usage' => 58, 'writing_score' => 68, 'flags' => 2, 'status' => 'Active', 'enrolled' => '2024-01-08', 'last_active' => '2024-01-13 17:45'],
    ['id' => 9, 'name' => 'Isabel Rodriguez', 'email' => 'irodriguez@university.edu', 'class' => 'History 201', 'completed' => 8, 'total' => 8, 'ai_usage' => 10, 'writing_score' => 90, 'flags' => 0, 'status' => 'Active', 'enrolled' => '2024-01-08', 'last_active' => '2024-01-15 10:00'],
    ['id' => 10, 'name' => 'James Chen', 'email' => 'jchen@university.edu', 'class' => 'Biology 110', 'completed' => 14, 'total' => 15, 'ai_usage' => 15, 'writing_score' => 85, 'flags' => 0, 'status' => 'Active', 'enrolled' => '2024-01-08', 'last_active' => '2024-01-15 11:20'],
    ['id' => 11, 'name' => 'Karen White', 'email' => 'kwhite@university.edu', 'class' => 'Psychology 101', 'completed' => 7, 'total' => 9, 'ai_usage' => 30, 'writing_score' => 81, 'flags' => 1, 'status' => 'Active', 'enrolled' => '2024-01-08', 'last_active' => '2024-01-14 09:15'],
    ['id' => 12, 'name' => 'Liam Brown', 'email' => 'lbrown@university.edu', 'class' => 'English 101 - Section A', 'completed' => 10, 'total' => 12, 'ai_usage' => 18, 'writing_score' => 83, 'flags' => 0, 'status' => 'Inactive', 'enrolled' => '2024-01-08', 'last_active' => '2024-01-10 08:00'],
    ['id' => 13, 'name' => 'Mia Garcia', 'email' => 'mgarcia@university.edu', 'class' => 'English 101 - Section B', 'completed' => 9, 'total' => 10, 'ai_usage' => 62, 'writing_score' => 70, 'flags' => 2, 'status' => 'Active', 'enrolled' => '2024-01-08', 'last_active' => '2024-01-15 07:30'],
    ['id' => 14, 'name' => 'Nathan Scott', 'email' => 'nscott@university.edu', 'class' => 'Biology 110', 'completed' => 15, 'total' => 15, 'ai_usage' => 2, 'writing_score' => 96, 'flags' => 0, 'status' => 'Active', 'enrolled' => '2024-01-08', 'last_active' => '2024-01-15 12:45'],
    ['id' => 15, 'name' => 'Olivia Turner', 'email' => 'oturner@university.edu', 'class' => 'History 201', 'completed' => 7, 'total' => 8, 'ai_usage' => 35, 'writing_score' => 77, 'flags' => 1, 'status' => 'Active', 'enrolled' => '2024-01-08', 'last_active' => '2024-01-14 16:00'],
];

$studentsJson = json_encode($students);
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
            search: '',
            filterClass: 'all',
            filterFlag: 'all',
            showDetailModal: false,
            selectedStudent: null,
            students: <?= htmlspecialchars($studentsJson, ENT_QUOTES) ?>,
            get filteredStudents() {
                return this.students.filter(s => {
                    const matchSearch = this.search === '' ||
                        s.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        s.email.toLowerCase().includes(this.search.toLowerCase());
                    const matchClass = this.filterClass === 'all' || s.class === this.filterClass;
                    const matchFlag = this.filterFlag === 'all' ||
                        (this.filterFlag === 'flagged' && s.flags > 0) ||
                        (this.filterFlag === 'clean' && s.flags === 0);
                    return matchSearch && matchClass && matchFlag;
                });
            },
            openDetail(student) {
                this.selectedStudent = student;
                this.showDetailModal = true;
            }
         }">

        <!-- Page Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900"><?= htmlspecialchars($pageTitle) ?></h1>
                <p class="mt-1 text-sm text-gray-500">View and manage students across all your classes.</p>
            </div>
            <div class="flex space-x-3">
                <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export CSV
                </button>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Student
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-lg border border-gray-200 p-4 text-center">
                <p class="text-2xl font-bold text-gray-900"><?= count($students) ?></p>
                <p class="text-xs text-gray-500">Total Students</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4 text-center">
                <p class="text-2xl font-bold text-green-600"><?= count(array_filter($students, fn($s) => $s['flags'] === 0)) ?></p>
                <p class="text-xs text-gray-500">Clean Records</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4 text-center">
                <p class="text-2xl font-bold text-red-600"><?= count(array_filter($students, fn($s) => $s['flags'] > 0)) ?></p>
                <p class="text-xs text-gray-500">With Flags</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4 text-center">
                <p class="text-2xl font-bold text-blue-600"><?= round(array_sum(array_column($students, 'writing_score')) / count($students)) ?>%</p>
                <p class="text-xs text-gray-500">Avg Writing Score</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4 text-center">
                <p class="text-2xl font-bold text-purple-600"><?= round(array_sum(array_column($students, 'ai_usage')) / count($students)) ?>%</p>
                <p class="text-xs text-gray-500">Avg AI Usage</p>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-[250px]">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input type="text" x-model="search" placeholder="Search by name or email..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Class:</label>
                    <select x-model="filterClass" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="all">All Classes</option>
                        <?php foreach ($classOptions as $cls): ?>
                            <option value="<?= $cls ?>"><?= $cls ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Flags:</label>
                    <select x-model="filterFlag" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="all">All Students</option>
                        <option value="flagged">Flagged Only</option>
                        <option value="clean">Clean Only</option>
                    </select>
                </div>
                <span class="text-sm text-gray-500">
                    <span x-text="filteredStudents.length"></span> students
                </span>
            </div>
        </div>

        <!-- Students Data Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assignments</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AI Usage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Writing Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Flags</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="student in filteredStudents" :key="student.id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-700 font-semibold text-sm mr-3" x-text="student.name.charAt(0)"></div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900" x-text="student.name"></p>
                                            <p class="text-xs text-gray-400" x-text="student.status === 'Inactive' ? 'Inactive' : ''"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="student.email"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700" x-text="student.class"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="text-gray-900" x-text="student.completed + '/' + student.total"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="h-2 rounded-full" :class="student.ai_usage >= 60 ? 'bg-red-500' : (student.ai_usage >= 30 ? 'bg-yellow-500' : 'bg-green-500')" :style="'width: ' + student.ai_usage + '%'"></div>
                                        </div>
                                        <span class="text-sm font-medium" :class="student.ai_usage >= 60 ? 'text-red-700' : (student.ai_usage >= 30 ? 'text-yellow-700' : 'text-green-700')" x-text="student.ai_usage + '%'"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-blue-500 h-2 rounded-full" :style="'width: ' + student.writing_score + '%'"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900" x-text="student.writing_score + '%'"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <template x-if="student.flags > 0">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800" x-text="'🚩 ' + student.flags + ' flag' + (student.flags > 1 ? 's' : '')"></span>
                                    </template>
                                    <template x-if="student.flags === 0">
                                        <span class="text-xs text-green-600">✓ Clean</span>
                                    </template>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button @click="openDetail(student)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View Details</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <!-- Empty State -->
            <div x-show="filteredStudents.length === 0" class="text-center py-12">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg>
                <p class="text-gray-500 text-sm">No students match your search criteria.</p>
                <button @click="search = ''; filterClass = 'all'; filterFlag = 'all'" class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">Clear Filters</button>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between mb-8">
            <p class="text-sm text-gray-500">Showing <span x-text="filteredStudents.length"></span> of <?= count($students) ?> students</p>
            <div class="flex space-x-1">
                <button class="px-3 py-1.5 bg-blue-600 text-white rounded text-sm font-medium">1</button>
                <button class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded text-sm hover:bg-gray-50">2</button>
                <button class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded text-sm hover:bg-gray-50">3</button>
                <button class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded text-sm hover:bg-gray-50">Next →</button>
            </div>
        </div>

        <!-- Student Detail Modal -->
        <div x-show="showDetailModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div class="fixed inset-0 bg-black/50" @click="showDetailModal = false"></div>
                <div class="relative bg-white rounded-2xl shadow-xl max-w-2xl w-full z-10 max-h-[90vh] overflow-y-auto" @click.away="showDetailModal = false">
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-5 rounded-t-2xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center text-white font-bold text-xl mr-4" x-text="selectedStudent?.name?.charAt(0) || ''"></div>
                                <div>
                                    <h3 class="text-xl font-semibold text-white" x-text="selectedStudent?.name || ''"></h3>
                                    <p class="text-blue-100 text-sm" x-text="selectedStudent?.email || ''"></p>
                                </div>
                            </div>
                            <button @click="showDetailModal = false" class="text-white/70 hover:text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>
                    <!-- Modal Body -->
                    <div class="p-6 space-y-6">
                        <!-- Quick Stats -->
                        <div class="grid grid-cols-4 gap-4">
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <p class="text-lg font-bold text-gray-900" x-text="selectedStudent?.completed + '/' + selectedStudent?.total"></p>
                                <p class="text-xs text-gray-500">Completed</p>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <p class="text-lg font-bold" :class="(selectedStudent?.ai_usage || 0) >= 60 ? 'text-red-600' : 'text-green-600'" x-text="(selectedStudent?.ai_usage || 0) + '%'"></p>
                                <p class="text-xs text-gray-500">AI Usage</p>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <p class="text-lg font-bold text-blue-600" x-text="(selectedStudent?.writing_score || 0) + '%'"></p>
                                <p class="text-xs text-gray-500">Writing Score</p>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <p class="text-lg font-bold" :class="(selectedStudent?.flags || 0) > 0 ? 'text-red-600' : 'text-green-600'" x-text="selectedStudent?.flags || 0"></p>
                                <p class="text-xs text-gray-500">Flags</p>
                            </div>
                        </div>

                        <!-- Student Info -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">Student Information</h4>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <span class="text-gray-500">Class:</span>
                                    <span class="ml-2 text-gray-900 font-medium" x-text="selectedStudent?.class || ''"></span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Status:</span>
                                    <span class="ml-2 text-gray-900 font-medium" x-text="selectedStudent?.status || ''"></span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Enrolled:</span>
                                    <span class="ml-2 text-gray-900 font-medium" x-text="selectedStudent?.enrolled || ''"></span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Last Active:</span>
                                    <span class="ml-2 text-gray-900 font-medium" x-text="selectedStudent?.last_active || ''"></span>
                                </div>
                            </div>
                        </div>

                        <!-- AI Usage Detail -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">AI Usage Analysis</h4>
                            <div class="mb-2 flex items-center justify-between text-sm">
                                <span class="text-gray-500">Overall AI Detection Score</span>
                                <span class="font-semibold" :class="(selectedStudent?.ai_usage || 0) >= 60 ? 'text-red-600' : (selectedStudent?.ai_usage || 0) >= 30 ? 'text-yellow-600' : 'text-green-600'" x-text="(selectedStudent?.ai_usage || 0) + '%'"></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
                                <div class="h-3 rounded-full transition-all" :class="(selectedStudent?.ai_usage || 0) >= 60 ? 'bg-red-500' : (selectedStudent?.ai_usage || 0) >= 30 ? 'bg-yellow-500' : 'bg-green-500'" :style="'width: ' + (selectedStudent?.ai_usage || 0) + '%'"></div>
                            </div>
                            <p class="text-xs text-gray-500">
                                <template x-if="(selectedStudent?.ai_usage || 0) >= 60">
                                    <span class="text-red-600">⚠️ High AI content detected. Review recommended.</span>
                                </template>
                                <template x-if="(selectedStudent?.ai_usage || 0) >= 30 && (selectedStudent?.ai_usage || 0) < 60">
                                    <span class="text-yellow-600">⚡ Moderate AI indicators found. May warrant review.</span>
                                </template>
                                <template x-if="(selectedStudent?.ai_usage || 0) < 30">
                                    <span class="text-green-600">✓ Low AI detection. Writing appears authentic.</span>
                                </template>
                            </p>
                        </div>

                        <!-- Recent Submissions in Modal -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">Recent Submissions</h4>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-700">Essay: Climate Change</span>
                                    <span class="text-xs text-green-600 font-medium">92/100</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-700">Book Report Ch.5</span>
                                    <span class="text-xs text-blue-600 font-medium">45/50</span>
                                </div>
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-sm text-gray-700">Grammar Quiz #4</span>
                                    <span class="text-xs text-green-600 font-medium">19/20</span>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                            <div class="flex space-x-2">
                                <button class="px-3 py-1.5 text-sm text-red-600 hover:text-red-800 font-medium">Remove Student</button>
                            </div>
                            <div class="flex space-x-3">
                                <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Send Message</button>
                                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">View Full Profile</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
