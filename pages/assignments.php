<?php
$pageTitle = 'Assignments';

// ── Status configuration ─────────────────────────────────────────────
$statusConfig = [
    'not_started' => ['label' => 'Not Started', 'bg' => 'bg-slate-100',   'text' => 'text-slate-700',   'bar' => 'bg-slate-400'],
    'in_progress' => ['label' => 'In Progress', 'bg' => 'bg-blue-100',    'text' => 'text-blue-700',    'bar' => 'bg-blue-500'],
    'submitted'   => ['label' => 'Submitted',   'bg' => 'bg-amber-100',   'text' => 'text-amber-700',   'bar' => 'bg-amber-500'],
    'graded'      => ['label' => 'Graded',      'bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'bar' => 'bg-emerald-500'],
    'overdue'     => ['label' => 'Overdue',      'bg' => 'bg-rose-100',    'text' => 'text-rose-700',    'bar' => 'bg-rose-500'],
];

// ── Class / subject color map ────────────────────────────────────────
$classColors = [
    'English 101'          => ['bg' => 'bg-violet-100',  'text' => 'text-violet-700'],
    'Biology 201'          => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700'],
    'History 301'          => ['bg' => 'bg-amber-100',   'text' => 'text-amber-700'],
    'Computer Science 101' => ['bg' => 'bg-cyan-100',    'text' => 'text-cyan-700'],
    'Mathematics 202'      => ['bg' => 'bg-pink-100',    'text' => 'text-pink-700'],
    'Philosophy 101'       => ['bg' => 'bg-indigo-100',  'text' => 'text-indigo-700'],
];

// ── Fake assignments data ────────────────────────────────────────────
$assignments = [
    [
        'id'          => 1,
        'title'       => 'Argumentative Essay: AI in Education',
        'class'       => 'English 101',
        'description' => 'Write a well-structured argumentative essay exploring the impact of artificial intelligence on modern education. Discuss both the benefits and potential drawbacks, providing evidence-based arguments to support your thesis. Consider perspectives from educators, students, and technology experts.',
        'due'         => date('Y-m-d', strtotime('+3 days')),
        'status'      => 'in_progress',
        'progress'    => 45,
        'points'      => 100,
        'grade'       => null,
        'letter'      => null,
        'document'    => 'AI_Essay_Draft_v2.docx',
        'comments'    => [
            ['author' => 'Prof. Williams', 'text' => 'Good thesis statement. Strengthen your second body paragraph with more concrete examples.', 'date' => date('Y-m-d H:i:s', strtotime('-1 day'))],
            ['author' => 'Prof. Williams', 'text' => 'Consider adding a counterargument section before your conclusion.', 'date' => date('Y-m-d H:i:s', strtotime('-3 hours'))],
        ],
        'requirements' => [
            'Minimum 1500 words',
            'Include at least 5 scholarly sources',
            'MLA format with works cited page',
            'Include counterargument section',
        ],
        'rubric' => [
            ['criterion' => 'Thesis & Argument', 'points' => 30, 'description' => 'Clear, arguable thesis with well-developed supporting arguments'],
            ['criterion' => 'Evidence & Sources', 'points' => 25, 'description' => 'Relevant, credible sources properly cited in MLA format'],
            ['criterion' => 'Organization',       'points' => 20, 'description' => 'Logical structure with smooth transitions between paragraphs'],
            ['criterion' => 'Grammar & Style',     'points' => 15, 'description' => 'Proper grammar, varied sentence structure, academic tone'],
            ['criterion' => 'Formatting',          'points' => 10, 'description' => 'Correct MLA formatting, headers, and works cited page'],
        ],
    ],
    [
        'id'          => 2,
        'title'       => 'Cell Biology Lab Report',
        'class'       => 'Biology 201',
        'description' => 'Complete a detailed lab report on the cell division experiment conducted in lab session 4. Include hypothesis, methodology, observations, data analysis with charts, and a thorough discussion of results comparing mitosis and meiosis processes.',
        'due'         => date('Y-m-d', strtotime('+7 days')),
        'status'      => 'not_started',
        'progress'    => 0,
        'points'      => 75,
        'grade'       => null,
        'letter'      => null,
        'document'    => null,
        'comments'    => [],
        'requirements' => [
            'Follow standard lab report format',
            'Include at least 2 data charts or graphs',
            'Minimum 800 words in discussion section',
            'Reference lab manual sections 4.1–4.5',
        ],
        'rubric' => [
            ['criterion' => 'Hypothesis',     'points' => 10, 'description' => 'Clear, testable hypothesis based on background research'],
            ['criterion' => 'Methodology',     'points' => 15, 'description' => 'Detailed, reproducible procedure description'],
            ['criterion' => 'Data & Analysis', 'points' => 25, 'description' => 'Accurate data presentation with proper charts and statistical analysis'],
            ['criterion' => 'Discussion',      'points' => 20, 'description' => 'Thorough interpretation of results with connections to theory'],
            ['criterion' => 'Formatting',      'points' => 5,  'description' => 'Proper lab report structure and citations'],
        ],
    ],
    [
        'id'          => 3,
        'title'       => 'World War II Primary Source Analysis',
        'class'       => 'History 301',
        'description' => 'Analyze three primary source documents from the World War II era. Evaluate the historical context, author perspective, intended audience, and significance of each document. Compare and contrast the narratives presented across the sources.',
        'due'         => date('Y-m-d', strtotime('-2 days')),
        'status'      => 'overdue',
        'progress'    => 20,
        'points'      => 50,
        'grade'       => null,
        'letter'      => null,
        'document'    => 'WWII_Analysis_Draft.docx',
        'comments'    => [
            ['author' => 'Dr. Chen', 'text' => 'This assignment is now overdue. Please submit as soon as possible — late penalty of 10% per day applies.', 'date' => date('Y-m-d H:i:s', strtotime('-1 day'))],
        ],
        'requirements' => [
            'Analyze at least 3 primary source documents',
            'Include historical context for each source',
            'Compare narratives across sources',
            'Chicago citation style required',
        ],
        'rubric' => [
            ['criterion' => 'Source Analysis',  'points' => 20, 'description' => 'Thorough analysis of each primary source'],
            ['criterion' => 'Historical Context','points' => 15, 'description' => 'Accurate contextualization within WWII era'],
            ['criterion' => 'Comparison',        'points' => 10, 'description' => 'Meaningful comparison across sources'],
            ['criterion' => 'Writing Quality',   'points' => 5,  'description' => 'Clear academic writing with proper citations'],
        ],
    ],
    [
        'id'          => 4,
        'title'       => 'Python Data Structures Project',
        'class'       => 'Computer Science 101',
        'description' => 'Implement a library management system using Python data structures including linked lists, hash tables, and binary search trees. The system should support adding, searching, borrowing, and returning books with an interactive command-line interface.',
        'due'         => date('Y-m-d', strtotime('-5 days')),
        'status'      => 'graded',
        'progress'    => 100,
        'points'      => 120,
        'grade'       => 102,
        'letter'      => 'A',
        'document'    => 'library_system.py',
        'comments'    => [
            ['author' => 'Prof. Martinez', 'text' => 'Excellent implementation! Your BST search is very efficient. The hash table collision handling is well done.', 'date' => date('Y-m-d H:i:s', strtotime('-3 days'))],
            ['author' => 'Prof. Martinez', 'text' => 'Bonus points awarded for implementing the recommendation engine. Great work!', 'date' => date('Y-m-d H:i:s', strtotime('-3 days'))],
        ],
        'requirements' => [
            'Implement linked list, hash table, and BST',
            'Interactive CLI with menu system',
            'Handle edge cases and input validation',
            'Include unit tests for all data structures',
        ],
        'rubric' => [
            ['criterion' => 'Data Structures',   'points' => 40, 'description' => 'Correct implementation of all required data structures'],
            ['criterion' => 'Functionality',      'points' => 30, 'description' => 'All CRUD operations work correctly'],
            ['criterion' => 'Code Quality',       'points' => 25, 'description' => 'Clean code, proper naming, comments, and modularity'],
            ['criterion' => 'Testing',            'points' => 15, 'description' => 'Comprehensive unit tests with good coverage'],
            ['criterion' => 'Bonus',              'points' => 10, 'description' => 'Extra features beyond requirements'],
        ],
    ],
    [
        'id'          => 5,
        'title'       => 'Statistical Analysis Problem Set',
        'class'       => 'Mathematics 202',
        'description' => 'Complete the problem set covering chapters 7-9 on hypothesis testing, confidence intervals, and regression analysis. Show all work including formulas, calculations, and interpretations of results in context.',
        'due'         => date('Y-m-d', strtotime('+14 days')),
        'status'      => 'not_started',
        'progress'    => 0,
        'points'      => 60,
        'grade'       => null,
        'letter'      => null,
        'document'    => null,
        'comments'    => [],
        'requirements' => [
            'Complete all 15 problems',
            'Show all work and formulas used',
            'Use proper statistical notation',
            'Interpret results in plain language',
        ],
        'rubric' => [
            ['criterion' => 'Correctness',     'points' => 35, 'description' => 'Accurate calculations and correct final answers'],
            ['criterion' => 'Work Shown',       'points' => 15, 'description' => 'Clear step-by-step solutions'],
            ['criterion' => 'Interpretation',   'points' => 10, 'description' => 'Meaningful interpretation of statistical results'],
        ],
    ],
    [
        'id'          => 6,
        'title'       => 'Ethics in Technology Essay',
        'class'       => 'Philosophy 101',
        'description' => 'Write a reflective essay examining the ethical implications of emerging technologies such as facial recognition, autonomous vehicles, and social media algorithms. Apply at least two ethical frameworks discussed in class to analyze a specific technology of your choice.',
        'due'         => date('Y-m-d', strtotime('+1 day')),
        'status'      => 'submitted',
        'progress'    => 100,
        'points'      => 80,
        'grade'       => null,
        'letter'      => null,
        'document'    => 'Ethics_Tech_Final.docx',
        'comments'    => [
            ['author' => 'Dr. Patel', 'text' => 'Received your submission. I will review it this week.', 'date' => date('Y-m-d H:i:s', strtotime('-2 hours'))],
        ],
        'requirements' => [
            'Minimum 1200 words',
            'Apply at least 2 ethical frameworks',
            'Include real-world examples',
            'APA citation format with references',
        ],
        'rubric' => [
            ['criterion' => 'Ethical Analysis',  'points' => 30, 'description' => 'Correct application of ethical frameworks'],
            ['criterion' => 'Critical Thinking',  'points' => 25, 'description' => 'Original insights and nuanced argumentation'],
            ['criterion' => 'Examples',            'points' => 15, 'description' => 'Relevant real-world examples effectively integrated'],
            ['criterion' => 'Writing & Format',    'points' => 10, 'description' => 'Clear writing, proper APA formatting'],
        ],
    ],
    [
        'id'          => 7,
        'title'       => 'Ecosystem Biodiversity Research Paper',
        'class'       => 'Biology 201',
        'description' => 'Research and write a comprehensive paper on biodiversity loss in a specific ecosystem of your choice. Include causes, current data and trends, conservation efforts, and propose evidence-based solutions. Must include original data visualization.',
        'due'         => date('Y-m-d', strtotime('+21 days')),
        'status'      => 'not_started',
        'progress'    => 0,
        'points'      => 150,
        'grade'       => null,
        'letter'      => null,
        'document'    => null,
        'comments'    => [],
        'requirements' => [
            'Minimum 3000 words',
            'At least 10 peer-reviewed sources',
            'Include original data visualization',
            'CSE citation format',
            'Abstract and keywords required',
        ],
        'rubric' => [
            ['criterion' => 'Research Depth',     'points' => 40, 'description' => 'Comprehensive coverage with quality peer-reviewed sources'],
            ['criterion' => 'Data Visualization', 'points' => 25, 'description' => 'Original, clear, and informative charts or graphs'],
            ['criterion' => 'Analysis',           'points' => 35, 'description' => 'Insightful analysis of causes, trends, and solutions'],
            ['criterion' => 'Writing Quality',    'points' => 30, 'description' => 'Well-organized, clear scientific writing'],
            ['criterion' => 'Formatting',         'points' => 20, 'description' => 'Proper CSE format, abstract, and references'],
        ],
    ],
];

// ── Tab counts ───────────────────────────────────────────────────────
$counts = [
    'all'       => count($assignments),
    'active'    => count(array_filter($assignments, fn($a) => in_array($a['status'], ['in_progress', 'submitted']))),
    'upcoming'  => count(array_filter($assignments, fn($a) => $a['status'] === 'not_started' && strtotime($a['due']) > time())),
    'completed' => count(array_filter($assignments, fn($a) => $a['status'] === 'graded')),
    'overdue'   => count(array_filter($assignments, fn($a) => $a['status'] === 'overdue')),
];

// ── Calendar helpers ─────────────────────────────────────────────────
$calYear  = (int) date('Y');
$calMonth = (int) date('n');
$calMonthName = date('F Y');
$daysInMonth  = (int) date('t');
$firstDayOfWeek = (int) date('w', mktime(0, 0, 0, $calMonth, 1, $calYear));
$today = (int) date('j');

$dueDatesByDay = [];
foreach ($assignments as $a) {
    $dueMonth = (int) date('n', strtotime($a['due']));
    $dueYear  = (int) date('Y', strtotime($a['due']));
    if ($dueMonth === $calMonth && $dueYear === $calYear) {
        $day = (int) date('j', strtotime($a['due']));
        $dueDatesByDay[$day][] = $a;
    }
}
?>

<!-- ================================================================= -->
<!-- PAGE WRAPPER (Alpine.js root)                                      -->
<!-- ================================================================= -->
<div
    x-data="{
        activeTab: 'all',
        showModal: false,
        selectedAssignment: null,
        assignments: <?= json_encode($assignments, JSON_HEX_APOS | JSON_HEX_TAG) ?>,

        get filteredAssignments() {
            if (this.activeTab === 'all') return this.assignments;
            if (this.activeTab === 'active') return this.assignments.filter(a => a.status === 'in_progress' || a.status === 'submitted');
            if (this.activeTab === 'upcoming') return this.assignments.filter(a => a.status === 'not_started' && new Date(a.due) > new Date());
            if (this.activeTab === 'completed') return this.assignments.filter(a => a.status === 'graded');
            if (this.activeTab === 'overdue') return this.assignments.filter(a => a.status === 'overdue');
            return this.assignments;
        },

        openModal(id) {
            this.selectedAssignment = this.assignments.find(a => a.id === id) || null;
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            setTimeout(() => this.selectedAssignment = null, 200);
        },

        dueLabel(due) {
            const now  = new Date();
            const d    = new Date(due + 'T23:59:59');
            const diff = Math.ceil((d - now) / 86400000);
            if (diff < 0) return 'Due ' + Math.abs(diff) + (Math.abs(diff) === 1 ? ' day ago' : ' days ago');
            if (diff === 0) return 'Due today';
            if (diff === 1) return 'Due tomorrow';
            return 'Due in ' + diff + ' days';
        },

        dueColor(due, status) {
            if (status === 'graded' || status === 'submitted') return 'text-slate-500';
            const now  = new Date();
            const d    = new Date(due + 'T23:59:59');
            const diff = Math.ceil((d - now) / 86400000);
            if (diff < 0)  return 'text-rose-600 font-semibold';
            if (diff <= 2) return 'text-amber-600 font-semibold';
            return 'text-emerald-600';
        },

        statusLabel(s) {
            const map = { not_started: 'Not Started', in_progress: 'In Progress', submitted: 'Submitted', graded: 'Graded', overdue: 'Overdue' };
            return map[s] || s;
        },

        statusClasses(s) {
            const map = {
                not_started: 'bg-slate-100 text-slate-700',
                in_progress: 'bg-blue-100 text-blue-700',
                submitted:   'bg-amber-100 text-amber-700',
                graded:      'bg-emerald-100 text-emerald-700',
                overdue:     'bg-rose-100 text-rose-700'
            };
            return map[s] || 'bg-slate-100 text-slate-700';
        },

        barColor(s) {
            const map = {
                not_started: 'bg-slate-400',
                in_progress: 'bg-blue-500',
                submitted:   'bg-amber-500',
                graded:      'bg-emerald-500',
                overdue:     'bg-rose-500'
            };
            return map[s] || 'bg-slate-400';
        },

        classColor(cls, prop) {
            const map = {
                'English 101':          { bg: 'bg-violet-100',  text: 'text-violet-700'  },
                'Biology 201':          { bg: 'bg-emerald-100', text: 'text-emerald-700' },
                'History 301':          { bg: 'bg-amber-100',   text: 'text-amber-700'   },
                'Computer Science 101': { bg: 'bg-cyan-100',    text: 'text-cyan-700'    },
                'Mathematics 202':      { bg: 'bg-pink-100',    text: 'text-pink-700'    },
                'Philosophy 101':       { bg: 'bg-indigo-100',  text: 'text-indigo-700'  }
            };
            const c = map[cls] || { bg: 'bg-slate-100', text: 'text-slate-700' };
            return c[prop];
        },

        actionLabel(s) {
            const map = { not_started: 'Start Assignment', in_progress: 'Continue Writing', submitted: 'View Submission', graded: 'View Submission', overdue: 'Resubmit' };
            return map[s] || 'View';
        },

        actionIcon(s) {
            if (s === 'not_started') return 'M12 4v16m8-8H4';
            if (s === 'in_progress') return 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z';
            return 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z';
        }
    }"
    @keydown.escape.window="closeModal()"
>

    <!-- ============================================================= -->
    <!-- PAGE HEADER                                                    -->
    <!-- ============================================================= -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Assignments</h1>
            <p class="mt-1 text-sm text-slate-500"><?= e(formatNumber(count($assignments))) ?> total assignments across all classes</p>
        </div>
        <a href="<?= url('/documents') ?>" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            New Document
        </a>
    </div>

    <!-- ============================================================= -->
    <!-- FILTER TABS                                                    -->
    <!-- ============================================================= -->
    <div class="flex flex-wrap gap-2 mb-6 border-b border-slate-200 pb-4">
        <?php
        $tabs = [
            'all'       => ['label' => 'All',       'count' => $counts['all']],
            'active'    => ['label' => 'Active',    'count' => $counts['active']],
            'upcoming'  => ['label' => 'Upcoming',  'count' => $counts['upcoming']],
            'completed' => ['label' => 'Completed', 'count' => $counts['completed']],
            'overdue'   => ['label' => 'Overdue',   'count' => $counts['overdue']],
        ];
        foreach ($tabs as $key => $tab): ?>
            <button
                @click="activeTab = '<?= e($key) ?>'"
                :class="activeTab === '<?= e($key) ?>'
                    ? 'bg-indigo-600 text-white shadow-sm'
                    : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'"
                class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition"
            >
                <?= e($tab['label']) ?>
                <span
                    :class="activeTab === '<?= e($key) ?>'
                        ? 'bg-indigo-500 text-white'
                        : 'bg-slate-100 text-slate-600'"
                    class="inline-flex items-center justify-center rounded-full px-2 py-0.5 text-xs font-semibold min-w-[1.25rem]"
                ><?= e($tab['count']) ?></span>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- ============================================================= -->
    <!-- ASSIGNMENT CARDS                                               -->
    <!-- ============================================================= -->
    <div x-show="filteredAssignments.length > 0" x-transition class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-10">
        <template x-for="a in filteredAssignments" :key="a.id">
            <div class="group bg-white rounded-xl shadow-sm border border-slate-200 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col">
                <!-- Card header -->
                <div class="p-6 pb-0 flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <button @click="openModal(a.id)" class="text-left">
                            <h3 class="text-base font-bold text-slate-900 hover:text-indigo-600 transition truncate" x-text="a.title"></h3>
                        </button>
                        <div class="mt-1.5 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium" :class="classColor(a.class, 'bg') + ' ' + classColor(a.class, 'text')" x-text="a.class"></span>
                            <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium" :class="statusClasses(a.status)" x-text="statusLabel(a.status)"></span>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <template x-if="a.grade !== null">
                            <div>
                                <span class="text-lg font-bold text-emerald-600" x-text="a.grade + '/' + a.points"></span>
                                <span class="ml-1 inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold" x-text="a.letter"></span>
                            </div>
                        </template>
                        <template x-if="a.grade === null">
                            <span class="text-sm font-medium text-slate-500" x-text="a.points + ' pts'"></span>
                        </template>
                    </div>
                </div>

                <!-- Description -->
                <div class="px-6 pt-3">
                    <p class="text-sm text-slate-600 line-clamp-2" x-text="a.description"></p>
                </div>

                <!-- Requirements preview -->
                <div class="px-6 pt-3">
                    <ul class="space-y-1">
                        <template x-for="(req, i) in a.requirements.slice(0, 3)" :key="i">
                            <li class="flex items-start gap-2 text-xs text-slate-500">
                                <svg class="w-3.5 h-3.5 mt-0.5 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                <span x-text="req"></span>
                            </li>
                        </template>
                        <template x-if="a.requirements.length > 3">
                            <li class="text-xs text-indigo-500 ml-5 font-medium" x-text="'+' + (a.requirements.length - 3) + ' more'"></li>
                        </template>
                    </ul>
                </div>

                <!-- Document link -->
                <div class="px-6 pt-3">
                    <template x-if="a.document">
                        <a :href="'<?= url('/documents') ?>'" class="inline-flex items-center gap-1.5 text-xs font-medium text-indigo-600 hover:text-indigo-700 transition">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span x-text="a.document"></span>
                        </a>
                    </template>
                </div>

                <!-- Teacher comments preview -->
                <template x-if="a.comments.length > 0">
                    <div class="px-6 pt-3">
                        <div class="rounded-lg bg-slate-50 border border-slate-100 px-3 py-2">
                            <div class="flex items-center gap-1.5 mb-1">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                <span class="text-xs font-semibold text-slate-700" x-text="a.comments[a.comments.length - 1].author"></span>
                            </div>
                            <p class="text-xs text-slate-500 line-clamp-1" x-text="a.comments[a.comments.length - 1].text"></p>
                        </div>
                    </div>
                </template>

                <!-- Footer: due date, progress bar, action -->
                <div class="mt-auto p-6 pt-4 space-y-3">
                    <!-- Due date -->
                    <div class="flex items-center justify-between text-xs">
                        <span :class="dueColor(a.due, a.status)" class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span x-text="dueLabel(a.due)"></span>
                        </span>
                        <span class="text-slate-400" x-text="new Date(a.due).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })"></span>
                    </div>

                    <!-- Progress bar -->
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="text-slate-500">Progress</span>
                            <span class="font-medium text-slate-700" x-text="a.progress + '%'"></span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500" :class="barColor(a.status)" :style="'width:' + a.progress + '%'"></div>
                        </div>
                    </div>

                    <!-- Action button -->
                    <div class="flex items-center gap-2">
                        <button @click="openModal(a.id)" class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition"
                            :class="a.status === 'overdue'
                                ? 'bg-rose-600 text-white hover:bg-rose-700'
                                : (a.status === 'not_started'
                                    ? 'bg-indigo-600 text-white hover:bg-indigo-700'
                                    : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50')">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" :d="actionIcon(a.status)"/></svg>
                            <span x-text="actionLabel(a.status)"></span>
                        </button>
                        <button @click="openModal(a.id)" class="p-2 rounded-lg border border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-200 transition" title="View Details">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- ============================================================= -->
    <!-- EMPTY / FILTERED STATES                                        -->
    <!-- ============================================================= -->
    <div x-show="filteredAssignments.length === 0" x-transition x-cloak class="text-center py-16">
        <!-- Overdue empty -->
        <template x-if="activeTab === 'overdue'">
            <div>
                <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full bg-emerald-50 mb-4">
                    <svg class="w-8 h-8 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 mb-1">No overdue assignments</h3>
                <p class="text-sm text-slate-500">Great job staying on top of your work! Keep it up.</p>
            </div>
        </template>
        <!-- Completed empty -->
        <template x-if="activeTab === 'completed'">
            <div>
                <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 mb-1">No completed assignments yet</h3>
                <p class="text-sm text-slate-500">Completed and graded assignments will appear here.</p>
            </div>
        </template>
        <!-- Active empty -->
        <template x-if="activeTab === 'active'">
            <div>
                <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 mb-4">
                    <svg class="w-8 h-8 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 mb-1">No active assignments</h3>
                <p class="text-sm text-slate-500">You don't have any assignments in progress right now.</p>
            </div>
        </template>
        <!-- Upcoming empty -->
        <template x-if="activeTab === 'upcoming'">
            <div>
                <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full bg-indigo-50 mb-4">
                    <svg class="w-8 h-8 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 mb-1">No upcoming assignments</h3>
                <p class="text-sm text-slate-500">New assignments from your teachers will appear here.</p>
            </div>
        </template>
        <!-- All empty (fallback) -->
        <template x-if="activeTab === 'all'">
            <div>
                <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 mb-1">No assignments found</h3>
                <p class="text-sm text-slate-500">Check back later for new assignments from your classes.</p>
            </div>
        </template>
    </div>

    <!-- ============================================================= -->
    <!-- CALENDAR VIEW                                                  -->
    <!-- ============================================================= -->
    <div class="mt-4 mb-10">
        <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <?= e($calMonthName) ?> — Due Dates
        </h2>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <!-- Day-of-week headers -->
            <div class="grid grid-cols-7 border-b border-slate-100">
                <?php foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $dow): ?>
                    <div class="py-2.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider"><?= e($dow) ?></div>
                <?php endforeach; ?>
            </div>

            <!-- Day cells -->
            <div class="grid grid-cols-7">
                <?php
                // Empty cells before first day
                for ($blank = 0; $blank < $firstDayOfWeek; $blank++): ?>
                    <div class="min-h-[5rem] border-b border-r border-slate-50 bg-slate-50/50"></div>
                <?php endfor;

                for ($day = 1; $day <= $daysInMonth; $day++):
                    $isToday    = ($day === $today);
                    $hasAssign  = isset($dueDatesByDay[$day]);
                    $cellIdx    = $firstDayOfWeek + $day - 1;
                    $isLastCol  = (($cellIdx + 1) % 7 === 0);
                ?>
                    <div class="min-h-[5rem] p-1.5 border-b border-slate-50 <?= $isLastCol ? '' : 'border-r border-slate-50' ?> <?= $isToday ? 'bg-indigo-50/60' : '' ?> relative">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-semibold <?= $isToday ? 'bg-indigo-600 text-white' : 'text-slate-700' ?>">
                            <?= e($day) ?>
                        </span>
                        <?php if ($hasAssign): ?>
                            <div class="mt-0.5 space-y-0.5">
                                <?php foreach ($dueDatesByDay[$day] as $ca):
                                    $cc = $classColors[$ca['class']] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-700'];
                                    $sc = $statusConfig[$ca['status']] ?? $statusConfig['not_started'];
                                ?>
                                    <div class="truncate rounded px-1 py-0.5 text-[10px] font-medium leading-tight <?= e($sc['bg']) ?> <?= e($sc['text']) ?>" title="<?= e($ca['title']) ?>">
                                        <?= e(truncate($ca['title'], 18)) ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endfor;

                // Trailing blank cells
                $totalCells = $firstDayOfWeek + $daysInMonth;
                $trailing   = (7 - ($totalCells % 7)) % 7;
                for ($t = 0; $t < $trailing; $t++): ?>
                    <div class="min-h-[5rem] border-b border-r border-slate-50 bg-slate-50/50"></div>
                <?php endfor; ?>
            </div>

            <!-- Legend -->
            <div class="flex flex-wrap items-center gap-4 px-4 py-3 border-t border-slate-100 bg-slate-50/50">
                <?php foreach ($statusConfig as $sKey => $sVal): ?>
                    <span class="inline-flex items-center gap-1.5 text-xs text-slate-600">
                        <span class="w-2.5 h-2.5 rounded-full <?= e($sVal['bar']) ?>"></span>
                        <?= e($sVal['label']) ?>
                    </span>
                <?php endforeach; ?>
                <span class="inline-flex items-center gap-1.5 text-xs text-slate-600">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                    Today
                </span>
            </div>
        </div>
    </div>

    <!-- ============================================================= -->
    <!-- ASSIGNMENT DETAIL MODAL                                        -->
    <!-- ============================================================= -->
    <template x-if="selectedAssignment">
        <div
            x-show="showModal"
            x-cloak
            class="fixed inset-0 z-50 flex items-start justify-center px-4 py-8 overflow-y-auto"
            @click.self="closeModal()"
        >
            <!-- Backdrop -->
            <div
                x-show="showModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"
                @click="closeModal()"
            ></div>

            <!-- Modal card -->
            <div
                x-show="showModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl z-10"
            >
                <!-- Modal header -->
                <div class="flex items-start justify-between gap-4 p-6 border-b border-slate-100">
                    <div class="min-w-0 flex-1">
                        <h2 class="text-xl font-bold text-slate-900" x-text="selectedAssignment.title"></h2>
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-medium" :class="classColor(selectedAssignment.class, 'bg') + ' ' + classColor(selectedAssignment.class, 'text')" x-text="selectedAssignment.class"></span>
                            <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-medium" :class="statusClasses(selectedAssignment.status)" x-text="statusLabel(selectedAssignment.status)"></span>
                            <span :class="dueColor(selectedAssignment.due, selectedAssignment.status)" class="text-xs flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span x-text="dueLabel(selectedAssignment.due)"></span>
                            </span>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <template x-if="selectedAssignment.grade !== null">
                            <div>
                                <span class="text-2xl font-bold text-emerald-600" x-text="selectedAssignment.grade + '/' + selectedAssignment.points"></span>
                                <span class="ml-1 inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 text-sm font-bold" x-text="selectedAssignment.letter"></span>
                            </div>
                        </template>
                        <template x-if="selectedAssignment.grade === null">
                            <span class="text-lg font-semibold text-slate-500" x-text="selectedAssignment.points + ' points'"></span>
                        </template>
                    </div>
                    <button @click="closeModal()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal body -->
                <div class="p-6 space-y-6 max-h-[60vh] overflow-y-auto">
                    <!-- Full description -->
                    <div>
                        <h4 class="text-sm font-semibold text-slate-900 mb-2">Description</h4>
                        <p class="text-sm text-slate-600 leading-relaxed" x-text="selectedAssignment.description"></p>
                    </div>

                    <!-- Progress bar -->
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span class="font-medium text-slate-700">Progress</span>
                            <span class="font-semibold" :class="barColor(selectedAssignment.status).replace('bg-', 'text-')" x-text="selectedAssignment.progress + '%'"></span>
                        </div>
                        <div class="h-3 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500" :class="barColor(selectedAssignment.status)" :style="'width:' + selectedAssignment.progress + '%'"></div>
                        </div>
                    </div>

                    <!-- Requirements checklist -->
                    <div>
                        <h4 class="text-sm font-semibold text-slate-900 mb-2">Requirements</h4>
                        <ul class="space-y-2">
                            <template x-for="(req, i) in selectedAssignment.requirements" :key="i">
                                <li class="flex items-start gap-3">
                                    <input type="checkbox" :checked="selectedAssignment.progress === 100" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/40">
                                    <span class="text-sm text-slate-600" x-text="req"></span>
                                </li>
                            </template>
                        </ul>
                    </div>

                    <!-- Submission area -->
                    <div>
                        <h4 class="text-sm font-semibold text-slate-900 mb-2">Submission</h4>
                        <template x-if="selectedAssignment.document">
                            <div class="flex items-center gap-3 p-3 rounded-lg bg-indigo-50 border border-indigo-100">
                                <svg class="w-8 h-8 text-indigo-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-indigo-700 truncate" x-text="selectedAssignment.document"></p>
                                    <p class="text-xs text-indigo-500">Linked document</p>
                                </div>
                                <a :href="'<?= url('/documents') ?>'" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">Open</a>
                            </div>
                        </template>
                        <template x-if="!selectedAssignment.document">
                            <div class="flex flex-col items-center gap-2 p-6 rounded-lg border-2 border-dashed border-slate-200 text-center">
                                <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                <p class="text-sm text-slate-500">No document linked yet</p>
                                <a href="<?= url('/documents') ?>" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Create a new document</a>
                            </div>
                        </template>
                    </div>

                    <!-- Rubric -->
                    <div>
                        <h4 class="text-sm font-semibold text-slate-900 mb-2">Rubric</h4>
                        <div class="overflow-hidden rounded-lg border border-slate-200">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-slate-50">
                                        <th class="text-left px-4 py-2 font-semibold text-slate-700">Criterion</th>
                                        <th class="text-right px-4 py-2 font-semibold text-slate-700 w-20">Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(r, i) in selectedAssignment.rubric" :key="i">
                                        <tr class="border-t border-slate-100">
                                            <td class="px-4 py-2.5">
                                                <p class="font-medium text-slate-700" x-text="r.criterion"></p>
                                                <p class="text-xs text-slate-400 mt-0.5" x-text="r.description"></p>
                                            </td>
                                            <td class="px-4 py-2.5 text-right font-semibold text-slate-600" x-text="r.points"></td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot>
                                    <tr class="border-t border-slate-200 bg-slate-50">
                                        <td class="px-4 py-2 font-bold text-slate-900">Total</td>
                                        <td class="px-4 py-2 text-right font-bold text-slate-900" x-text="selectedAssignment.points"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Teacher comments thread -->
                    <template x-if="selectedAssignment.comments.length > 0">
                        <div>
                            <h4 class="text-sm font-semibold text-slate-900 mb-2">Teacher Comments</h4>
                            <div class="space-y-3">
                                <template x-for="(c, i) in selectedAssignment.comments" :key="i">
                                    <div class="flex items-start gap-3 p-3 rounded-lg bg-slate-50 border border-slate-100">
                                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold shrink-0" x-text="c.author.split(' ').map(w => w[0]).join('')"></span>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2 mb-0.5">
                                                <span class="text-sm font-semibold text-slate-700" x-text="c.author"></span>
                                                <span class="text-xs text-slate-400" x-text="new Date(c.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })"></span>
                                            </div>
                                            <p class="text-sm text-slate-600" x-text="c.text"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Modal footer -->
                <div class="flex items-center justify-end gap-3 p-6 border-t border-slate-100">
                    <button @click="closeModal()" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 transition">
                        Close
                    </button>
                    <a :href="'<?= url('/documents') ?>'" class="inline-flex items-center gap-2 rounded-lg px-5 py-2 text-sm font-semibold text-white transition"
                        :class="selectedAssignment.status === 'overdue' ? 'bg-rose-600 hover:bg-rose-700' : 'bg-indigo-600 hover:bg-indigo-700'">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" :d="actionIcon(selectedAssignment.status)"/></svg>
                        <span x-text="actionLabel(selectedAssignment.status)"></span>
                    </a>
                </div>
            </div>
        </div>
    </template>

</div>
