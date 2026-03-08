<?php
/**
 * Login Page — LearnAI
 *
 * Rendered inside layouts/auth.php (centred card with brand mark).
 * Features client-side validation via Alpine.js, social login
 * buttons, password visibility toggle, and polished UX.
 */

$pageTitle = 'Sign In';
?>

<div x-data="{
    form: {
        email: '',
        password: '',
        remember: false
    },
    errors: {},
    submitting: false,
    showPassword: false,
    serverError: '',

    validate() {
        this.errors = {};
        this.serverError = '';

        /* email */
        const email = this.form.email.trim();
        if (!email) {
            this.errors.email = 'Email address is required.';
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            this.errors.email = 'Please enter a valid email address.';
        }

        /* password */
        if (!this.form.password) {
            this.errors.password = 'Password is required.';
        } else if (this.form.password.length < 8) {
            this.errors.password = 'Password must be at least 8 characters.';
        }

        return Object.keys(this.errors).length === 0;
    },

    submit() {
        if (!this.validate()) return;
        this.submitting = true;
        this.serverError = '';

        // Simulate API call — replace with real fetch() in production
        setTimeout(() => {
            window.location.href = '<?= url('/dashboard') ?>';
        }, 800);
    },

    clearError(field) {
        delete this.errors[field];
    }
}">
    <!-- ── Header ────────────────────────────────────────── -->
    <div class="text-center mb-8">
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
            Welcome back
        </h1>
        <p class="mt-2 text-sm text-slate-500">
            Sign in to your <?= e(config('name')) ?> account
        </p>
    </div>

    <!-- ── Server error banner ───────────────────────────── -->
    <div
        x-show="serverError"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="mb-6 rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 flex items-start gap-3"
    >
        <svg class="w-5 h-5 text-rose-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd"/>
        </svg>
        <div>
            <p class="text-sm font-medium text-rose-800" x-text="serverError"></p>
        </div>
    </div>

    <!-- ── Login Form ────────────────────────────────────── -->
    <form @submit.prevent="submit" novalidate class="space-y-5">
        <?= csrf_field() ?>

        <!-- Email field -->
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
                    x-model="form.email"
                    @input="clearError('email')"
                    autocomplete="email"
                    placeholder="you@school.edu"
                    class="block w-full rounded-xl border pl-11 pr-4 py-3 text-sm shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
                    :class="errors.email
                        ? 'border-rose-300 text-rose-900 placeholder-rose-300 focus:border-rose-500'
                        : 'border-slate-300 text-slate-900 placeholder-slate-400 focus:border-indigo-500'"
                >
            </div>
            <p x-show="errors.email"
               x-text="errors.email"
               x-cloak
               x-transition:enter="transition ease-out duration-150"
               x-transition:enter-start="opacity-0 -translate-y-1"
               x-transition:enter-end="opacity-100 translate-y-0"
               class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
            </p>
        </div>

        <!-- Password field -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-medium text-slate-700">
                    Password
                </label>
                <a href="<?= url('/forgot') ?>"
                   class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">
                    Forgot password?
                </a>
            </div>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
                    </svg>
                </div>
                <input
                    id="password"
                    :type="showPassword ? 'text' : 'password'"
                    x-model="form.password"
                    @input="clearError('password')"
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="block w-full rounded-xl border pl-11 pr-11 py-3 text-sm shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
                    :class="errors.password
                        ? 'border-rose-300 text-rose-900 placeholder-rose-300 focus:border-rose-500'
                        : 'border-slate-300 text-slate-900 placeholder-slate-400 focus:border-indigo-500'"
                >
                <!-- Toggle password visibility -->
                <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 transition-colors"
                    :aria-label="showPassword ? 'Hide password' : 'Show password'"
                >
                    <svg x-show="!showPassword"
                         class="h-5 w-5"
                         fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                    </svg>
                    <svg x-show="showPassword" x-cloak
                         class="h-5 w-5"
                         fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
                    </svg>
                </button>
            </div>
            <p x-show="errors.password"
               x-text="errors.password"
               x-cloak
               x-transition:enter="transition ease-out duration-150"
               x-transition:enter-start="opacity-0 -translate-y-1"
               x-transition:enter-end="opacity-100 translate-y-0"
               class="mt-1.5 text-xs text-rose-600">
            </p>
        </div>

        <!-- Remember me checkbox -->
        <div class="flex items-center gap-2">
            <input
                id="remember"
                type="checkbox"
                x-model="form.remember"
                class="h-4 w-4 rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500/40 focus:ring-2 focus:ring-offset-0 transition"
            >
            <label for="remember" class="text-sm text-slate-600 select-none">
                Remember me for 30 days
            </label>
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
            <span x-text="submitting ? 'Signing in…' : 'Sign in'"></span>
        </button>
    </form>

    <!-- ── Divider ───────────────────────────────────────── -->
    <div class="relative my-8">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-slate-200"></div>
        </div>
        <div class="relative flex justify-center text-xs">
            <span class="bg-white px-4 text-slate-400 font-medium">Or continue with</span>
        </div>
    </div>

    <!-- ── Social login buttons ──────────────────────────── -->
    <div class="grid grid-cols-2 gap-3">
        <!-- Google -->
        <button
            type="button"
            class="inline-flex items-center justify-center gap-2.5 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 hover:border-slate-400 hover:shadow focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all duration-150"
        >
            <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Google
        </button>

        <!-- Microsoft -->
        <button
            type="button"
            class="inline-flex items-center justify-center gap-2.5 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 hover:border-slate-400 hover:shadow focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all duration-150"
        >
            <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 23 23">
                <path fill="#f35325" d="M1 1h10v10H1z"/>
                <path fill="#81bc06" d="M12 1h10v10H12z"/>
                <path fill="#05a6f0" d="M1 12h10v10H1z"/>
                <path fill="#ffba08" d="M12 12h10v10H12z"/>
            </svg>
            Microsoft
        </button>
    </div>

    <!-- ── Security notice ───────────────────────────────── -->
    <div class="mt-8 rounded-xl bg-slate-50 border border-slate-100 px-4 py-3">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/>
            </svg>
            <p class="text-xs text-slate-500 leading-relaxed">
                <span class="font-semibold text-slate-600">Secure connection.</span>
                Your credentials are encrypted with TLS 1.3.
                <?= e(config('name')) ?> is FERPA &amp; COPPA compliant and
                never shares your data with third parties.
            </p>
        </div>
    </div>

    <!-- ── Register link ─────────────────────────────────── -->
    <p class="mt-6 text-center text-sm text-slate-500">
        Don&rsquo;t have an account?
        <a href="<?= url('/register') ?>"
           class="font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">
            Create one free &rarr;
        </a>
    </p>
</div>
