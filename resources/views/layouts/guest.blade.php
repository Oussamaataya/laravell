<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            @keyframes float {
                0%, 100% { transform: translateY(0) rotate(0deg); }
                50% { transform: translateY(-20px) rotate(5deg); }
            }
            @keyframes pulse-glow {
                0%, 100% { opacity: 0.3; transform: scale(1); }
                50% { opacity: 0.6; transform: scale(1.1); }
            }
            @keyframes gradient-shift {
                0%, 100% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
            }
            .animate-float { animation: float 6s ease-in-out infinite; }
            .animate-float-delayed { animation: float 8s ease-in-out infinite 1s; }
            .animate-pulse-glow { animation: pulse-glow 4s ease-in-out infinite; }
            .gradient-animate {
                background: linear-gradient(-45deg, #F53003, #FF4433, #F8B803, #F0ACB8);
                background-size: 400% 400%;
                animation: gradient-shift 15s ease infinite;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] overflow-x-hidden">
        <!-- Animated Background Elements -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
            <!-- Gradient Orbs - Couleurs cohérentes avec la page d'accueil -->
            <div class="absolute top-10 right-10 w-72 h-72 bg-[#f53003] dark:bg-[#FF4433] rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-3xl opacity-15 dark:opacity-10 animate-pulse-glow"></div>
            <div class="absolute bottom-10 left-10 w-96 h-96 bg-[#dbdbd7] dark:bg-[#3E3E3A] rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-3xl opacity-15 dark:opacity-10 animate-pulse-glow" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-[#f53003] dark:bg-[#FF4433] rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-3xl opacity-10 dark:opacity-8 animate-pulse-glow" style="animation-delay: 4s;"></div>
            
            <!-- Floating Shapes -->
            <div class="absolute top-20 left-1/4 w-16 h-16 border-2 border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg opacity-30 animate-float"></div>
            <div class="absolute bottom-32 right-1/4 w-12 h-12 border-2 border-[#f53003] dark:border-[#FF4433] rounded-full opacity-20 animate-float-delayed"></div>
            <div class="absolute top-1/3 right-1/3 w-20 h-20 border-2 border-[#e3e3e0] dark:border-[#3E3E3A] rotate-45 opacity-30 animate-float" style="animation-delay: 3s;"></div>
        </div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 p-6 relative z-10">
            <!-- Card with Glassmorphism Effect -->
            <div class="w-full sm:max-w-md relative">
                <!-- Card Background avec effet subtil -->
                <div class="absolute inset-0 bg-[#f53003] dark:bg-[#FF4433] rounded-2xl opacity-0 group-hover:opacity-5 blur-xl transition-opacity duration-500"></div>
                
                <div class="relative px-8 py-10 bg-white dark:bg-[#161615] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] hover:border-[#19140035] dark:hover:border-[#62605b] transition-all duration-300">
                    {{ $slot }}
                </div>
            </div>

            <!-- Decorative Bottom Text -->
            <p class="mt-8 text-xs text-[#706f6c] dark:text-[#A1A09A] text-center">
                Secured by modern encryption technology
            </p>
        </div>
    </body>
</html>
