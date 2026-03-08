<?php
/**
 * Documents Page — LearnAI
 *
 * Browse, search, filter, and manage student writing documents.
 */

$pageTitle = 'My Documents';

// ── Fake document data ───────────────────────────────────────
$documents = [
    [
        'id'         => 1,
        'title'      => 'The Impact of AI on Modern Education',
        'type'       => 'essay',
        'status'     => 'Final',
        'word_count' => 2847,
        'updated_at' => date('Y-m-d H:i:s', strtotime('-22 minutes')),
        'assignment' => 'Essay on Technology',
        'class'      => 'English 101',
    ],
    [
        'id'         => 2,
        'title'      => 'Lab Report: Chemical Reactions and Catalysts',
        'type'       => 'report',
        'status'     => 'Draft',
        'word_count' => 1205,
        'updated_at' => date('Y-m-d H:i:s', strtotime('-3 hours')),
        'assignment' => null,
        'class'      => null,
    ],
    [
        'id'         => 3,
        'title'      => 'Literature Review: Machine Learning in Healthcare',
        'type'       => 'research',
        'status'     => 'In Progress',
        'word_count' => 4312,
        'updated_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
        'assignment' => 'Research Project',
        'class'      => 'CS 450',
    ],
    [
        'id'         => 4,
        'title'      => 'The Old Lighthouse — Short Story',
        'type'       => 'creative',
        'status'     => 'Final',
        'word_count' => 3560,
        'updated_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
        'assignment' => 'Creative Writing Portfolio',
        'class'      => 'CRWR 200',
    ],
    [
        'id'         => 5,
        'title'      => 'Quarterly Progress Presentation',
        'type'       => 'presentation',
        'status'     => 'Submitted',
        'word_count' => 980,
        'updated_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
        'assignment' => 'Mid-Term Presentation',
        'class'      => 'BUS 310',
    ],
    [
        'id'         => 6,
        'title'      => 'Lecture Notes: Organic Chemistry Week 5',
        'type'       => 'notes',
        'status'     => 'Draft',
        'word_count' => 642,
        'updated_at' => date('Y-m-d H:i:s', strtotime('-4 days')),
        'assignment' => null,
        'class'      => null,
    ],
    [
        'id'         => 7,
        'title'      => 'Comparative Analysis of Renewable Energy Sources',
        'type'       => 'research',
        'status'     => 'In Progress',
        'word_count' => 5120,
        'updated_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
        'assignment' => 'Term Paper',
        'class'      => 'ENV 220',
    ],
    [
        'id'         => 8,
        'title'      => 'Hamlet Character Study: The Duality of Inaction',
        'type'       => 'essay',
        'status'     => 'Submitted',
        'word_count' => 1870,
        'updated_at' => date('Y-m-d H:i:s', strtotime('-7 days')),
        'assignment' => 'Shakespeare Essay',
        'class'      => 'ENG 320',
    ],
    [
        'id'         => 9,
        'title'      => 'Internship Report: Summer 2024 at DataCorp',
        'type'       => 'report',
        'status'     => 'Final',
        'word_count' => 3200,
        'updated_at' => date('Y-m-d H:i:s', strtotime('-10 days')),
        'assignment' => 'Internship Reflection',
        'class'      => 'COOP 400',
    ],
    [
        'id'         => 10,
        'title'      => 'Echoes in the Rain — Poetry Collection',
        'type'       => 'creative',
        'status'     => 'Draft',
        'word_count' => 415,
        'updated_at' => date('Y-m-d H:i:s', strtotime('-12 days')),
        'assignment' => null,
        'class'      => null,
    ],
];

// ── Type styling config ──────────────────────────────────────
$typeConfig = [
    'essay'        => ['label' => 'Essay',        'bg' => 'bg-violet-100',  'text' => 'text-violet-700',  'icon_bg' => 'bg-violet-500'],
    'report'       => ['label' => 'Report',       'bg' => 'bg-amber-100',   'text' => 'text-amber-700',   'icon_bg' => 'bg-amber-500'],
    'research'     => ['label' => 'Research',      'bg' => 'bg-cyan-100',    'text' => 'text-cyan-700',    'icon_bg' => 'bg-cyan-500'],
    'creative'     => ['label' => 'Creative',      'bg' => 'bg-pink-100',    'text' => 'text-pink-700',    'icon_bg' => 'bg-pink-500'],
    'presentation' => ['label' => 'Presentation',  'bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'icon_bg' => 'bg-emerald-500'],
    'notes'        => ['label' => 'Notes',         'bg' => 'bg-slate-100',   'text' => 'text-slate-700',   'icon_bg' => 'bg-slate-500'],
];

// ── Status badge config ──────────────────────────────────────
$statusColors = [
    'Draft'       => ['bg' => 'bg-slate-100',   'text' => 'text-slate-700',   'dot' => 'bg-slate-400'],
    'In Progress' => ['bg' => 'bg-blue-100',    'text' => 'text-blue-700',    'dot' => 'bg-blue-500'],
    'Final'       => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500'],
    'Submitted'   => ['bg' => 'bg-indigo-100',  'text' => 'text-indigo-700',  'dot' => 'bg-indigo-500'],
];

// ── Summary stats ────────────────────────────────────────────
$totalDocuments = count($documents);
$totalWords     = array_sum(array_column($documents, 'word_count'));
?>

<!-- ═══════════════════════════════════════════════════════════ -->
<!--  DOCUMENTS PAGE — Alpine.js root                            -->
<!-- ═══════════════════════════════════════════════════════════ -->
<div
    x-data="{
        view: 'grid',
        search: '',
        statusFilter: 'all',
        sortBy: 'newest',
        showDeleteModal: false,
        documentToDelete: null,

        documents: <?= json_encode($documents, JSON_HEX_APOS | JSON_HEX_TAG) ?>,

        get filteredDocuments() {
            let docs = [...this.documents];

            // Filter by search term
            if (this.search.trim() !== '') {
                const q = this.search.toLowerCase();
                docs = docs.filter(d =>
                    d.title.toLowerCase().includes(q) ||
                    d.type.toLowerCase().includes(q) ||
                    (d.assignment && d.assignment.toLowerCase().includes(q)) ||
                    (d.class && d.class.toLowerCase().includes(q))
                );
            }

            // Filter by status
            if (this.statusFilter !== 'all') {
                docs = docs.filter(d => d.status === this.statusFilter);
            }

            // Sort
            switch (this.sortBy) {
                case 'name_asc':
                    docs.sort((a, b) => a.title.localeCompare(b.title));
                    break;
                case 'name_desc':
                    docs.sort((a, b) => b.title.localeCompare(a.title));
                    break;
                case 'oldest':
                    docs.sort((a, b) => new Date(a.updated_at) - new Date(b.updated_at));
                    break;
                case 'words':
                    docs.sort((a, b) => b.word_count - a.word_count);
                    break;
                case 'newest':
                default:
                    docs.sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));
                    break;
            }

            return docs;
        },

        get totalFilteredWords() {
            return this.filteredDocuments.reduce((sum, d) => sum + d.word_count, 0);
        },

        get hasActiveFilters() {
            return this.search.trim() !== '' || this.statusFilter !== 'all';
        },

        clearFilters() {
            this.search = '';
            this.statusFilter = 'all';
            this.sortBy = 'newest';
        },

        confirmDelete(doc) {
            this.documentToDelete = doc;
            this.showDeleteModal = true;
        },

        deleteDocument() {
            if (this.documentToDelete) {
                this.documents = this.documents.filter(d => d.id !== this.documentToDelete.id);
            }
            this.showDeleteModal = false;
            this.documentToDelete = null;
        },

        formatWords(n) {
            if (n >= 1000) return (n / 1000).toFixed(1) + 'K';
            return n.toString();
        },

        relativeTime(dateStr) {
            const now  = new Date();
            const then = new Date(dateStr);
            const diff = Math.floor((now - then) / 1000);
            if (diff < 60)    return diff + 's ago';
            if (diff < 3600)  return Math.floor(diff / 60) + 'm ago';
            if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
            const days = Math.floor(diff / 86400);
            if (days < 30)    return days + 'd ago';
            return then.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        },

        typeLabel(type) {
            const map = { essay: 'Essay', report: 'Report', research: 'Research', creative: 'Creative', presentation: 'Presentation', notes: 'Notes' };
            return map[type] || type;
        }
    }"
>

    <!-- ════════════════════════════════════════════════════════ -->
    <!--  PAGE HEADER                                              -->
    <!-- ════════════════════════════════════════════════════════ -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">My Documents</h1>
            <p class="text-sm text-slate-500 mt-1">Create, organize, and track all your writing in one place.</p>
        </div>

        <div class="flex items-center gap-3">
            <!-- View toggle -->
            <div class="hidden sm:flex items-center rounded-lg border border-slate-200 bg-white p-0.5">
                <button
                    @click="view = 'grid'"
                    :class="view === 'grid' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                    class="inline-flex items-center justify-center rounded-md px-2.5 py-1.5 text-sm font-medium transition"
                    title="Grid view"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7" rx="1" />
                        <rect x="14" y="3" width="7" height="7" rx="1" />
                        <rect x="3" y="14" width="7" height="7" rx="1" />
                        <rect x="14" y="14" width="7" height="7" rx="1" />
                    </svg>
                </button>
                <button
                    @click="view = 'list'"
                    :class="view === 'list' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                    class="inline-flex items-center justify-center rounded-md px-2.5 py-1.5 text-sm font-medium transition"
                    title="List view"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- New Document button -->
            <a
                href="<?= url('/documents/new') ?>"
                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-200/50 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                New Document
            </a>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════ -->
    <!--  FILTERS BAR                                              -->
    <!-- ════════════════════════════════════════════════════════ -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6">
        <div class="flex flex-col md:flex-row md:items-center gap-3">
            <!-- Search -->
            <div class="relative flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                <input
                    type="text"
                    x-model.debounce.200ms="search"
                    placeholder="Search documents, assignments, classes…"
                    class="block w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-300 focus:bg-white focus:ring-2 focus:ring-indigo-100 transition"
                />
            </div>

            <!-- Status filter -->
            <div class="flex items-center gap-3">
                <select
                    x-model="statusFilter"
                    class="rounded-lg border border-slate-200 bg-slate-50 py-2 pl-3 pr-8 text-sm text-slate-700 focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 transition"
                >
                    <option value="all">All Statuses</option>
                    <option value="Draft">Draft</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Final">Final</option>
                    <option value="Submitted">Submitted</option>
                </select>

                <!-- Sort -->
                <select
                    x-model="sortBy"
                    class="rounded-lg border border-slate-200 bg-slate-50 py-2 pl-3 pr-8 text-sm text-slate-700 focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 transition"
                >
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="name_asc">Name A–Z</option>
                    <option value="name_desc">Name Z–A</option>
                    <option value="words">Word Count</option>
                </select>

                <!-- Clear filters -->
                <button
                    x-show="hasActiveFilters"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    @click="clearFilters()"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Clear
                </button>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════ -->
    <!--  DOCUMENT COUNT SUMMARY                                   -->
    <!-- ════════════════════════════════════════════════════════ -->
    <div class="flex flex-wrap items-center justify-between gap-2 mb-5 px-1">
        <p class="text-sm text-slate-500">
            Showing
            <span class="font-semibold text-slate-700" x-text="filteredDocuments.length"></span>
            of
            <span class="font-semibold text-slate-700" x-text="documents.length"></span>
            documents
        </p>
        <p class="text-sm text-slate-500">
            <svg class="inline w-4 h-4 text-slate-400 -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            <span class="font-semibold text-slate-700" x-text="formatWords(totalFilteredWords)"></span> words total
        </p>
    </div>

    <!-- ════════════════════════════════════════════════════════ -->
    <!--  GRID VIEW                                                -->
    <!-- ════════════════════════════════════════════════════════ -->
    <div x-show="view === 'grid' && filteredDocuments.length > 0" x-transition>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            <template x-for="doc in filteredDocuments" :key="doc.id">
                <div class="group relative bg-white rounded-xl shadow-sm border border-slate-200 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col">
                    <!-- Card body -->
                    <div class="p-5 flex-1 flex flex-col">
                        <!-- Type badge + status row -->
                        <div class="flex items-center justify-between mb-3">
                            <!-- Type icon -->
                            <span
                                class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-medium"
                                :class="{
                                    'bg-violet-100 text-violet-700':  doc.type === 'essay',
                                    'bg-amber-100 text-amber-700':    doc.type === 'report',
                                    'bg-cyan-100 text-cyan-700':      doc.type === 'research',
                                    'bg-pink-100 text-pink-700':      doc.type === 'creative',
                                    'bg-emerald-100 text-emerald-700': doc.type === 'presentation',
                                    'bg-slate-100 text-slate-700':    doc.type === 'notes'
                                }"
                            >
                                <!-- Type-specific icon -->
                                <template x-if="doc.type === 'essay'">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                </template>
                                <template x-if="doc.type === 'report'">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
                                </template>
                                <template x-if="doc.type === 'research'">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19 14.5M14.25 3.104c.251.023.501.05.75.082M19 14.5l-1.47 4.897A2.25 2.25 0 0115.378 21H8.622a2.25 2.25 0 01-2.152-1.603L5 14.5m14 0H5" /></svg>
                                </template>
                                <template x-if="doc.type === 'creative'">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42" /></svg>
                                </template>
                                <template x-if="doc.type === 'presentation'">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5" /></svg>
                                </template>
                                <template x-if="doc.type === 'notes'">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                </template>
                                <span x-text="typeLabel(doc.type)"></span>
                            </span>

                            <!-- Status dot + label -->
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium"
                                :class="{
                                    'bg-slate-100 text-slate-700':   doc.status === 'Draft',
                                    'bg-blue-100 text-blue-700':     doc.status === 'In Progress',
                                    'bg-emerald-100 text-emerald-700': doc.status === 'Final',
                                    'bg-indigo-100 text-indigo-700': doc.status === 'Submitted'
                                }"
                            >
                                <span
                                    class="h-1.5 w-1.5 rounded-full"
                                    :class="{
                                        'bg-slate-400':   doc.status === 'Draft',
                                        'bg-blue-500':    doc.status === 'In Progress',
                                        'bg-emerald-500': doc.status === 'Final',
                                        'bg-indigo-500':  doc.status === 'Submitted'
                                    }"
                                ></span>
                                <span x-text="doc.status"></span>
                            </span>
                        </div>

                        <!-- Title -->
                        <h3 class="text-sm font-semibold text-slate-900 leading-snug mb-2 line-clamp-2" x-text="doc.title"></h3>

                        <!-- Meta row -->
                        <div class="flex items-center gap-3 text-xs text-slate-500 mt-auto pt-3 border-t border-slate-100">
                            <!-- Word count -->
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 011.037-.443 48.282 48.282 0 005.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                                </svg>
                                <span x-text="doc.word_count.toLocaleString() + ' words'"></span>
                            </span>

                            <!-- Last modified -->
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span x-text="relativeTime(doc.updated_at)"></span>
                            </span>
                        </div>

                        <!-- Assignment link -->
                        <template x-if="doc.assignment">
                            <div class="mt-2.5 pt-2.5 border-t border-slate-100">
                                <a
                                    :href="'<?= url('/assignments/') ?>' + doc.id"
                                    class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 font-medium transition"
                                >
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m9.07-9.07l4.5-4.5a4.5 4.5 0 016.364 6.364l-1.757 1.757" />
                                    </svg>
                                    <span x-text="doc.assignment"></span>
                                    <template x-if="doc.class">
                                        <span class="text-slate-400" x-text="'· ' + doc.class"></span>
                                    </template>
                                </a>
                            </div>
                        </template>
                    </div>

                    <!-- Hover actions overlay -->
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-white via-white/95 to-transparent pt-8 pb-4 px-5 rounded-b-xl opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <div class="flex items-center justify-center gap-2">
                            <a
                                :href="'<?= url('/documents/') ?>' + doc.id"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700 transition"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                                Open
                            </a>
                            <button
                                @click.prevent="documents.push({...doc, id: Date.now(), title: doc.title + ' (Copy)', status: 'Draft', updated_at: new Date().toISOString()})"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" /></svg>
                                Duplicate
                            </button>
                            <button
                                @click.prevent="confirmDelete(doc)"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 transition"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════ -->
    <!--  LIST VIEW                                                -->
    <!-- ════════════════════════════════════════════════════════ -->
    <div x-show="view === 'list' && filteredDocuments.length > 0" x-transition>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="py-3.5 pl-5 pr-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                <button @click="sortBy = sortBy === 'name_asc' ? 'name_desc' : 'name_asc'" class="inline-flex items-center gap-1 hover:text-slate-900 transition">
                                    Name
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" /></svg>
                                </button>
                            </th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Type</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                <button @click="sortBy = 'words'" class="inline-flex items-center gap-1 hover:text-slate-900 transition">
                                    Words
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" /></svg>
                                </button>
                            </th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                <button @click="sortBy = sortBy === 'newest' ? 'oldest' : 'newest'" class="inline-flex items-center gap-1 hover:text-slate-900 transition">
                                    Modified
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" /></svg>
                                </button>
                            </th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Assignment</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-5">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="doc in filteredDocuments" :key="doc.id">
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- Name -->
                                <td class="py-3.5 pl-5 pr-3 text-sm max-w-xs">
                                    <a :href="'<?= url('/documents/') ?>' + doc.id" class="font-semibold text-slate-900 hover:text-indigo-600 transition truncate block" x-text="doc.title"></a>
                                </td>
                                <!-- Type -->
                                <td class="whitespace-nowrap px-3 py-3.5 text-sm">
                                    <span
                                        class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium"
                                        :class="{
                                            'bg-violet-100 text-violet-700':   doc.type === 'essay',
                                            'bg-amber-100 text-amber-700':     doc.type === 'report',
                                            'bg-cyan-100 text-cyan-700':       doc.type === 'research',
                                            'bg-pink-100 text-pink-700':       doc.type === 'creative',
                                            'bg-emerald-100 text-emerald-700': doc.type === 'presentation',
                                            'bg-slate-100 text-slate-700':     doc.type === 'notes'
                                        }"
                                        x-text="typeLabel(doc.type)"
                                    ></span>
                                </td>
                                <!-- Status -->
                                <td class="whitespace-nowrap px-3 py-3.5 text-sm">
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium"
                                        :class="{
                                            'bg-slate-100 text-slate-700':     doc.status === 'Draft',
                                            'bg-blue-100 text-blue-700':       doc.status === 'In Progress',
                                            'bg-emerald-100 text-emerald-700': doc.status === 'Final',
                                            'bg-indigo-100 text-indigo-700':   doc.status === 'Submitted'
                                        }"
                                    >
                                        <span
                                            class="h-1.5 w-1.5 rounded-full"
                                            :class="{
                                                'bg-slate-400':   doc.status === 'Draft',
                                                'bg-blue-500':    doc.status === 'In Progress',
                                                'bg-emerald-500': doc.status === 'Final',
                                                'bg-indigo-500':  doc.status === 'Submitted'
                                            }"
                                        ></span>
                                        <span x-text="doc.status"></span>
                                    </span>
                                </td>
                                <!-- Words -->
                                <td class="whitespace-nowrap px-3 py-3.5 text-sm text-slate-600" x-text="doc.word_count.toLocaleString()"></td>
                                <!-- Modified -->
                                <td class="whitespace-nowrap px-3 py-3.5 text-sm text-slate-500" x-text="relativeTime(doc.updated_at)"></td>
                                <!-- Assignment -->
                                <td class="whitespace-nowrap px-3 py-3.5 text-sm">
                                    <template x-if="doc.assignment">
                                        <span class="text-slate-700">
                                            <span x-text="doc.assignment"></span>
                                            <template x-if="doc.class">
                                                <span class="text-slate-400 ml-1" x-text="'(' + doc.class + ')'"></span>
                                            </template>
                                        </span>
                                    </template>
                                    <template x-if="!doc.assignment">
                                        <span class="text-slate-300">—</span>
                                    </template>
                                </td>
                                <!-- Actions -->
                                <td class="whitespace-nowrap py-3.5 pl-3 pr-5 text-right text-sm">
                                    <div class="flex items-center justify-end gap-1">
                                        <a
                                            :href="'<?= url('/documents/') ?>' + doc.id"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition"
                                            title="Open"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                                        </a>
                                        <button
                                            @click="documents.push({...doc, id: Date.now(), title: doc.title + ' (Copy)', status: 'Draft', updated_at: new Date().toISOString()})"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition"
                                            title="Duplicate"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" /></svg>
                                        </button>
                                        <button
                                            @click="confirmDelete(doc)"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition"
                                            title="Delete"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════ -->
    <!--  EMPTY STATE                                              -->
    <!-- ════════════════════════════════════════════════════════ -->
    <div
        x-show="filteredDocuments.length === 0"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="flex flex-col items-center justify-center py-20 px-6"
    >
        <!-- Illustration -->
        <div class="relative mb-6">
            <div class="absolute -inset-4 rounded-full bg-indigo-50"></div>
            <svg class="relative w-20 h-20 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12H9.75m1.5 3h1.5m-1.5-6h6m-9-1.5H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
        </div>

        <h3 class="text-lg font-semibold text-slate-900 mb-1">No documents found</h3>
        <p class="text-sm text-slate-500 text-center max-w-sm mb-6">
            <template x-if="hasActiveFilters">
                <span>No documents match your current filters. Try adjusting your search or clearing filters.</span>
            </template>
            <template x-if="!hasActiveFilters">
                <span>You haven't created any documents yet. Start writing your first masterpiece!</span>
            </template>
        </p>

        <div class="flex items-center gap-3">
            <template x-if="hasActiveFilters">
                <button
                    @click="clearFilters()"
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Clear Filters
                </button>
            </template>
            <a
                href="<?= url('/documents/new') ?>"
                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-200/50 hover:bg-indigo-700 transition"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Create Your First Document
            </a>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════ -->
    <!--  DELETE CONFIRMATION MODAL                                -->
    <!-- ════════════════════════════════════════════════════════ -->
    <div
        x-show="showDeleteModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @keydown.escape.window="showDeleteModal = false"
        style="display: none;"
    >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showDeleteModal = false"></div>

        <!-- Dialog -->
        <div
            x-show="showDeleteModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative w-full max-w-md bg-white rounded-2xl shadow-xl p-6"
        >
            <!-- Icon -->
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>

            <h3 class="text-lg font-semibold text-slate-900 text-center mb-1">Delete Document</h3>
            <p class="text-sm text-slate-500 text-center mb-1">
                Are you sure you want to delete
            </p>
            <p class="text-sm font-semibold text-slate-900 text-center mb-4" x-text="documentToDelete ? '\"' + documentToDelete.title + '\"' : ''"></p>
            <p class="text-xs text-red-500 text-center bg-red-50 rounded-lg px-3 py-2 mb-6">
                <svg class="inline w-3.5 h-3.5 -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
                This action cannot be undone. The document and all its contents will be permanently removed.
            </p>

            <div class="flex items-center gap-3">
                <button
                    @click="showDeleteModal = false; documentToDelete = null"
                    class="flex-1 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition"
                >
                    Cancel
                </button>
                <button
                    @click="deleteDocument()"
                    class="flex-1 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition"
                >
                    <svg class="inline w-4 h-4 -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                    Delete Document
                </button>
            </div>
        </div>
    </div>

</div>
