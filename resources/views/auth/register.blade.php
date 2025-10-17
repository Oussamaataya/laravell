<x-guest-layout>
    <!-- Header with Icon -->
    <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-[#FDFDFC] dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-2xl mb-4 shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)]">
            <svg class="w-7 h-7 text-[#f53003] dark:text-[#FF4433]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
        </div>
        <h2 class="text-3xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Create an account') }}</h2>
        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Join us and start your journey today') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name with Icon -->
        <div class="group">
            <x-input-label for="name" :value="__('Name')" class="text-[#1b1b18] dark:text-[#EDEDEC] text-sm font-medium mb-2" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-[#706f6c] dark:text-[#A1A09A] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <x-text-input id="name" 
                    class="block w-full pl-12 pr-4 py-3 bg-[#FDFDFC] dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] text-sm focus:outline-none focus:border-[#1b1b18] dark:focus:border-[#EDEDEC] transition-all duration-300" 
                    type="text" 
                    name="name" 
                    :value="old('name')" 
                    required 
                    autofocus 
                    autocomplete="name" 
                    placeholder="Votre nom complet" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm text-[#f53003] dark:text-[#FF4433]" />
        </div>

        <!-- Email Address with Icon -->
        <div class="group">
            <x-input-label for="email" :value="__('Email')" class="text-[#1b1b18] dark:text-[#EDEDEC] text-sm font-medium mb-2" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-[#706f6c] dark:text-[#A1A09A] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                </div>
                <x-text-input id="email" 
                    class="block w-full pl-12 pr-4 py-3 bg-[#FDFDFC] dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] text-sm focus:outline-none focus:border-[#1b1b18] dark:focus:border-[#EDEDEC] transition-all duration-300" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autocomplete="username" 
                    placeholder="vous@exemple.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-[#f53003] dark:text-[#FF4433]" />
        </div>

        <!-- Password with Icon -->
        <div class="group">
            <x-input-label for="password" :value="__('Password')" class="text-[#1b1b18] dark:text-[#EDEDEC] text-sm font-medium mb-2" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-[#706f6c] dark:text-[#A1A09A] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <x-text-input id="password" 
                    class="block w-full pl-12 pr-4 py-3 bg-[#FDFDFC] dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] text-sm focus:outline-none focus:border-[#1b1b18] dark:focus:border-[#EDEDEC] transition-all duration-300"
                    type="password"
                    name="password"
                    required 
                    autocomplete="new-password" 
                    placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-[#f53003] dark:text-[#FF4433]" />
        </div>

        <!-- Confirm Password with Icon -->
        <div class="group">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-[#1b1b18] dark:text-[#EDEDEC] text-sm font-medium mb-2" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-[#706f6c] dark:text-[#A1A09A] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <x-text-input id="password_confirmation" 
                    class="block w-full pl-12 pr-4 py-3 bg-[#FDFDFC] dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] text-sm focus:outline-none focus:border-[#1b1b18] dark:focus:border-[#EDEDEC] transition-all duration-300"
                    type="password"
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password" 
                    placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-[#f53003] dark:text-[#FF4433]" />
        </div>

        <!-- Submit Button -->
        <div class="pt-3">
            <button type="submit" 
                class="relative w-full group/btn overflow-hidden px-6 py-3.5 bg-[#1b1b18] dark:bg-[#eeeeec] hover:bg-black dark:hover:bg-white border border-[#19140035] dark:border-[#100f0d1f] rounded-lg text-white dark:text-[#1b1b18] text-sm font-semibold shadow-[0px_0px_0px_1px_rgba(255,255,255,0.06)_inset,0px_1px_0px_0px_rgba(255,255,255,0.1)_inset,0px_0px_0px_1px_rgba(26,26,26,0.04),0px_2px_4px_0px_rgba(0,0,0,0.3),0px_12px_13px_-6px_rgba(26,26,26,0.08)] dark:shadow-[0px_0px_0px_1px_rgba(0,0,0,0.03)_inset,0px_1px_0px_0px_rgba(0,0,0,0.02)_inset,0px_0px_0px_1px_rgba(255,255,255,0.03),0px_2px_2px_0px_rgba(255,255,255,0.1),0px_4px_4px_-1px_rgba(255,255,255,0.04)] transform hover:-translate-y-0.5 transition-all duration-300">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    {{ __('Create account') }}
                    <svg class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </span>
            </button>
        </div>

        <!-- Divider -->
        <div class="relative py-4">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-[#e3e3e0] dark:border-[#3E3E3A]"></div>
            </div>
            <div class="relative flex justify-center text-xs">
                <span class="px-4 bg-white dark:bg-[#161615] text-[#706f6c] dark:text-[#A1A09A]">{{ __('Already have an account?') }}</span>
            </div>
        </div>

        <!-- Login Link as Button -->
        <div>
            <a href="{{ route('login') }}" 
               class="block w-full px-6 py-3.5 bg-transparent border border-[#19140035] dark:border-[#100f0d1f] hover:bg-[#1b1b18]/5 dark:hover:bg-[#eeeeec]/5 rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] text-sm font-semibold text-center transition-all duration-300 transform hover:-translate-y-0.5">
                {{ __('Sign in instead') }}
            </a>
        </div>
    </form>
</x-guest-layout>
