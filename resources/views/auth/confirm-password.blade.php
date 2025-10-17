<x-guest-layout>
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Confirm password') }}</h2>
        <p class="mt-1 text-sm text-[#706f6c] dark:text-[#A1A09A]">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-[#1b1b18] dark:text-[#EDEDEC] text-sm font-medium" />
            <x-text-input id="password" 
                class="block mt-2 w-full px-4 py-2.5 bg-[#FDFDFC] dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm text-[#1b1b18] dark:text-[#EDEDEC] text-sm focus:outline-none focus:border-[#f53003] dark:focus:border-[#FF4433] transition-colors"
                type="password"
                name="password"
                required 
                autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-[#f53003] dark:text-[#FF4433]" />
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" 
                class="w-full px-5 py-2.5 bg-[#1b1b18] dark:bg-[#eeeeec] dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white border border-black dark:border-[#eeeeec] rounded-sm text-white text-sm font-medium leading-normal transition-colors">
                {{ __('Confirm') }}
            </button>
        </div>
    </form>
</x-guest-layout>
