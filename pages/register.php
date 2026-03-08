<?php
/**
 * Register Page — LearnAI
 *
 * Rendered inside layouts/auth.php (centred card with brand mark).
 */

$pageTitle = 'Create Account';
?>

<div x-data="{
    form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role: '',
        school: '',
        terms: false
    },
    errors: {},
    submitting: false,
    showPassword: false,
    showConfirm: false,

    /* ── password strength ─────────────────────────────── */
    get strength() {
        const p = this.form.password;
        if (!p) return { score: 0, label: '', color: '', width: '0%' };
        let s = 0;
        if (p.length >= 8)  s++;
        if (p.length >= 12) s++;
        if (/[A-Z]/.test(p)) s++;
        if (/[0-9]/.test(p)) s++;
        if (/[^A-Za-z0-9]/.test(p)) s++;

        const map = [
            { label: '',            color: '',            width: '0%'   },
            { label: 'Very weak',   color: 'bg-rose-500', width: '20%'  },
            { label: 'Weak',        color: 'bg-orange-500', width: '40%' },
            { label: 'Fair',        color: 'bg-amber-500', width: '60%' },
            { label: 'Strong',      color: 'bg-emerald-500', width: '80%' },
            { label: 'Very strong', color: 'bg-emerald-600', width: '100%' },
        ];
        return { score: s, ...map[s] };
    },

    /* ── validation ────────────────────────────────────── */
    validate() {
        this.errors = {};

        if (!this.form.name.trim()) {
            this.errors.name = 'Full name is required.';
        } else if (this.form.name.trim().length < 2) {
            this.errors.name = 'Name must be at least 2 characters.';
        }

        if (!this.form.email.trim()) {
            this.errors.email = 'Email address is required.';
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email)) {
            this.errors.email = 'Please enter a valid email address.';
        }

        if (!this.form.password) {
            this.errors.password = 'Password is required.';
        } else if (this.form.password.length < 8) {
            this.errors.password = 'Password must be at least 8 characters.';
        }

        if (!this.form.password_confirmation) {
            this.errors.password_confirmation = 'Please confirm your password.';
        } else if (this.form.password !== this.form.password_confirmation) {
            this.errors.password_confirmation = 'Passwords do not match.';
        }

        if (!this.form.role) {
            this.errors.role = 'Please select your role.';
        }

        if (!this.form.terms) {
            this.errors.terms = 'You must accept the terms and privacy policy.';
        }

        return Object.keys(this.errors).length === 0;
    },

    /* ── submit ────────────────────────────────────────── */
    submit() {
        if (!this.validate()) return;
        this.submitting = true;
        setTimeout(() => {
            window.location.href = '<?= url('/dashboard') ?>';
        }, 1000);
    }
}">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Create your account</h1>
        <p class="mt-2 text-sm text-slate-500">Start using <?= e(config('name')) ?> for free — no credit card needed</p>
    </div>

    <!-- Form -->
    <form @submit.prevent="submit" novalidate class="space-y-5">
        <?= csrf_field() ?>

        <!-- Full name -->
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Full name</label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                </div>
                <input
                    id="name"
                    type="text"
                    x-model="form.name"
                    autocomplete="name"
                    placeholder="Jane Smith"
                    class="block w-full rounded-xl border pl-11 pr-4 py-3 text-sm shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
                    :class="errors.name ? 'border-rose-300 text-rose-900 placeholder-rose-300 focus:border-rose-500' : 'border-slate-300 text-slate-900 placeholder-slate-400 focus:border-indigo-500'"
                >
            </div>
            <p x-show="errors.name" x-text="errors.name" x-cloak class="mt-1.5 text-xs text-rose-600"></p>
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email address</label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                </div>
                <input
                    id="email"
                    type="email"
                    x-model="form.email"
                    autocomplete="email"
                    placeholder="you@school.edu"
                    class="block w-full rounded-xl border pl-11 pr-4 py-3 text-sm shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
                    :class="errors.email ? 'border-rose-300 text-rose-900 placeholder-rose-300 focus:border-rose-500' : 'border-slate-300 text-slate-900 placeholder-slate-400 focus:border-indigo-500'"
                >
            </div>
            <p x-show="errors.email" x-text="errors.email" x-cloak class="mt-1.5 text-xs text-rose-600"></p>
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                </div>
                <input
                    id="password"
                    :type="showPassword ? 'text' : 'password'"
                    x-model="form.password"
                    autocomplete="new-password"
                    placeholder="Min. 8 characters"
                    class="block w-full rounded-xl border pl-11 pr-11 py-3 text-sm shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
                    :class="errors.password ? 'border-rose-300 text-rose-900 placeholder-rose-300 focus:border-rose-500' : 'border-slate-300 text-slate-900 placeholder-slate-400 focus:border-indigo-500'"
                >
                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 transition-colors">
                    <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                    <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                </button>
            </div>
            <p x-show="errors.password" x-text="errors.password" x-cloak class="mt-1.5 text-xs text-rose-600"></p>

            <!-- Password strength meter -->
            <div x-show="form.password.length > 0" x-cloak class="mt-2.5">
                <div class="h-1.5 w-full rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-300"
                         :class="strength.color"
                         :style="{ width: strength.width }">
                    </div>
                </div>
                <p class="mt-1 text-xs" :class="{
                    'text-rose-600': strength.score <= 1,
                    'text-orange-600': strength.score === 2,
                    'text-amber-600': strength.score === 3,
                    'text-emerald-600': strength.score >= 4,
                }">
                    <span x-text="strength.label"></span>
                </p>
            </div>
        </div>

        <!-- Confirm password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Confirm password</label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                </div>
                <input
                    id="password_confirmation"
                    :type="showConfirm ? 'text' : 'password'"
                    x-model="form.password_confirmation"
                    autocomplete="new-password"
                    placeholder="Re-enter your password"
                    class="block w-full rounded-xl border pl-11 pr-11 py-3 text-sm shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
                    :class="errors.password_confirmation ? 'border-rose-300 text-rose-900 placeholder-rose-300 focus:border-rose-500' : 'border-slate-300 text-slate-900 placeholder-slate-400 focus:border-indigo-500'"
                >
                <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 transition-colors">
                    <svg x-show="!showConfirm" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                    <svg x-show="showConfirm" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                </button>
            </div>
            <!-- Match indicator -->
            <p x-show="form.password_confirmation && form.password === form.password_confirmation" x-cloak class="mt-1.5 text-xs text-emerald-600 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                Passwords match
            </p>
            <p x-show="errors.password_confirmation" x-text="errors.password_confirmation" x-cloak class="mt-1.5 text-xs text-rose-600"></p>
        </div>

        <!-- Role -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">I am a&hellip;</label>
            <div class="grid grid-cols-3 gap-3">
                <template x-for="option in [
                    { value: 'student', label: 'Student', icon: '🎒' },
                    { value: 'teacher', label: 'Teacher', icon: '👩‍🏫' },
                    { value: 'admin',   label: 'Admin',   icon: '🏫' },
                ]" :key="option.value">
                    <label
                        class="relative flex flex-col items-center gap-1.5 rounded-xl border-2 px-3 py-4 cursor-pointer transition-all duration-150 text-center hover:shadow-md"
                        :class="form.role === option.value
                            ? 'border-indigo-600 bg-indigo-50 ring-1 ring-indigo-600'
                            : 'border-slate-200 bg-white hover:border-slate-300'"
                    >
                        <input type="radio" name="role" :value="option.value" x-model="form.role" class="sr-only">
                        <span class="text-2xl" x-text="option.icon"></span>
                        <span class="text-xs font-semibold" :class="form.role === option.value ? 'text-indigo-700' : 'text-slate-600'" x-text="option.label"></span>
                    </label>
                </template>
            </div>
            <p x-show="errors.role" x-text="errors.role" x-cloak class="mt-1.5 text-xs text-rose-600"></p>
        </div>

        <!-- School / Institution (optional) -->
        <div>
            <label for="school" class="block text-sm font-medium text-slate-700 mb-1.5">
                School / Institution <span class="text-slate-400 font-normal">(optional)</span>
            </label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z"/></svg>
                </div>
                <input
                    id="school"
                    type="text"
                    x-model="form.school"
                    autocomplete="organization"
                    placeholder="e.g. Lincoln High School"
                    class="block w-full rounded-xl border border-slate-300 pl-11 pr-4 py-3 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition-colors duration-150 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/40"
                >
            </div>
        </div>

        <!-- Terms -->
        <div>
            <div class="flex items-start gap-2.5">
                <input
                    id="terms"
                    type="checkbox"
                    x-model="form.terms"
                    class="mt-0.5 h-4 w-4 rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500/40 focus:ring-2 focus:ring-offset-0 transition"
                >
                <label for="terms" class="text-sm text-slate-600 leading-snug select-none">
                    I agree to the
                    <a href="#" class="font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">Terms of Service</a>
                    and
                    <a href="#" class="font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">Privacy Policy</a>
                </label>
            </div>
            <p x-show="errors.terms" x-text="errors.terms" x-cloak class="mt-1.5 text-xs text-rose-600 ml-6"></p>
        </div>

        <!-- Submit -->
        <button
            type="submit"
            :disabled="submitting"
            class="relative w-full flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-200/50 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:ring-offset-2 transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed"
        >
            <svg x-show="submitting" x-cloak class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 0 1 4 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
            <span x-text="submitting ? 'Creating account…' : 'Create account'"></span>
        </button>
    </form>

    <!-- Divider -->
    <div class="relative my-8">
        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
        <div class="relative flex justify-center text-xs"><span class="bg-white px-4 text-slate-400 font-medium">Or sign up with</span></div>
    </div>

    <!-- Social sign-up -->
    <div class="grid grid-cols-2 gap-3">
        <button type="button" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 hover:border-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all duration-150">
            <svg class="h-5 w-5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
            Google
        </button>
        <button type="button" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 hover:border-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all duration-150">
            <svg class="h-5 w-5" viewBox="0 0 23 23"><path fill="#f35325" d="M1 1h10v10H1z"/><path fill="#81bc06" d="M12 1h10v10H12z"/><path fill="#05a6f0" d="M1 12h10v10H1z"/><path fill="#ffba08" d="M12 12h10v10H12z"/></svg>
            Microsoft
        </button>
    </div>

    <!-- Login link -->
    <p class="mt-8 text-center text-sm text-slate-500">
        Already have an account?
        <a href="<?= url('/login') ?>" class="font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">Sign in &rarr;</a>
    </p>
</div>
