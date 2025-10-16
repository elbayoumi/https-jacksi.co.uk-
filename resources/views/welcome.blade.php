<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Seller Portal – Invoicing & Client Management SaaS</title>

    {{-- SEO Meta Tags --}}
    <meta name="description" content="A powerful multi-user invoicing system for Sellers and Admins. Export PDFs, manage clients, track invoices, and more." />
    <meta name="keywords" content="Laravel, Invoicing, Seller Dashboard, Admin Panel, PDF Export, SaaS, Laravel Breeze" />
    <meta name="author" content="Your Name" />

    {{-- Open Graph (Facebook & social sharing) --}}
    <meta property="og:title" content="Seller Portal – Advanced Invoicing System">
    <meta property="og:description" content="Modern multi-user SaaS solution for managing clients and invoices with PDF export and admin insights.">
    <meta property="og:image" content="{{ asset('images/preview.png') }}">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:type" content="website">

    {{-- Styles --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body class="bg-gray-50 text-gray-800 font-sans leading-relaxed">

    {{-- Hero Section --}}
    <section class="bg-gradient-to-br from-indigo-600 to-indigo-800 text-white py-24 text-center px-6">
        <h1 class="text-4xl sm:text-5xl font-extrabold mb-4">Seller Portal</h1>
        <p class="text-lg sm:text-xl max-w-2xl mx-auto mb-6">
            The ultimate multi-user invoicing platform built with Laravel. Empower your sellers. Control the flow. Export like a pro.
        </p>
        <a href="{{ route('login') }}" class="inline-block px-6 py-3 bg-white text-indigo-700 font-semibold rounded-lg shadow-md hover:bg-gray-100 transition">
            Launch Dashboard
        </a>
    </section>

    {{-- Features --}}
    <section class="py-20 bg-white px-6">
        <div class="max-w-6xl mx-auto grid sm:grid-cols-2 lg:grid-cols-3 gap-12">
            <div>
                <h3 class="text-xl font-bold mb-2">👤 Role-based Access</h3>
                <p>Separate dashboards and controls for Admin and Sellers, with robust middleware enforcement.</p>
            </div>
            <div>
                <h3 class="text-xl font-bold mb-2">📦 Invoice Management</h3>
                <p>Create, calculate, and manage invoices with multiple items. Auto-totaling. Clean UI.</p>
            </div>
            <div>
                <h3 class="text-xl font-bold mb-2">📤 PDF Export</h3>
                <p>Generate printable and shareable PDF invoices using DomPDF – professional-grade output.</p>
            </div>
            <div>
                <h3 class="text-xl font-bold mb-2">🔐 Auth via Laravel Breeze</h3>
                <p>Secure login & registration flows with email verification and role protection.</p>
            </div>
            <div>
                <h3 class="text-xl font-bold mb-2">📊 Admin Insights</h3>
                <p>Track top sellers, total revenue, and invoice stats at a glance in a clean admin dashboard.</p>
            </div>
            <div>
                <h3 class="text-xl font-bold mb-2">🛠️ RESTful API</h3>
                <p>Full-featured REST API for clients, invoices, items — secure with Sanctum.</p>
            </div>
        </div>
    </section>

    {{-- Technology Stack --}}
    <section class="py-20 bg-gray-100 px-6 text-center">
        <h2 class="text-3xl font-bold mb-4">🧱 Built With</h2>
        <p class="text-lg text-gray-600 mb-8">Modern tools, robust structure, scalable backend.</p>
        <div class="flex flex-wrap justify-center gap-6 text-sm text-gray-700">
            <span class="px-4 py-2 bg-white rounded shadow">Laravel 10</span>
            <span class="px-4 py-2 bg-white rounded shadow">MySQL</span>
            <span class="px-4 py-2 bg-white rounded shadow">Laravel Breeze</span>
            <span class="px-4 py-2 bg-white rounded shadow">Blade</span>
            <span class="px-4 py-2 bg-white rounded shadow">DomPDF</span>
            <span class="px-4 py-2 bg-white rounded shadow">Sanctum</span>
            <span class="px-4 py-2 bg-white rounded shadow">Tailwind CSS</span>
        </div>
    </section>

    {{-- Call to Action --}}
    <section class="bg-indigo-700 text-white py-16 text-center">
        <h2 class="text-3xl font-bold mb-4">Ready to Experience the Power?</h2>
        <p class="mb-6">Login now and explore the seller dashboard in action.</p>
        <a href="{{ route('login') }}" class="inline-block px-6 py-3 bg-white text-indigo-700 font-semibold rounded-lg hover:bg-gray-100 transition">
            Log In
        </a>
    </section>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-gray-400 text-center py-6 text-sm">
        &copy; {{ date('Y') }} Seller Portal. Built for performance. Powered by Laravel.
    </footer>

</body>
</html>
