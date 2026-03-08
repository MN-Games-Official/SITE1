<?php
/**
 * Landing Page — LearnAI
 *
 * Public marketing page rendered inside layouts/landing.php.
 * The router wraps output in ob_start / ob_get_clean automatically.
 */

$pageTitle = 'AI-Powered Writing Education';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     HERO SECTION
     ═══════════════════════════════════════════════════════════════════════ -->
<section class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800">
    <!-- Decorative blobs -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
        <div class="absolute -top-40 -right-40 w-[600px] h-[600px] rounded-full bg-purple-500/20 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-[500px] h-[500px] rounded-full bg-indigo-400/20 blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] rounded-full bg-sky-400/10 blur-3xl"></div>
        <!-- Grid pattern overlay -->
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-28 sm:pt-28 sm:pb-36 lg:pt-32 lg:pb-40">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <!-- Left: Copy -->
            <div class="text-center lg:text-left">
                <div class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur-sm px-4 py-1.5 text-sm font-medium text-indigo-100 ring-1 ring-white/20 mb-8">
                    <span class="flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Now available for K-12 &amp; Higher Ed
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-[1.1]">
                    AI-Powered<br>
                    <span class="bg-gradient-to-r from-sky-300 to-emerald-300 bg-clip-text text-transparent">Writing Education</span>
                </h1>

                <p class="mt-6 text-lg sm:text-xl text-indigo-100 leading-relaxed max-w-xl mx-auto lg:mx-0">
                    Transform academic writing with intelligent AI guidance that helps students improve while maintaining academic integrity. Real-time feedback, plagiarism detection, and powerful analytics for educators.
                </p>

                <div class="mt-10 flex flex-col sm:flex-row items-center gap-4 justify-center lg:justify-start">
                    <a href="<?= url('/register') ?>" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-white px-8 py-3.5 text-base font-bold text-indigo-700 shadow-lg shadow-indigo-900/30 hover:bg-indigo-50 hover:shadow-xl transition-all duration-200 group">
                        Get Started Free
                        <svg class="w-5 h-5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </a>
                    <a href="#demo" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-white/10 backdrop-blur-sm px-8 py-3.5 text-base font-semibold text-white ring-1 ring-white/20 hover:bg-white/20 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"/></svg>
                        Book a Demo
                    </a>
                </div>

                <!-- Trust bar -->
                <div class="mt-12 flex flex-wrap items-center gap-x-8 gap-y-3 justify-center lg:justify-start text-sm text-indigo-200">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        Free for educators
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        FERPA &amp; COPPA compliant
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        No credit card required
                    </span>
                </div>
            </div>

            <!-- Right: Hero mockup -->
            <div class="relative hidden lg:block">
                <div class="relative rounded-2xl bg-white/10 backdrop-blur-md ring-1 ring-white/20 p-6 shadow-2xl">
                    <!-- Fake browser chrome -->
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-3 h-3 rounded-full bg-rose-400/80"></span>
                        <span class="w-3 h-3 rounded-full bg-amber-400/80"></span>
                        <span class="w-3 h-3 rounded-full bg-emerald-400/80"></span>
                        <span class="ml-3 flex-1 h-6 rounded-md bg-white/10"></span>
                    </div>
                    <!-- Editor mockup -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-400/30 flex items-center justify-center text-white text-sm">📝</div>
                            <div class="h-4 bg-white/20 rounded w-48"></div>
                        </div>
                        <div class="space-y-2 pl-11">
                            <div class="h-3 bg-white/15 rounded w-full"></div>
                            <div class="h-3 bg-white/15 rounded w-5/6"></div>
                            <div class="h-3 bg-white/15 rounded w-4/6"></div>
                        </div>
                        <!-- AI suggestion card -->
                        <div class="ml-11 mt-2 rounded-xl bg-emerald-500/20 ring-1 ring-emerald-400/30 p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-400 text-[10px]">✨</span>
                                <span class="text-xs font-semibold text-emerald-200">AI Suggestion</span>
                            </div>
                            <div class="space-y-1.5">
                                <div class="h-2.5 bg-emerald-300/20 rounded w-full"></div>
                                <div class="h-2.5 bg-emerald-300/20 rounded w-3/4"></div>
                            </div>
                            <div class="flex gap-2 mt-3">
                                <span class="inline-block rounded-md bg-emerald-400/30 px-3 py-1 text-[10px] font-semibold text-emerald-100">Accept</span>
                                <span class="inline-block rounded-md bg-white/10 px-3 py-1 text-[10px] font-semibold text-white/60">Dismiss</span>
                            </div>
                        </div>
                        <div class="space-y-2 pl-11 mt-2">
                            <div class="h-3 bg-white/15 rounded w-full"></div>
                            <div class="h-3 bg-white/15 rounded w-3/5"></div>
                        </div>
                    </div>
                    <!-- Stats bar -->
                    <div class="mt-5 flex items-center justify-between rounded-xl bg-white/5 ring-1 ring-white/10 px-4 py-3 text-xs text-indigo-200">
                        <span>Originality: <strong class="text-emerald-300">96%</strong></span>
                        <span>Readability: <strong class="text-sky-300">A+</strong></span>
                        <span>Words: <strong class="text-white">1,247</strong></span>
                    </div>
                </div>
                <!-- Floating badges -->
                <div class="absolute -top-4 -right-4 rounded-xl bg-white shadow-lg shadow-slate-200/50 px-4 py-2.5 flex items-center gap-2 animate-bounce" style="animation-duration: 3s;">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 text-sm">✓</span>
                    <div>
                        <p class="text-xs font-bold text-slate-800">100% Original</p>
                        <p class="text-[10px] text-slate-500">Integrity verified</p>
                    </div>
                </div>
                <div class="absolute -bottom-4 -left-4 rounded-xl bg-white shadow-lg shadow-slate-200/50 px-4 py-2.5 flex items-center gap-2 animate-bounce" style="animation-duration: 4s; animation-delay: 1s;">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 text-sm">📊</span>
                    <div>
                        <p class="text-xs font-bold text-slate-800">Grade: A</p>
                        <p class="text-[10px] text-slate-500">Above average</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Curved divider -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 56" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto"><path d="M0 24C240 56 480 56 720 40C960 24 1200 0 1440 8V56H0V24Z" fill="white"/></svg>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════════
     SOCIAL PROOF BAR
     ═══════════════════════════════════════════════════════════════════════ -->
<section class="bg-white py-12 border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-center text-sm font-medium text-slate-400 uppercase tracking-wider mb-8">Trusted by 2,000+ schools and universities</p>
        <div class="flex flex-wrap items-center justify-center gap-x-12 gap-y-6 opacity-40 grayscale">
            <span class="text-2xl font-extrabold text-slate-800 tracking-tight">Stanford</span>
            <span class="text-2xl font-extrabold text-slate-800 tracking-tight">MIT</span>
            <span class="text-2xl font-extrabold text-slate-800 tracking-tight">Harvard</span>
            <span class="text-2xl font-extrabold text-slate-800 tracking-tight">Berkeley</span>
            <span class="text-2xl font-extrabold text-slate-800 tracking-tight">Yale</span>
            <span class="text-2xl font-extrabold text-slate-800 tracking-tight">Columbia</span>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════════
     FEATURES SECTION
     ═══════════════════════════════════════════════════════════════════════ -->
<section id="features" class="bg-white py-24 sm:py-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section header -->
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="inline-flex items-center rounded-full bg-indigo-50 px-4 py-1.5 text-xs font-semibold text-indigo-700 uppercase tracking-wider ring-1 ring-indigo-100 mb-4">Features</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                Everything you need to teach writing with AI
            </h2>
            <p class="mt-4 text-lg text-slate-500 leading-relaxed">
                A comprehensive suite of tools designed for modern educators who want to harness AI responsibly while maintaining the highest standards of academic integrity.
            </p>
        </div>

        <!-- Feature grid -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1: AI Writing Assistant -->
            <div class="group relative rounded-2xl border border-slate-200 bg-white p-8 hover:shadow-xl hover:shadow-indigo-100/50 hover:border-indigo-200 transition-all duration-300">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 mb-5 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">AI Writing Assistant</h3>
                <p class="text-slate-500 leading-relaxed text-sm">
                    Intelligent writing companion that provides contextual suggestions, grammar corrections, and style improvements — guiding students without writing for them.
                </p>
            </div>

            <!-- Feature 2: Academic Integrity -->
            <div class="group relative rounded-2xl border border-slate-200 bg-white p-8 hover:shadow-xl hover:shadow-emerald-100/50 hover:border-emerald-200 transition-all duration-300">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 mb-5 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Academic Integrity</h3>
                <p class="text-slate-500 leading-relaxed text-sm">
                    Built-in plagiarism detection and AI-content analysis ensures student work remains original. Track every edit with a complete revision history.
                </p>
            </div>

            <!-- Feature 3: Real-Time Feedback -->
            <div class="group relative rounded-2xl border border-slate-200 bg-white p-8 hover:shadow-xl hover:shadow-sky-100/50 hover:border-sky-200 transition-all duration-300">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-100 text-sky-600 mb-5 group-hover:bg-sky-600 group-hover:text-white transition-colors duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Real-Time Feedback</h3>
                <p class="text-slate-500 leading-relaxed text-sm">
                    Instant, actionable feedback as students write. From sentence structure to argument strength, every aspect of writing gets expert-level analysis.
                </p>
            </div>

            <!-- Feature 4: Teacher Dashboard -->
            <div class="group relative rounded-2xl border border-slate-200 bg-white p-8 hover:shadow-xl hover:shadow-purple-100/50 hover:border-purple-200 transition-all duration-300">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 text-purple-600 mb-5 group-hover:bg-purple-600 group-hover:text-white transition-colors duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1-2.25-2.25v-2.25Z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Teacher Dashboard</h3>
                <p class="text-slate-500 leading-relaxed text-sm">
                    A powerful command center for educators. Manage assignments, monitor student progress, review submissions, and provide targeted feedback — all in one place.
                </p>
            </div>

            <!-- Feature 5: Analytics & Insights -->
            <div class="group relative rounded-2xl border border-slate-200 bg-white p-8 hover:shadow-xl hover:shadow-amber-100/50 hover:border-amber-200 transition-all duration-300">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600 mb-5 group-hover:bg-amber-600 group-hover:text-white transition-colors duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Analytics &amp; Insights</h3>
                <p class="text-slate-500 leading-relaxed text-sm">
                    Deep analytics on writing quality, student growth trajectories, class-wide trends, and AI usage patterns. Data-driven decisions made simple.
                </p>
            </div>

            <!-- Feature 6: Classroom Management -->
            <div class="group relative rounded-2xl border border-slate-200 bg-white p-8 hover:shadow-xl hover:shadow-rose-100/50 hover:border-rose-200 transition-all duration-300">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-100 text-rose-600 mb-5 group-hover:bg-rose-600 group-hover:text-white transition-colors duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Classroom Management</h3>
                <p class="text-slate-500 leading-relaxed text-sm">
                    Organize classes, set assignment parameters, define AI-usage policies per assignment, and seamlessly integrate with your existing LMS workflow.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════════
     HOW IT WORKS
     ═══════════════════════════════════════════════════════════════════════ -->
<section class="bg-gradient-to-b from-slate-50 to-white py-24 sm:py-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section header -->
        <div class="text-center max-w-3xl mx-auto mb-20">
            <span class="inline-flex items-center rounded-full bg-indigo-50 px-4 py-1.5 text-xs font-semibold text-indigo-700 uppercase tracking-wider ring-1 ring-indigo-100 mb-4">How It Works</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                From assignment to insight in four simple steps
            </h2>
            <p class="mt-4 text-lg text-slate-500 leading-relaxed">
                Getting started takes minutes, not months. Our platform integrates seamlessly into your existing teaching workflow.
            </p>
        </div>

        <!-- Steps -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-6">
            <!-- Step 1 -->
            <div class="relative text-center lg:text-left">
                <div class="flex items-center justify-center lg:justify-start mb-6">
                    <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-600 text-white text-xl font-extrabold shadow-lg shadow-indigo-200">1</span>
                </div>
                <!-- Connector line (hidden on mobile / last item) -->
                <div class="hidden lg:block absolute top-7 left-[4.5rem] w-[calc(100%-5rem)] h-px bg-gradient-to-r from-indigo-300 to-indigo-100"></div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Create Assignment</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Set up writing assignments with custom rubrics, AI-assistance levels, word counts, and due dates. Choose from templates or build your own.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="relative text-center lg:text-left">
                <div class="flex items-center justify-center lg:justify-start mb-6">
                    <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-600 text-white text-xl font-extrabold shadow-lg shadow-indigo-200">2</span>
                </div>
                <div class="hidden lg:block absolute top-7 left-[4.5rem] w-[calc(100%-5rem)] h-px bg-gradient-to-r from-indigo-300 to-indigo-100"></div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Students Write with AI</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Students compose in our guided editor with AI assistance tailored to your policies. Suggestions help them learn, not shortcut the process.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="relative text-center lg:text-left">
                <div class="flex items-center justify-center lg:justify-start mb-6">
                    <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-600 text-white text-xl font-extrabold shadow-lg shadow-indigo-200">3</span>
                </div>
                <div class="hidden lg:block absolute top-7 left-[4.5rem] w-[calc(100%-5rem)] h-px bg-gradient-to-r from-indigo-300 to-indigo-100"></div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Review &amp; Analyze</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Review submissions with AI-powered insights: originality scores, writing quality metrics, and flags for potential integrity issues — all at a glance.
                </p>
            </div>

            <!-- Step 4 -->
            <div class="relative text-center lg:text-left">
                <div class="flex items-center justify-center lg:justify-start mb-6">
                    <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-600 text-white text-xl font-extrabold shadow-lg shadow-indigo-200">4</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Track Progress</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Monitor improvement over time with detailed analytics dashboards. Identify students who need help and celebrate those who are excelling.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════════
     FOR SCHOOLS / TRUST SECTION
     ═══════════════════════════════════════════════════════════════════════ -->
<section class="bg-slate-900 py-24 sm:py-32 relative overflow-hidden">
    <!-- Background decoration -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
        <div class="absolute -top-40 right-0 w-[500px] h-[500px] rounded-full bg-indigo-600/10 blur-3xl"></div>
        <div class="absolute -bottom-40 left-0 w-[400px] h-[400px] rounded-full bg-purple-600/10 blur-3xl"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section header -->
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="inline-flex items-center rounded-full bg-indigo-500/10 px-4 py-1.5 text-xs font-semibold text-indigo-300 uppercase tracking-wider ring-1 ring-indigo-500/20 mb-4">For Schools &amp; Districts</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                Enterprise-grade security for education
            </h2>
            <p class="mt-4 text-lg text-slate-400 leading-relaxed">
                Built from the ground up to meet the strictest requirements of K-12 districts and higher education institutions.
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Security -->
            <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-8 hover:bg-white/10 transition-colors duration-300">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-500/20 text-indigo-400 mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Bank-Level Security</h3>
                <p class="text-sm text-slate-400 leading-relaxed">
                    SOC 2 Type II certified with 256-bit AES encryption at rest and TLS 1.3 in transit. Your data is protected at every layer.
                </p>
            </div>

            <!-- Privacy -->
            <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-8 hover:bg-white/10 transition-colors duration-300">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500/20 text-emerald-400 mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Privacy Compliance</h3>
                <p class="text-sm text-slate-400 leading-relaxed">
                    Fully FERPA, COPPA, and GDPR compliant. Student data is never sold or used for advertising. Signed DPAs available for every district.
                </p>
            </div>

            <!-- District deployment -->
            <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-8 hover:bg-white/10 transition-colors duration-300">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-500/20 text-sky-400 mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">District Deployment</h3>
                <p class="text-sm text-slate-400 leading-relaxed">
                    Roll out to an entire district in days, not months. Centralized admin panel, bulk provisioning, and dedicated onboarding support included.
                </p>
            </div>

            <!-- Integration -->
            <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-8 hover:bg-white/10 transition-colors duration-300">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-500/20 text-amber-400 mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/></svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">LMS Integration</h3>
                <p class="text-sm text-slate-400 leading-relaxed">
                    Seamless integration with Google Classroom, Canvas, Blackboard, Schoology, and more through LTI 1.3 and robust REST APIs.
                </p>
            </div>
        </div>

        <!-- Stats row -->
        <div class="mt-16 grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
            <div>
                <p class="text-4xl font-extrabold text-white">2,000+</p>
                <p class="mt-1 text-sm text-slate-400">Schools &amp; universities</p>
            </div>
            <div>
                <p class="text-4xl font-extrabold text-white">500K+</p>
                <p class="mt-1 text-sm text-slate-400">Students worldwide</p>
            </div>
            <div>
                <p class="text-4xl font-extrabold text-white">10M+</p>
                <p class="mt-1 text-sm text-slate-400">Documents analyzed</p>
            </div>
            <div>
                <p class="text-4xl font-extrabold text-white">99.9%</p>
                <p class="mt-1 text-sm text-slate-400">Uptime SLA</p>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════════
     PRICING SECTION
     ═══════════════════════════════════════════════════════════════════════ -->
<section id="pricing" class="bg-white py-24 sm:py-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section header -->
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="inline-flex items-center rounded-full bg-indigo-50 px-4 py-1.5 text-xs font-semibold text-indigo-700 uppercase tracking-wider ring-1 ring-indigo-100 mb-4">Pricing</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                Simple, transparent pricing
            </h2>
            <p class="mt-4 text-lg text-slate-500 leading-relaxed">
                Start free, upgrade when you need more. No hidden fees, no surprises, cancel anytime.
            </p>
        </div>

        <!-- Pricing cards -->
        <div class="grid lg:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <!-- Free tier -->
            <div class="rounded-2xl border border-slate-200 bg-white p-8 flex flex-col hover:shadow-lg transition-shadow duration-300">
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-slate-900">Free</h3>
                    <p class="text-sm text-slate-500 mt-1">Perfect for individual teachers getting started</p>
                </div>
                <div class="mb-8">
                    <span class="text-5xl font-extrabold text-slate-900">$0</span>
                    <span class="text-slate-500 ml-1">/month</span>
                </div>
                <ul class="space-y-3 mb-10 flex-1">
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        Up to 3 classes
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        30 students per class
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        Basic AI writing assistance
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        Plagiarism detection (50/mo)
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        Email support
                    </li>
                </ul>
                <a href="<?= url('/register') ?>" class="block w-full text-center rounded-xl border-2 border-slate-200 px-6 py-3 text-sm font-bold text-slate-700 hover:border-indigo-300 hover:text-indigo-700 hover:bg-indigo-50 transition-all duration-200">
                    Get Started Free
                </a>
            </div>

            <!-- Professional tier (highlighted) -->
            <div class="rounded-2xl border-2 border-indigo-600 bg-white p-8 flex flex-col relative shadow-xl shadow-indigo-100/50 lg:scale-105">
                <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                    <span class="inline-flex items-center rounded-full bg-indigo-600 px-4 py-1 text-xs font-bold text-white shadow-lg">Most Popular</span>
                </div>
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-slate-900">Professional</h3>
                    <p class="text-sm text-slate-500 mt-1">For schools and departments ready to scale</p>
                </div>
                <div class="mb-8">
                    <span class="text-5xl font-extrabold text-slate-900">$12</span>
                    <span class="text-slate-500 ml-1">/student/month</span>
                </div>
                <ul class="space-y-3 mb-10 flex-1">
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        Unlimited classes &amp; students
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        Advanced AI writing assistant
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        Unlimited plagiarism checks
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        Advanced analytics dashboard
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        Google Classroom integration
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        Priority email &amp; chat support
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        Custom AI policy controls
                    </li>
                </ul>
                <a href="<?= url('/register') ?>" class="block w-full text-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-200/50 hover:bg-indigo-700 hover:shadow-xl transition-all duration-200">
                    Start Free Trial
                </a>
            </div>

            <!-- Enterprise tier -->
            <div class="rounded-2xl border border-slate-200 bg-white p-8 flex flex-col hover:shadow-lg transition-shadow duration-300">
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-slate-900">Enterprise</h3>
                    <p class="text-sm text-slate-500 mt-1">For districts and universities at scale</p>
                </div>
                <div class="mb-8">
                    <span class="text-5xl font-extrabold text-slate-900">Custom</span>
                </div>
                <ul class="space-y-3 mb-10 flex-1">
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        Everything in Professional
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        SSO / SAML authentication
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        LTI 1.3 &amp; API access
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        District-wide admin console
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        Custom data retention policies
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        Dedicated success manager
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        99.9% uptime SLA
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        On-site training &amp; PD workshops
                    </li>
                </ul>
                <a href="#demo" class="block w-full text-center rounded-xl border-2 border-slate-200 px-6 py-3 text-sm font-bold text-slate-700 hover:border-indigo-300 hover:text-indigo-700 hover:bg-indigo-50 transition-all duration-200">
                    Contact Sales
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════════
     TESTIMONIALS
     ═══════════════════════════════════════════════════════════════════════ -->
<section id="testimonials" class="bg-gradient-to-b from-slate-50 to-white py-24 sm:py-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section header -->
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="inline-flex items-center rounded-full bg-indigo-50 px-4 py-1.5 text-xs font-semibold text-indigo-700 uppercase tracking-wider ring-1 ring-indigo-100 mb-4">Testimonials</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                Loved by educators everywhere
            </h2>
            <p class="mt-4 text-lg text-slate-500 leading-relaxed">
                See what teachers and administrators are saying about how <?= e(config('name')) ?> is transforming writing instruction.
            </p>
        </div>

        <!-- Testimonial cards -->
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Testimonial 1 -->
            <div class="rounded-2xl bg-white border border-slate-200 p-8 shadow-sm hover:shadow-lg transition-shadow duration-300 flex flex-col">
                <div class="flex items-center gap-1 mb-4">
                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/></svg>
                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/></svg>
                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/></svg>
                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/></svg>
                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/></svg>
                </div>
                <blockquote class="text-slate-600 leading-relaxed flex-1 mb-6">
                    &ldquo;LearnAI has completely transformed how I teach writing. The AI assistant helps my students develop stronger arguments without doing the work for them. I&rsquo;ve seen a 40% improvement in essay quality since we started using it.&rdquo;
                </blockquote>
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-indigo-700 font-bold text-sm">SM</div>
                    <div>
                        <p class="text-sm font-bold text-slate-900">Sarah Mitchell</p>
                        <p class="text-xs text-slate-500">AP English Teacher, Lincoln High School</p>
                    </div>
                </div>
            </div>

            <!-- Testimonial 2 -->
            <div class="rounded-2xl bg-white border border-slate-200 p-8 shadow-sm hover:shadow-lg transition-shadow duration-300 flex flex-col">
                <div class="flex items-center gap-1 mb-4">
                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/></svg>
                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/></svg>
                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/></svg>
                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/></svg>
                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/></svg>
                </div>
                <blockquote class="text-slate-600 leading-relaxed flex-1 mb-6">
                    &ldquo;As a district administrator, I was concerned about AI in classrooms. LearnAI gave us the controls we needed — teachers set AI policies per assignment, and the analytics give us full visibility into how AI is being used. It&rsquo;s the responsible approach.&rdquo;
                </blockquote>
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 font-bold text-sm">DR</div>
                    <div>
                        <p class="text-sm font-bold text-slate-900">Dr. James Rivera</p>
                        <p class="text-xs text-slate-500">Director of Technology, Westfield USD</p>
                    </div>
                </div>
            </div>

            <!-- Testimonial 3 -->
            <div class="rounded-2xl bg-white border border-slate-200 p-8 shadow-sm hover:shadow-lg transition-shadow duration-300 flex flex-col">
                <div class="flex items-center gap-1 mb-4">
                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/></svg>
                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/></svg>
                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/></svg>
                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/></svg>
                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/></svg>
                </div>
                <blockquote class="text-slate-600 leading-relaxed flex-1 mb-6">
                    &ldquo;My students actually enjoy writing now. The real-time feedback keeps them engaged, and the revision tracking shows them how much they&rsquo;ve improved. One student told me it felt like having a personal writing tutor available 24/7.&rdquo;
                </blockquote>
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-purple-100 text-purple-700 font-bold text-sm">EP</div>
                    <div>
                        <p class="text-sm font-bold text-slate-900">Emily Parker</p>
                        <p class="text-xs text-slate-500">8th Grade ELA, Oak Valley Middle School</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════════
     FINAL CTA
     ═══════════════════════════════════════════════════════════════════════ -->
<section class="relative overflow-hidden bg-indigo-600 py-24 sm:py-28">
    <!-- Decorative -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-indigo-500/50 blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-purple-500/30 blur-3xl"></div>
    </div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight">
            Ready to transform writing education?
        </h2>
        <p class="mt-6 text-lg text-indigo-100 leading-relaxed max-w-2xl mx-auto">
            Join thousands of educators who are using <?= e(config('name')) ?> to help students become better writers. Start free today — no credit card required.
        </p>
        <div class="mt-10 flex flex-col sm:flex-row items-center gap-4 justify-center">
            <a href="<?= url('/register') ?>" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-white px-8 py-4 text-base font-bold text-indigo-700 shadow-lg hover:bg-indigo-50 hover:shadow-xl transition-all duration-200 group">
                Get Started Free
                <svg class="w-5 h-5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
            </a>
            <a href="#demo" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-500 px-8 py-4 text-base font-semibold text-white ring-1 ring-white/20 hover:bg-indigo-400 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                Contact Sales
            </a>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════════
     FAQ SECTION
     ═══════════════════════════════════════════════════════════════════════ -->
<section id="faq" class="bg-white py-24 sm:py-32">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section header -->
        <div class="text-center mb-16">
            <span class="inline-flex items-center rounded-full bg-indigo-50 px-4 py-1.5 text-xs font-semibold text-indigo-700 uppercase tracking-wider ring-1 ring-indigo-100 mb-4">FAQ</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                Frequently asked questions
            </h2>
        </div>

        <!-- Accordion -->
        <div class="space-y-4" x-data="{ active: null }">
            <!-- Q1 -->
            <div class="rounded-xl border border-slate-200 overflow-hidden">
                <button @click="active = active === 1 ? null : 1" class="w-full flex items-center justify-between px-6 py-5 text-left hover:bg-slate-50 transition-colors">
                    <span class="text-base font-semibold text-slate-900">Does the AI write essays for students?</span>
                    <svg class="w-5 h-5 text-slate-400 flex-shrink-0 transition-transform duration-200" :class="active === 1 && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                </button>
                <div x-show="active === 1" x-collapse x-cloak class="px-6 pb-5">
                    <p class="text-sm text-slate-500 leading-relaxed">
                        Absolutely not. Our AI is designed to guide, not generate. It provides suggestions for improving grammar, sentence structure, and argument strength, but students must do the actual writing. Teachers can also configure the level of AI assistance per assignment to match their pedagogical goals.
                    </p>
                </div>
            </div>

            <!-- Q2 -->
            <div class="rounded-xl border border-slate-200 overflow-hidden">
                <button @click="active = active === 2 ? null : 2" class="w-full flex items-center justify-between px-6 py-5 text-left hover:bg-slate-50 transition-colors">
                    <span class="text-base font-semibold text-slate-900">Is student data safe and private?</span>
                    <svg class="w-5 h-5 text-slate-400 flex-shrink-0 transition-transform duration-200" :class="active === 2 && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                </button>
                <div x-show="active === 2" x-collapse x-cloak class="px-6 pb-5">
                    <p class="text-sm text-slate-500 leading-relaxed">
                        Yes. We are fully FERPA, COPPA, and GDPR compliant. Student data is encrypted at rest and in transit, never sold or shared with third parties, and we sign Data Processing Agreements with every district. We undergo annual SOC 2 Type II audits to ensure compliance.
                    </p>
                </div>
            </div>

            <!-- Q3 -->
            <div class="rounded-xl border border-slate-200 overflow-hidden">
                <button @click="active = active === 3 ? null : 3" class="w-full flex items-center justify-between px-6 py-5 text-left hover:bg-slate-50 transition-colors">
                    <span class="text-base font-semibold text-slate-900">How long does it take to set up?</span>
                    <svg class="w-5 h-5 text-slate-400 flex-shrink-0 transition-transform duration-200" :class="active === 3 && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                </button>
                <div x-show="active === 3" x-collapse x-cloak class="px-6 pb-5">
                    <p class="text-sm text-slate-500 leading-relaxed">
                        Individual teachers can get started in under 5 minutes. School-wide deployments typically take 1-2 weeks including training. District-wide rollouts with SSO integration and custom configuration take 2-4 weeks. Our onboarding team guides you through every step.
                    </p>
                </div>
            </div>

            <!-- Q4 -->
            <div class="rounded-xl border border-slate-200 overflow-hidden">
                <button @click="active = active === 4 ? null : 4" class="w-full flex items-center justify-between px-6 py-5 text-left hover:bg-slate-50 transition-colors">
                    <span class="text-base font-semibold text-slate-900">Which LMS platforms do you integrate with?</span>
                    <svg class="w-5 h-5 text-slate-400 flex-shrink-0 transition-transform duration-200" :class="active === 4 && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                </button>
                <div x-show="active === 4" x-collapse x-cloak class="px-6 pb-5">
                    <p class="text-sm text-slate-500 leading-relaxed">
                        We integrate with Google Classroom, Canvas, Blackboard, Schoology, Moodle, and any LMS that supports LTI 1.3. We also provide REST APIs for custom integrations. Grade sync, roster import, and assignment sync are all supported.
                    </p>
                </div>
            </div>

            <!-- Q5 -->
            <div class="rounded-xl border border-slate-200 overflow-hidden">
                <button @click="active = active === 5 ? null : 5" class="w-full flex items-center justify-between px-6 py-5 text-left hover:bg-slate-50 transition-colors">
                    <span class="text-base font-semibold text-slate-900">Can I try it before committing?</span>
                    <svg class="w-5 h-5 text-slate-400 flex-shrink-0 transition-transform duration-200" :class="active === 5 && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                </button>
                <div x-show="active === 5" x-collapse x-cloak class="px-6 pb-5">
                    <p class="text-sm text-slate-500 leading-relaxed">
                        Of course! Our Free plan lets you use the platform with up to 3 classes and 30 students per class — no credit card required. The Professional plan also includes a 30-day free trial so you can explore all features before making a decision.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
