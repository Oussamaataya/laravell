<x-guest-layout>
    <!-- Header with Icon -->
    <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-[#FDFDFC] dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-2xl mb-4 shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)]">
            <svg class="w-7 h-7 text-[#f53003] dark:text-[#FF4433]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
        </div>
        <h2 class="text-3xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ __('Welcome back') }}</h2>
        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Sign in to your account to continue') }}</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address with Icon -->
        <div class="group">
            <x-input-label for="email" :value="__('Email')" class="text-[#1b1b18] dark:text-[#EDEDEC] text-sm font-medium mb-2 flex items-center gap-2" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-[#706f6c] dark:text-[#A1A09A] group-focus-within:text-[#f53003] dark:group-focus-within:text-[#FF4433] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                </div>
                <x-text-input id="email" 
                    class="block w-full pl-12 pr-4 py-3 bg-[#FDFDFC] dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] text-sm focus:outline-none focus:border-[#f53003] dark:focus:border-[#FF4433] transition-all duration-300 hover:border-[#19140035] dark:hover:border-[#62605b]" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autofocus 
                    autocomplete="username" 
                    placeholder="vous@exemple.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-[#f53003] dark:text-[#FF4433]" />
        </div>

        <!-- Password with Icon and Toggle -->
        <div class="group">
            <x-input-label for="password" :value="__('Password')" class="text-[#1b1b18] dark:text-[#EDEDEC] text-sm font-medium mb-2 flex items-center gap-2" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-[#706f6c] dark:text-[#A1A09A] group-focus-within:text-[#f53003] dark:group-focus-within:text-[#FF4433] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <x-text-input id="password" 
                    class="block w-full pl-12 pr-4 py-3 bg-[#FDFDFC] dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] text-sm focus:outline-none focus:border-[#f53003] dark:focus:border-[#FF4433] transition-all duration-300 hover:border-[#19140035] dark:hover:border-[#62605b]"
                    type="password"
                    name="password"
                    required 
                    autocomplete="current-password" 
                    placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-[#f53003] dark:text-[#FF4433]" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group/check">
                <input id="remember_me" type="checkbox" 
                    class="rounded-lg border-2 border-[#e3e3e0] dark:border-[#3E3E3A] text-[#f53003] dark:text-[#FF4433] focus:ring-[#f53003] dark:focus:ring-[#FF4433] focus:ring-offset-0 transition-all duration-200 cursor-pointer" 
                    name="remember">
                <span class="ms-2 text-sm text-[#706f6c] dark:text-[#A1A09A] group-hover/check:text-[#1b1b18] dark:group-hover/check:text-[#EDEDEC] transition-colors">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-[#f53003] dark:text-[#FF4433] hover:underline focus:outline-none font-medium transition-all" 
                   href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <!-- Submit Button - Style cohérent avec la page d'accueil -->
        <div class="pt-3">
            <button type="submit" 
                class="w-full px-6 py-3 bg-[#1b1b18] dark:bg-[#eeeeec] dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white border border-black dark:border-[#eeeeec] rounded-lg text-white text-sm font-semibold transition-all duration-300 hover:shadow-lg flex items-center justify-center gap-2">
                <span>{{ __('Log in') }}</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </button>
        </div>

        <!-- Divider -->
        <div class="relative py-4">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-[#e3e3e0] dark:border-[#3E3E3A]"></div>
            </div>
            <div class="relative flex justify-center text-xs">
                <span class="px-4 bg-white dark:bg-[#161615] text-[#706f6c] dark:text-[#A1A09A]">{{ __("Don't have an account?") }}</span>
            </div>
        </div>

        <!-- Register Link as Button -->
        <div>
            <a href="{{ route('register') }}" 
               class="block w-full px-6 py-3 bg-transparent border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] text-sm font-semibold text-center transition-all duration-300">
                {{ __('Create new account') }}
            </a>
        </div>
    </form>
</x-guest-layout>
