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
<body class="bg-gray-100">

    <!-- Hero Section -->
    <section class="bg-blue-600 text-white py-20">
        <div class="container mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Empower Your Selling Experience</h1>
            <p class="text-lg mb-8">A powerful SaaS application designed for sellers and admins to streamline operations.</p>
            <a href="#cta" class="bg-white text-blue-600 px-6 py-3 rounded-full font-semibold">Get Started</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl font-bold mb-10">Features</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-xl font-semibold mb-2">Feature One</h3>
                    <p>Detail about feature one that highlights its benefits.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-xl font-semibold mb-2">Feature Two</h3>
                    <p>Detail about feature two that highlights its benefits.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-xl font-semibold mb-2">Feature Three</h3>
                    <p>Detail about feature three that highlights its benefits.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Tech Stack Section -->
    <section class="bg-gray-200 py-20">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl font-bold mb-10">Tech Stack</h2>
            <p class="mb-8">Built with the latest technologies to ensure performance and scalability.</p>
            <ul class="flex justify-center space-x-4">
                <li class="bg-white p-4 rounded-full shadow-lg">Laravel</li>
                <li class="bg-white p-4 rounded-full shadow-lg">Tailwind CSS</li>
                <li class="bg-white p-4 rounded-full shadow-lg">Vue.js</li>
                <li class="bg-white p-4 rounded-full shadow-lg">MySQL</li>
            </ul>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section id="cta" class="py-20">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl font-bold mb-4">Ready to Get Started?</h2>
            <p class="mb-8">Join us today and take your selling experience to the next level.</p>
            <a href="#" class="bg-blue-600 text-white px-6 py-3 rounded-full font-semibold">Sign Up Now</a>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="bg-gray-800 text-white py-10">
        <div class="container mx-auto text-center">
            <p>&copy; {{ date('Y') }} Your SaaS Application. All rights reserved.</p>
            <p>Follow us on <a href="#" class="underline">Twitter</a>, <a href="#" class="underline">Facebook</a>, <a href="#" class="underline">LinkedIn</a>.</p>
        </div>
    </footer>

</body>
</html>