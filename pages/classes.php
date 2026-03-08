<?php
$pageTitle = 'My Classes';

$classes = [
    [
        'id' => 1,
        'name' => 'English 101',
        'subject' => 'English',
        'department' => 'Language Arts',
        'badgeColor' => 'bg-rose-100 text-rose-700',
        'instructor' => 'Dr. Sarah Mitchell',
        'instructorEmail' => 'smitchell@learnai.edu',
        'studentCount' => 28,
        'assignmentCount' => 12,
        'activeAssignments' => 3,
        'nextDueDate' => '2025-02-14',
        'nextDueTitle' => 'Essay: Modern Literature Analysis',
        'description' => 'Introduction to English composition, rhetoric, and critical reading skills.',
        'semester' => 'Spring 2025',
        'schedule' => 'Mon/Wed/Fri 9:00 AM - 9:50 AM',
        'room' => 'Humanities 204',
        'recentActivity' => [
            ['type' => 'assignment', 'text' => 'New assignment posted: Poetry Critique', 'time' => '2 hours ago'],
            ['type' => 'announcement', 'text' => 'Class cancelled on Friday due to faculty meeting', 'time' => '1 day ago'],
            ['type' => 'grade', 'text' => 'Grades posted for Midterm Essay', 'time' => '3 days ago'],
        ],
        'classmates' => [
            ['name' => 'Alice Johnson', 'role' => 'Student'],
            ['name' => 'Brian Chen', 'role' => 'Student'],
            ['name' => 'Carla Diaz', 'role' => 'Student'],
            ['name' => 'Derek Foster', 'role' => 'Student'],
            ['name' => 'Emily Park', 'role' => 'Student'],
            ['name' => 'Frank Nguyen', 'role' => 'Teaching Assistant'],
        ],
        'recentAssignments' => [
            ['title' => 'Poetry Critique', 'dueDate' => '2025-02-14', 'status' => 'pending', 'points' => 50],
            ['title' => 'Midterm Essay', 'dueDate' => '2025-02-01', 'status' => 'graded', 'points' => 100],
            ['title' => 'Reading Response #4', 'dueDate' => '2025-01-28', 'status' => 'submitted', 'points' => 25],
            ['title' => 'Grammar Workshop', 'dueDate' => '2025-01-20', 'status' => 'graded', 'points' => 30],
        ],
        'resources' => [
            ['name' => 'Course Syllabus', 'type' => 'PDF'],
            ['name' => 'MLA Citation Guide', 'type' => 'PDF'],
            ['name' => 'Writing Center Resources', 'type' => 'Link'],
        ],
    ],
    [
        'id' => 2,
        'name' => 'Biology 201',
        'subject' => 'Biology',
        'department' => 'Natural Sciences',
        'badgeColor' => 'bg-emerald-100 text-emerald-700',
        'instructor' => 'Prof. James Rivera',
        'instructorEmail' => 'jrivera@learnai.edu',
        'studentCount' => 34,
        'assignmentCount' => 15,
        'activeAssignments' => 4,
        'nextDueDate' => '2025-02-12',
        'nextDueTitle' => 'Lab Report: Cell Division',
        'description' => 'Intermediate biology covering cellular processes, genetics, and ecology.',
        'semester' => 'Spring 2025',
        'schedule' => 'Tue/Thu 10:30 AM - 11:45 AM',
        'room' => 'Science Building 310',
        'recentActivity' => [
            ['type' => 'resource', 'text' => 'New study guide uploaded for Chapter 8', 'time' => '5 hours ago'],
            ['type' => 'assignment', 'text' => 'Lab Report: Cell Division due date extended', 'time' => '1 day ago'],
            ['type' => 'discussion', 'text' => 'New discussion thread: Mitosis vs Meiosis', 'time' => '2 days ago'],
        ],
        'classmates' => [
            ['name' => 'Grace Kim', 'role' => 'Student'],
            ['name' => 'Hassan Ali', 'role' => 'Student'],
            ['name' => 'Isabella Torres', 'role' => 'Student'],
            ['name' => 'Jack Williams', 'role' => 'Student'],
            ['name' => 'Katie Brown', 'role' => 'Lab Assistant'],
            ['name' => 'Liam O\'Connor', 'role' => 'Student'],
        ],
        'recentAssignments' => [
            ['title' => 'Lab Report: Cell Division', 'dueDate' => '2025-02-12', 'status' => 'pending', 'points' => 75],
            ['title' => 'Chapter 7 Quiz', 'dueDate' => '2025-02-08', 'status' => 'pending', 'points' => 20],
            ['title' => 'Genetics Problem Set', 'dueDate' => '2025-02-03', 'status' => 'graded', 'points' => 40],
            ['title' => 'Midterm Exam', 'dueDate' => '2025-01-30', 'status' => 'graded', 'points' => 150],
        ],
        'resources' => [
            ['name' => 'Lab Safety Manual', 'type' => 'PDF'],
            ['name' => 'Virtual Microscope Tool', 'type' => 'Link'],
            ['name' => 'Chapter 8 Slides', 'type' => 'PPTX'],
        ],
    ],
    [
        'id' => 3,
        'name' => 'History 301',
        'subject' => 'History',
        'department' => 'Social Sciences',
        'badgeColor' => 'bg-amber-100 text-amber-700',
        'instructor' => 'Dr. Linda Kowalski',
        'instructorEmail' => 'lkowalski@learnai.edu',
        'studentCount' => 22,
        'assignmentCount' => 9,
        'activeAssignments' => 2,
        'nextDueDate' => '2025-02-18',
        'nextDueTitle' => 'Research Paper Outline',
        'description' => 'Advanced survey of modern world history from the Enlightenment to the present.',
        'semester' => 'Spring 2025',
        'schedule' => 'Mon/Wed 1:00 PM - 2:15 PM',
        'room' => 'Liberal Arts 112',
        'recentActivity' => [
            ['type' => 'announcement', 'text' => 'Guest speaker next Wednesday: Dr. Alan Petrov', 'time' => '4 hours ago'],
            ['type' => 'grade', 'text' => 'Grades posted for Document Analysis #2', 'time' => '2 days ago'],
        ],
        'classmates' => [
            ['name' => 'Maria Santos', 'role' => 'Student'],
            ['name' => 'Nathan Wright', 'role' => 'Student'],
            ['name' => 'Olivia Henderson', 'role' => 'Student'],
            ['name' => 'Paul Zhang', 'role' => 'Student'],
            ['name' => 'Quinn Murphy', 'role' => 'Student'],
        ],
        'recentAssignments' => [
            ['title' => 'Research Paper Outline', 'dueDate' => '2025-02-18', 'status' => 'pending', 'points' => 30],
            ['title' => 'Document Analysis #3', 'dueDate' => '2025-02-10', 'status' => 'submitted', 'points' => 50],
            ['title' => 'Document Analysis #2', 'dueDate' => '2025-01-27', 'status' => 'graded', 'points' => 50],
        ],
        'resources' => [
            ['name' => 'Primary Source Collection', 'type' => 'Link'],
            ['name' => 'Chicago Style Guide', 'type' => 'PDF'],
            ['name' => 'Timeline Tool', 'type' => 'Link'],
            ['name' => 'Course Reading List', 'type' => 'PDF'],
        ],
    ],
    [
        'id' => 4,
        'name' => 'Computer Science 101',
        'subject' => 'Computer Science',
        'department' => 'Engineering & Technology',
        'badgeColor' => 'bg-indigo-100 text-indigo-700',
        'instructor' => 'Prof. Kevin Nakamura',
        'instructorEmail' => 'knakamura@learnai.edu',
        'studentCount' => 42,
        'assignmentCount' => 18,
        'activeAssignments' => 5,
        'nextDueDate' => '2025-02-11',
        'nextDueTitle' => 'Programming Assignment #5: Sorting Algorithms',
        'description' => 'Foundations of programming, algorithms, and computational thinking using Python.',
        'semester' => 'Spring 2025',
        'schedule' => 'Tue/Thu 2:00 PM - 3:15 PM',
        'room' => 'Engineering Hall 405',
        'recentActivity' => [
            ['type' => 'assignment', 'text' => 'New assignment posted: Sorting Algorithms', 'time' => '1 hour ago'],
            ['type' => 'resource', 'text' => 'Lecture recording uploaded: Recursion Deep Dive', 'time' => '6 hours ago'],
            ['type' => 'discussion', 'text' => '12 new replies in: Help with Big-O Notation', 'time' => '1 day ago'],
        ],
        'classmates' => [
            ['name' => 'Rachel Lee', 'role' => 'Student'],
            ['name' => 'Sam Patel', 'role' => 'Student'],
            ['name' => 'Tina Rodriguez', 'role' => 'Student'],
            ['name' => 'Uma Krishnan', 'role' => 'Teaching Assistant'],
            ['name' => 'Victor Chang', 'role' => 'Student'],
            ['name' => 'Wendy Okafor', 'role' => 'Student'],
        ],
        'recentAssignments' => [
            ['title' => 'Sorting Algorithms', 'dueDate' => '2025-02-11', 'status' => 'pending', 'points' => 60],
            ['title' => 'Recursion Exercises', 'dueDate' => '2025-02-07', 'status' => 'pending', 'points' => 40],
            ['title' => 'Data Structures Quiz', 'dueDate' => '2025-02-04', 'status' => 'submitted', 'points' => 25],
            ['title' => 'Linked List Implementation', 'dueDate' => '2025-01-31', 'status' => 'graded', 'points' => 80],
        ],
        'resources' => [
            ['name' => 'Python Documentation', 'type' => 'Link'],
            ['name' => 'Algorithm Visualizer', 'type' => 'Link'],
            ['name' => 'Coding Style Guide', 'type' => 'PDF'],
            ['name' => 'Office Hours Schedule', 'type' => 'PDF'],
        ],
    ],
];

$activityIcons = [
    'assignment' => '<svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
    'announcement' => '<svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>',
    'grade' => '<svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    'resource' => '<svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
    'discussion' => '<svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>',
];

$statusStyles = [
    'pending' => 'bg-yellow-100 text-yellow-800',
    'submitted' => 'bg-blue-100 text-blue-800',
    'graded' => 'bg-green-100 text-green-800',
    'late' => 'bg-red-100 text-red-800',
];

$resourceIcons = [
    'PDF' => '<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>',
    'Link' => '<svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>',
    'PPTX' => '<svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 13v-1m4 1v-3m4 3V8M12 21l9-9-9-9-9 9 9 9z"/></svg>',
];
?>

<div x-data="{
    showJoinModal: false,
    classCode: '',
    expandedClass: null,
    toggleClass(id) { this.expandedClass = this.expandedClass === id ? null : id }
}">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">My Classes</h1>
            <p class="mt-1 text-sm text-slate-500">Spring 2025 &mdash; <?= e(count($classes)) ?> classes enrolled</p>
        </div>
        <button
            @click="showJoinModal = true"
            class="mt-4 sm:mt-0 inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Join Class
        </button>
    </div>

    <!-- Stats Summary Bar -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 text-center">
            <p class="text-2xl font-bold text-indigo-600"><?= e(count($classes)) ?></p>
            <p class="text-xs text-slate-500 mt-1">Enrolled Classes</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 text-center">
            <?php $totalActive = array_sum(array_column($classes, 'activeAssignments')); ?>
            <p class="text-2xl font-bold text-amber-600"><?= e($totalActive) ?></p>
            <p class="text-xs text-slate-500 mt-1">Active Assignments</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 text-center">
            <?php $totalStudents = array_sum(array_column($classes, 'studentCount')); ?>
            <p class="text-2xl font-bold text-emerald-600"><?= e(formatNumber($totalStudents)) ?></p>
            <p class="text-xs text-slate-500 mt-1">Total Classmates</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 text-center">
            <?php $totalAssignments = array_sum(array_column($classes, 'assignmentCount')); ?>
            <p class="text-2xl font-bold text-slate-700"><?= e($totalAssignments) ?></p>
            <p class="text-xs text-slate-500 mt-1">Total Assignments</p>
        </div>
    </div>

    <!-- Class Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
        <?php foreach ($classes as $class): ?>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition flex flex-col">
            <!-- Card Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="text-lg font-bold text-slate-900 truncate"><?= e($class['name']) ?></h3>
                    </div>
                    <span class="inline-block px-2.5 py-0.5 text-xs font-medium rounded-full <?= e($class['badgeColor']) ?>">
                        <?= e($class['subject']) ?>
                    </span>
                </div>
            </div>

            <!-- Instructor -->
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-semibold flex-shrink-0">
                    <?= e(initials($class['instructor'])) ?>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-800 truncate"><?= e($class['instructor']) ?></p>
                    <p class="text-xs text-slate-400"><?= e($class['department']) ?></p>
                </div>
            </div>

            <!-- Metrics -->
            <div class="grid grid-cols-3 gap-3 mb-4 py-3 border-y border-slate-100">
                <div class="text-center">
                    <p class="text-lg font-bold text-slate-700"><?= e($class['studentCount']) ?></p>
                    <p class="text-xs text-slate-400">Students</p>
                </div>
                <div class="text-center">
                    <p class="text-lg font-bold text-slate-700">
                        <span class="text-indigo-600"><?= e($class['activeAssignments']) ?></span>
                        <span class="text-slate-300">/</span><?= e($class['assignmentCount']) ?>
                    </p>
                    <p class="text-xs text-slate-400">Assignments</p>
                </div>
                <div class="text-center">
                    <p class="text-sm font-semibold text-slate-700"><?= e(formatDate($class['nextDueDate'])) ?></p>
                    <p class="text-xs text-slate-400">Next Due</p>
                </div>
            </div>

            <!-- Next Due Assignment -->
            <div class="bg-indigo-50 rounded-lg px-3 py-2 mb-4">
                <p class="text-xs text-indigo-600 font-medium">Next Due</p>
                <p class="text-sm text-indigo-900 font-semibold truncate"><?= e($class['nextDueTitle']) ?></p>
            </div>

            <!-- Recent Activity -->
            <div class="mb-4 flex-1">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Recent Activity</p>
                <ul class="space-y-2">
                    <?php foreach (array_slice($class['recentActivity'], 0, 3) as $activity): ?>
                    <li class="flex items-start gap-2">
                        <span class="mt-0.5 flex-shrink-0"><?= $activityIcons[$activity['type']] ?? '' ?></span>
                        <div class="min-w-0">
                            <p class="text-sm text-slate-700 leading-snug"><?= e(truncate($activity['text'], 60)) ?></p>
                            <p class="text-xs text-slate-400"><?= e($activity['time']) ?></p>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Card Actions -->
            <div class="flex items-center gap-2 pt-3 border-t border-slate-100 mt-auto">
                <button
                    @click="toggleClass(<?= e($class['id']) ?>)"
                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition"
                    :class="expandedClass === <?= e($class['id']) ?> ? 'bg-indigo-600 text-white' : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100'"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span x-text="expandedClass === <?= e($class['id']) ?> ? 'Hide Details' : 'View Class'">View Class</span>
                </button>
                <a href="<?= url('/classes/' . $class['id']) ?>" class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
            </div>

            <!-- Expanded Class Detail Panel -->
            <div
                x-show="expandedClass === <?= e($class['id']) ?>"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 max-h-0"
                x-transition:enter-end="opacity-100 max-h-[1000px]"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 max-h-[1000px]"
                x-transition:leave-end="opacity-0 max-h-0"
                class="overflow-hidden mt-4 pt-4 border-t border-slate-200"
                x-cloak
            >
                <!-- Class Info -->
                <div class="mb-5">
                    <p class="text-sm text-slate-600 mb-2"><?= e($class['description']) ?></p>
                    <div class="grid grid-cols-1 gap-1 text-xs text-slate-500">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <?= e($class['schedule']) ?>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <?= e($class['room']) ?>
                        </div>
                    </div>
                </div>

                <!-- Classmates -->
                <div class="mb-5">
                    <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Classmates</h4>
                    <div class="space-y-2">
                        <?php foreach ($class['classmates'] as $mate): ?>
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-xs font-semibold flex-shrink-0">
                                <?= e(initials($mate['name'])) ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-sm text-slate-700 truncate block"><?= e($mate['name']) ?></span>
                            </div>
                            <?php if ($mate['role'] !== 'Student'): ?>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-600 font-medium"><?= e($mate['role']) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <p class="mt-2 text-xs text-slate-400">
                        + <?= e($class['studentCount'] - count($class['classmates'])) ?> more students
                    </p>
                </div>

                <!-- Recent Assignments -->
                <div class="mb-5">
                    <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Recent Assignments</h4>
                    <div class="space-y-2">
                        <?php foreach ($class['recentAssignments'] as $assignment): ?>
                        <div class="flex items-center justify-between bg-slate-50 rounded-lg px-3 py-2">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-slate-700 truncate"><?= e($assignment['title']) ?></p>
                                <p class="text-xs text-slate-400">Due: <?= e(formatDate($assignment['dueDate'])) ?> &middot; <?= e($assignment['points']) ?> pts</p>
                            </div>
                            <span class="ml-2 inline-block px-2 py-0.5 text-xs font-medium rounded-full <?= e($statusStyles[$assignment['status']] ?? 'bg-slate-100 text-slate-600') ?>">
                                <?= e(ucfirst($assignment['status'])) ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Class Resources -->
                <div>
                    <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Resources</h4>
                    <div class="grid grid-cols-1 gap-2">
                        <?php foreach ($class['resources'] as $resource): ?>
                        <a href="<?= url('/classes/' . $class['id'] . '/resources') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-slate-50 transition group">
                            <span class="flex-shrink-0"><?= $resourceIcons[$resource['type']] ?? $resourceIcons['Link'] ?></span>
                            <span class="text-sm text-slate-700 group-hover:text-indigo-600 transition"><?= e($resource['name']) ?></span>
                            <span class="ml-auto text-xs text-slate-400"><?= e($resource['type']) ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Join Class Modal -->
    <div
        x-show="showJoinModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-cloak
    >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showJoinModal = false"></div>

        <!-- Modal Content -->
        <div
            x-show="showJoinModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6 z-10"
            @click.away="showJoinModal = false"
            @keydown.escape.window="showJoinModal = false"
        >
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Join a Class</h2>
                    <p class="text-sm text-slate-500 mt-1">Enter the class code provided by your instructor</p>
                </div>
                <button @click="showJoinModal = false" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form action="<?= url('/classes/join') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="mb-6">
                    <label for="classCode" class="block text-sm font-medium text-slate-700 mb-2">Class Code</label>
                    <input
                        type="text"
                        id="classCode"
                        name="class_code"
                        x-model="classCode"
                        placeholder="e.g. ABC-1234-XYZ"
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-center text-lg font-mono tracking-widest"
                        required
                        maxlength="20"
                        autocomplete="off"
                    />
                    <p class="mt-2 text-xs text-slate-400">Ask your instructor for the class enrollment code.</p>
                </div>

                <!-- Illustration -->
                <div class="bg-slate-50 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-700">How it works</p>
                            <p class="text-xs text-slate-500 mt-0.5">Your instructor will share a unique class code. Enter it here to instantly join and access all class materials, assignments, and discussions.</p>
                        </div>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="showJoinModal = false; classCode = ''"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="classCode.trim().length < 4"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition shadow"
                    >
                        Join Class
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Tips Section -->
    <div class="bg-gradient-to-r from-indigo-50 to-slate-50 rounded-xl border border-indigo-100 p-6">
        <h3 class="text-sm font-semibold text-indigo-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
            Quick Tips
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-start gap-2">
                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-indigo-200 text-indigo-700 flex items-center justify-center text-xs font-bold">1</span>
                <p class="text-sm text-slate-600">Click <span class="font-semibold text-indigo-600">"View Class"</span> on any card to see classmates, assignments, and resources.</p>
            </div>
            <div class="flex items-start gap-2">
                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-indigo-200 text-indigo-700 flex items-center justify-center text-xs font-bold">2</span>
                <p class="text-sm text-slate-600">Use the <span class="font-semibold text-indigo-600">"Join Class"</span> button to enroll in new classes with a code from your instructor.</p>
            </div>
            <div class="flex items-start gap-2">
                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-indigo-200 text-indigo-700 flex items-center justify-center text-xs font-bold">3</span>
                <p class="text-sm text-slate-600">Check the <span class="font-semibold text-amber-600">active assignments</span> count to stay on top of upcoming deadlines.</p>
            </div>
        </div>
    </div>

</div>
