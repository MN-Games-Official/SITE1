<?php
/**
 * Forgot Password Page — LearnAI
 *
 * Rendered inside layouts/auth.php (centred card with brand mark).
 * Two-state UI: email entry form → success confirmation,
 * fully managed by Alpine.js without page reload.
 */

$pageTitle = 'Reset Password';
?>

<div x-data="{
    email: '',
    error: '',
    submitting: false,
    sent: false,

    validate() {
        this.error = '';

        const trimmed = this.email.trim();
        if (!trimmed) {
            this.error = 'Email address is required.';
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(trimmed)) {
            this.error = 'Please enter a valid email address.';
        }

        return !this.error;
    },

    submit() {
        if (!this.validate()) return;
        this.submitting = true;

        // Simulate API call — replace with real fetch() in production
        setTimeout(() => {
            this.submitting = false;
            this.sent = true;
        }, 1000);
    },

    reset() {
        this.sent = false;
        this.email = '';
        this.error = '';
        this.submitting = false;
    }
}">

    <!-- ══════════════════════════════════════════════════════
         STATE 1 — Email entry form
         ══════════════════════════════════════════════════════ -->
    <div x-show="!sent">

        <!-- Header with icon -->
        <div class="text-center mb-8">
            <div class="flex items-center justify-center mb-5">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-100">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
                    </svg>
                </div>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                Forgot your password?
            </h1>
            <p class="mt-2 text-sm text-slate-500 leading-relaxed max-w-xs mx-auto">
                No worries. Enter the email address associated with your account
                and we&rsquo;ll send you a link to reset your password.
            </p>
        </div>

        <!-- Reset form -->
        <form @submit.prevent="submit" novalidate class="space-y-5">
            <?= csrf_field() ?>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Email address
                </label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                        </svg>
                    </div>
                    <input
                        id="email"
                        type="email"
                        x-model="email"
                        @input="error = ''"
                        autocomplete="email"
                        placeholder="you@school.edu"
                        class="block w-full rounded-xl border pl-11 pr-4 py-3 text-sm shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
                        :class="error
                            ? 'border-rose-300 text-rose-900 placeholder-rose-300 focus:border-rose-500'
                            : 'border-slate-300 text-slate-900 placeholder-slate-400 focus:border-indigo-500'"
                    >
                </div>
                <p x-show="error"
                   x-text="error"
                   x-cloak
                   x-transition:enter="transition ease-out duration-150"
                   x-transition:enter-start="opacity-0 -translate-y-1"
                   x-transition:enter-end="opacity-100 translate-y-0"
                   class="mt-1.5 text-xs text-rose-600">
                </p>
            </div>

            <!-- Submit button -->
            <button
                type="submit"
                :disabled="submitting"
                class="relative w-full flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-200/50 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:ring-offset-2 transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed"
            >
                <!-- Loading spinner -->
                <svg x-show="submitting" x-cloak
                     class="animate-spin h-4 w-4 text-white"
                     fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 0 1 4 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                </svg>
                <span x-text="submitting ? 'Sending…' : 'Send reset link'"></span>
            </button>
        </form>

        <!-- Help text -->
        <div class="mt-6 rounded-xl bg-slate-50 border border-slate-100 px-4 py-3">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
                </svg>
                <p class="text-xs text-slate-500 leading-relaxed">
                    If your account was created via Google or Microsoft SSO,
                    use those providers to sign in instead.
                    <a href="<?= url('/login') ?>" class="font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">
                        Go to sign in &rarr;
                    </a>
                </p>
            </div>
        </div>

        <!-- Back to login link -->
        <div class="mt-8 text-center">
            <a href="<?= url('/login') ?>"
               class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-600 hover:text-indigo-600 transition-colors group">
                <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform"
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Back to sign in
            </a>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════
         STATE 2 — Success confirmation
         ══════════════════════════════════════════════════════ -->
    <div x-show="sent"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">

        <div class="text-center">
            <!-- Animated checkmark -->
            <div class="flex items-center justify-center mb-6">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                </div>
            </div>

            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-2">
                Check your email
            </h2>
            <p class="text-sm text-slate-500 leading-relaxed max-w-xs mx-auto mb-2">
                We&rsquo;ve sent a password reset link to:
            </p>
            <p class="text-sm font-semibold text-slate-800 mb-6" x-text="email"></p>

            <!-- Info box -->
            <div class="rounded-xl bg-sky-50 border border-sky-100 px-4 py-3 mb-6 text-left">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-sky-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
                    </svg>
                    <div class="text-xs text-sky-700 leading-relaxed">
                        <p class="font-semibold mb-1">Didn&rsquo;t receive the email?</p>
                        <ul class="space-y-1 list-disc list-inside">
                            <li>Check your spam or junk folder</li>
                            <li>Make sure you entered the correct email</li>
                            <li>The reset link expires in 60 minutes</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="space-y-3">
                <button
                    @click="reset()"
                    type="button"
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all duration-150"
                >
                    Try a different email
                </button>

                <a href="<?= url('/login') ?>"
                   class="block w-full text-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-200/50 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:ring-offset-2 transition-all duration-200">
                    Back to sign in
                </a>
            </div>

            <!-- Support fallback -->
            <p class="mt-6 text-xs text-slate-400">
                Still having trouble?
                <a href="mailto:<?= e(config('services.support_email')) ?>"
                   class="font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">
                    Contact support
                </a>
            </p>
        </div>
    </div>
</div>
