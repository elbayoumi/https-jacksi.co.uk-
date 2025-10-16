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
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    {{-- Hero Section --}}
    <header class="bg-blue-600 text-white">
        <div class="container mx-auto px-6 py-12">
            <h1 class="text-4xl md:text-5xl font-bold">Empower Your Sales with Our SaaS Solution</h1>
            <p class="mt-4 text-lg">Streamline your selling process and manage your business effortlessly.</p>
            <a href="#cta" class="mt-6 inline-block bg-white text-blue-600 font-semibold py-2 px-4 rounded-lg shadow-lg hover:bg-gray-200 transition">Get Started</a>
        </div>
    </header>

    {{-- Features Section --}}
    <section class="py-12">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center">Features</h2>
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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

    {{-- Tech Stack Section --}}
    <section class="bg-gray-200 py-12">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center">Tech Stack</h2>
            <p class="mt-4 text-center">Built with modern technologies to ensure performance and scalability.</p>
            <div class="mt-8 flex justify-center space-x-4">
                <span class="bg-white p-2 rounded-full shadow">Laravel</span>
                <span class="bg-white p-2 rounded-full shadow">Tailwind CSS</span>
                <span class="bg-white p-2 rounded-full shadow">Vue.js</span>
                <span class="bg-white p-2 rounded-full shadow">MySQL</span>
            </div>
        </div>
    </section>

    {{-- Call to Action Section --}}
    <section id="cta" class="py-12">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold">Ready to Get Started?</h2>
            <p class="mt-4">Join us today and take your sales to the next level.</p>
            <a href="#" class="mt-6 inline-block bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-lg hover:bg-blue-700 transition">Sign Up Now</a>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-gray-800 text-white py-6">
        <div class="container mx-auto px-6 text-center">
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