<x-guest-layout>
    <!-- Header with Icon -->
    <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-[#FDFDFC] dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-2xl mb-4 shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)]">
            <svg class="w-7 h-7 text-[#f53003] dark:text-[#FF4433]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
        </div>
        <h2 class="text-3xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Forgot password?') }}</h2>
        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] max-w-sm mx-auto">
            {{ __('No problem. Just let us know your email address and we will email you a password reset link.') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email Address with Icon -->
        <div class="group">
            <x-input-label for="email" :value="__('Email')" class="text-[#1b1b18] dark:text-[#EDEDEC] text-sm font-medium mb-2" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-[#706f6c] dark:text-[#A1A09A] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <x-text-input id="email" 
                    class="block w-full pl-12 pr-4 py-3 bg-[#FDFDFC] dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] text-sm focus:outline-none focus:border-[#1b1b18] dark:focus:border-[#EDEDEC] transition-all duration-300" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autofocus 
                    placeholder="vous@exemple.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-[#f53003] dark:text-[#FF4433]" />
        </div>

        <!-- Submit Button -->
        <div class="pt-3">
            <button type="submit" 
                class="relative w-full group/btn overflow-hidden px-6 py-3.5 bg-[#1b1b18] dark:bg-[#eeeeec] hover:bg-black dark:hover:bg-white border border-[#19140035] dark:border-[#100f0d1f] rounded-lg text-white dark:text-[#1b1b18] text-sm font-semibold shadow-[0px_0px_0px_1px_rgba(255,255,255,0.06)_inset,0px_1px_0px_0px_rgba(255,255,255,0.1)_inset,0px_0px_0px_1px_rgba(26,26,26,0.04),0px_2px_4px_0px_rgba(0,0,0,0.3),0px_12px_13px_-6px_rgba(26,26,26,0.08)] dark:shadow-[0px_0px_0px_1px_rgba(0,0,0,0.03)_inset,0px_1px_0px_0px_rgba(0,0,0,0.02)_inset,0px_0px_0px_1px_rgba(255,255,255,0.03),0px_2px_2px_0px_rgba(255,255,255,0.1),0px_4px_4px_-1px_rgba(255,255,255,0.04)] transform hover:-translate-y-0.5 transition-all duration-300">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"/>
                    </svg>
                    {{ __('Send reset link') }}
                </span>
            </button>
        </div>

        <!-- Back to Login Link -->
        <div class="text-center pt-2">
            <a href="{{ route('login') }}" 
               class="inline-flex items-center gap-2 text-sm text-[#706f6c] dark:text-[#A1A09A] hover:text-[#F53003] dark:hover:text-[#FF4433] font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('Back to login') }}
            </a>
        </div>
    </form>
</x-guest-layout>
