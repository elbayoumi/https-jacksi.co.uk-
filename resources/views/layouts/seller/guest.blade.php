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

<body class="font-sans text-gray-900 antialiased bg-gradient-to-br from-gray-100 to-white">

    <!-- Page Wrapper for Seller Auth Pages -->
    <div class="min-h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">

        <!-- Seller Logo -->
        <div class="mb-6">
            <a href="/" title="Back to Home">
                <x-application-logo class="w-20 h-20 fill-current text-indigo-600" />
            </a>
        </div>

        <!-- Auth Card -->
        <div class="w-full max-w-md bg-white border border-gray-100 shadow-xl rounded-xl px-8 py-6">

            <!-- Dynamic content will be injected here -->
            {{ $slot }}

        </div>
    </div>

</body>
</html>
