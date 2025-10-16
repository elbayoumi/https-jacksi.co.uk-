<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Seller Portal') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-[#f5f7fa] to-[#e2e8f0] font-sans text-gray-900 antialiased">

    <!-- Page Container -->
    <div class="min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 relative">

        <!-- Decorative Circle Top Left -->
        <div class="absolute top-0 left-0 w-48 h-48 bg-[#FFA500]/20 rounded-full blur-2xl opacity-30 -z-10"></div>
        <!-- Decorative Circle Bottom Right -->
        <div class="absolute bottom-0 right-0 w-64 h-64 bg-[#1F3B73]/10 rounded-full blur-3xl opacity-25 -z-10"></div>

        <!-- Logo -->
        <div class="mb-8">
            <a href="/" title="Back to Home">
                <x-application-logo class="w-20 h-20 text-[#1F3B73]" />
            </a>
            <h1 class="mt-2 text-xl font-semibold text-[#1F3B73] tracking-tight">Welcome to Seller Portal</h1>
        </div>

        <!-- Card -->
        <div class="w-full max-w-md bg-white border border-gray-200 shadow-3xl rounded-2xl px-10 py-8 transition-all duration-300">

            <!-- Slot Content -->
            {{ $slot }}

        </div>
    </div>

</body>
</html>
