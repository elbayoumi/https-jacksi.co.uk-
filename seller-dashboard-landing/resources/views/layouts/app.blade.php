<!-- resources/views/landing.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="High-end SaaS application for sellers and admins.">
    <title>Your SaaS Application</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <!-- Hero Section -->
    <section class="bg-blue-600 text-white py-20">
        <div class="container mx-auto text-center">
            <h1 class="text-5xl font-bold mb-4">Empower Your Selling Experience</h1>
            <p class="text-xl mb-8">A powerful SaaS platform designed for sellers and admins.</p>
            <a href="#features" class="bg-white text-blue-600 px-6 py-3 rounded-full font-semibold">Get Started</a>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20">
        <div class="container mx-auto text-center">
            <h2 class="text-4xl font-bold mb-12">Features</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-2xl font-semibold mb-4">Feature One</h3>
                    <p class="text-gray-700">Description of feature one that highlights its benefits.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-2xl font-semibold mb-4">Feature Two</h3>
                    <p class="text-gray-700">Description of feature two that highlights its benefits.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-2xl font-semibold mb-4">Feature Three</h3>
                    <p class="text-gray-700">Description of feature three that highlights its benefits.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Tech Stack Section -->
    <section class="bg-gray-200 py-20">
        <div class="container mx-auto text-center">
            <h2 class="text-4xl font-bold mb-12">Our Tech Stack</h2>
            <p class="text-xl mb-8">Built with the latest technologies to ensure performance and scalability.</p>
            <div class="flex justify-center space-x-4">
                <span class="bg-white px-4 py-2 rounded-full shadow">Laravel</span>
                <span class="bg-white px-4 py-2 rounded-full shadow">Tailwind CSS</span>
                <span class="bg-white px-4 py-2 rounded-full shadow">Vue.js</span>
                <span class="bg-white px-4 py-2 rounded-full shadow">MySQL</span>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="py-20">
        <div class="container mx-auto text-center">
            <h2 class="text-4xl font-bold mb-4">Ready to Get Started?</h2>
            <p class="text-xl mb-8">Join us today and take your selling experience to the next level.</p>
            <a href="#" class="bg-blue-600 text-white px-6 py-3 rounded-full font-semibold">Sign Up Now</a>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="bg-gray-800 text-white py-6">
        <div class="container mx-auto text-center">
            <p>&copy; {{ date('Y') }} Your SaaS Application. All rights reserved.</p>
            <p>Follow us on <a href="#" class="text-blue-400">Twitter</a>, <a href="#" class="text-blue-400">Facebook</a>, <a href="#" class="text-blue-400">Instagram</a>.</p>
        </div>
    </footer>
</body>
</html>