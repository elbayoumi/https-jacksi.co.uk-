{{-- resources/views/landing.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="High-end SaaS application for sellers and admins.">
    <title>Your SaaS Application</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800">
    <!-- Hero Section -->
    <section class="bg-blue-600 text-white py-20">
        <div class="container mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-bold">Empower Your Selling Experience</h1>
            <p class="mt-4 text-lg">A powerful SaaS application designed for sellers and admins.</p>
            <a href="#cta" class="mt-8 inline-block bg-white text-blue-600 font-semibold py-3 px-6 rounded-lg shadow-lg hover:bg-gray-200 transition">Get Started</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl font-bold">Features</h2>
            <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-10">
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-xl font-semibold">Feature One</h3>
                    <p class="mt-2">Description of feature one that highlights its benefits.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-xl font-semibold">Feature Two</h3>
                    <p class="mt-2">Description of feature two that highlights its benefits.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-xl font-semibold">Feature Three</h3>
                    <p class="mt-2">Description of feature three that highlights its benefits.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Tech Stack Section -->
    <section class="bg-gray-200 py-20">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl font-bold">Tech Stack</h2>
            <p class="mt-4">Built with the latest technologies to ensure performance and scalability.</p>
            <div class="mt-10 flex justify-center space-x-4">
                <span class="bg-white p-2 rounded-full shadow">Laravel</span>
                <span class="bg-white p-2 rounded-full shadow">Tailwind CSS</span>
                <span class="bg-white p-2 rounded-full shadow">Vue.js</span>
                <span class="bg-white p-2 rounded-full shadow">MySQL</span>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section id="cta" class="py-20 text-center">
        <h2 class="text-3xl font-bold">Ready to Get Started?</h2>
        <p class="mt-4">Join us today and take your selling experience to the next level.</p>
        <a href="#" class="mt-8 inline-block bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:bg-blue-700 transition">Sign Up Now</a>
    </section>

    <!-- Footer Section -->
    <footer class="bg-gray-800 text-white py-10">
        <div class="container mx-auto text-center">
            <p>&copy; {{ date('Y') }} Your SaaS Application. All rights reserved.</p>
            <div class="mt-4">
                <a href="#" class="text-gray-400 hover:text-white">Privacy Policy</a>
                <span class="mx-2">|</span>
                <a href="#" class="text-gray-400 hover:text-white">Terms of Service</a>
            </div>
        </div>
    </footer>
</body>
</html>