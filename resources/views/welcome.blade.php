<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeyondTrails - Discover & Explore Kenya's Premier Outdoor Adventures</title>
    <meta name="description" content="BeyondTrails is Kenya's premier platform for discovering hidden gems, curated hiking trails, wildlife safaris, and verified local tour operators.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        amber: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                        },
                        emerald: {
                            500: '#10b981',
                            600: '#059669',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0c0e12;
            color: #f3f4f6;
        }
        .font-heading {
            font-family: 'Outfit', sans-serif;
        }
        .hero-glow {
            background: radial-gradient(circle at 50% 20%, rgba(245, 158, 11, 0.15) 0%, rgba(16, 185, 129, 0.05) 40%, rgba(12, 14, 18, 0) 70%);
        }
        .glass-card {
            background: rgba(22, 27, 34, 0.75);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glass-nav {
            background: rgba(12, 14, 18, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .gradient-text {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #34d399 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col justify-between hero-glow selection:bg-amber-500 selection:text-black">

    <!-- Header Navigation -->
    <header class="fixed top-0 left-0 right-0 z-50 glass-nav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="/" class="flex items-center space-x-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-emerald-500 flex items-center justify-center shadow-lg shadow-amber-500/20 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6 text-black font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-6z" />
                    </svg>
                </div>
                <span class="font-heading font-extrabold text-2xl tracking-tight text-white">Beyond<span class="text-amber-500">Trails</span></span>
            </a>

            <nav class="hidden md:flex items-center space-x-8">
                <a href="#destinations" class="text-gray-300 hover:text-amber-400 font-medium transition-colors">Destinations</a>
                <a href="#features" class="text-gray-300 hover:text-amber-400 font-medium transition-colors">Features</a>
                <a href="#operators" class="text-gray-300 hover:text-amber-400 font-medium transition-colors">Operators</a>
            </nav>

            <div class="flex items-center space-x-4">
                <a href="/beyond" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-black font-semibold tracking-wide transition-all shadow-lg shadow-amber-500/25 hover:shadow-amber-500/40 flex items-center space-x-2">
                    <span>Admin Dashboard</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-32 pb-20 flex-grow">

        <!-- Hero Section -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center pt-12 pb-16">
            <div class="inline-flex items-center space-x-2 px-4 py-2 rounded-full glass-card border border-amber-500/30 mb-8">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs uppercase tracking-wider text-amber-400 font-semibold">Kenya's #1 Outdoor Expedition Engine</span>
            </div>

            <h1 class="font-heading font-extrabold text-4xl sm:text-6xl lg:text-7xl tracking-tight text-white leading-tight max-w-5xl mx-auto mb-8">
                Uncover Kenya's Untamed Trails & <span class="gradient-text">Hidden Gems</span>
            </h1>

            <p class="text-lg sm:text-xl text-gray-400 max-w-3xl mx-auto font-normal leading-relaxed mb-12">
                Explore curated hiking itineraries, track live route safety metrics, unlock trail quests, and book verified local tour operators across Mount Kenya, the Great Rift Valley, and coastal havens.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="#destinations" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-black font-bold text-lg shadow-xl shadow-amber-500/20 transition-all transform hover:-translate-y-0.5">
                    Explore Destinations
                </a>
                <a href="/beyond" class="w-full sm:w-auto px-8 py-4 rounded-xl glass-card hover:bg-white/10 text-white font-semibold text-lg transition-all border border-gray-700 hover:border-gray-500">
                    Open Control Panel
                </a>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-5xl mx-auto mt-20 p-6 glass-card rounded-2xl border border-gray-800">
                <div>
                    <p class="font-heading text-3xl font-extrabold text-amber-400">150+</p>
                    <p class="text-sm text-gray-400 font-medium">Mapped Routes</p>
                </div>
                <div>
                    <p class="font-heading text-3xl font-extrabold text-emerald-400">45+</p>
                    <p class="text-sm text-gray-400 font-medium">Verified Operators</p>
                </div>
                <div>
                    <p class="font-heading text-3xl font-extrabold text-amber-400">12k+</p>
                    <p class="text-sm text-gray-400 font-medium">Trail Explorers</p>
                </div>
                <div>
                    <p class="font-heading text-3xl font-extrabold text-emerald-400">99.8%</p>
                    <p class="text-sm text-gray-400 font-medium">Safety Score</p>
                </div>
            </div>
        </section>

        <!-- Featured Destinations Grid -->
        <section id="destinations" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
                <div>
                    <h2 class="font-heading font-extrabold text-3xl sm:text-4xl text-white">Popular Trails & Destinations</h2>
                    <p class="text-gray-400 mt-2 text-base">Hand-picked wilderness expeditions across Kenya</p>
                </div>
                <a href="/beyond/destinations" class="mt-4 md:mt-0 text-amber-400 hover:text-amber-300 font-semibold flex items-center space-x-1">
                    <span>View all in Dashboard</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="glass-card rounded-2xl overflow-hidden group hover:border-amber-500/50 transition-all duration-300">
                    <div class="h-48 bg-gradient-to-br from-amber-900/40 to-gray-900 relative p-6 flex flex-col justify-between">
                        <span class="px-3 py-1 bg-amber-500/20 text-amber-400 border border-amber-500/30 rounded-full text-xs font-semibold self-start">High Difficulty</span>
                        <div>
                            <span class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Central Kenya</span>
                            <h3 class="font-heading text-2xl font-bold text-white group-hover:text-amber-400 transition-colors">Mount Kenya Point Lenana</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-400 text-sm mb-4">4-Day alpine summit expedition with certified high-altitude guides and mountain hut accommodation.</p>
                        <div class="flex items-center justify-between text-xs text-gray-400 pt-4 border-t border-gray-800">
                            <span>Duration: 4 Days</span>
                            <span class="text-emerald-400 font-semibold">KSh 45,000</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="glass-card rounded-2xl overflow-hidden group hover:border-amber-500/50 transition-all duration-300">
                    <div class="h-48 bg-gradient-to-br from-emerald-900/40 to-gray-900 relative p-6 flex flex-col justify-between">
                        <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-full text-xs font-semibold self-start">Moderate</span>
                        <div>
                            <span class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Rift Valley</span>
                            <h3 class="font-heading text-2xl font-bold text-white group-hover:text-amber-400 transition-colors">Hell's Gate Gorge Trek</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-400 text-sm mb-4">Cycle alongside geothermal canyons, wildlife herds, and guided river bed rock climbing sessions.</p>
                        <div class="flex items-center justify-between text-xs text-gray-400 pt-4 border-t border-gray-800">
                            <span>Duration: 1 Day</span>
                            <span class="text-emerald-400 font-semibold">KSh 3,500</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="glass-card rounded-2xl overflow-hidden group hover:border-amber-500/50 transition-all duration-300">
                    <div class="h-48 bg-gradient-to-br from-blue-900/40 to-gray-900 relative p-6 flex flex-col justify-between">
                        <span class="px-3 py-1 bg-blue-500/20 text-blue-400 border border-blue-500/30 rounded-full text-xs font-semibold self-start">Scenic Adventure</span>
                        <div>
                            <span class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Coastal Kenya</span>
                            <h3 class="font-heading text-2xl font-bold text-white group-hover:text-amber-400 transition-colors">Shimba Hills Reserve</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-400 text-sm mb-4">Tropical rainforest trail featuring Sheldrick Falls, sable antelope sightings, and coastal view points.</p>
                        <div class="flex items-center justify-between text-xs text-gray-400 pt-4 border-t border-gray-800">
                            <span>Duration: 1 Day</span>
                            <span class="text-emerald-400 font-semibold">KSh 5,000</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Showcase -->
        <section id="features" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="font-heading font-extrabold text-3xl sm:text-4xl text-white">Engineered for Adventure & Moderation</h2>
                <p class="text-gray-400 mt-3">Combining real-time GPS tracking, operator verification, and cached admin panel controls.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="glass-card p-8 rounded-2xl">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="font-heading text-xl font-bold text-white mb-3">Verified Local Guides</h3>
                    <p class="text-gray-400 text-sm">Every tour operator and guide passes identity checks, safety certifications, and community reviews.</p>
                </div>

                <div class="glass-card p-8 rounded-2xl">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-6z"/></svg>
                    </div>
                    <h3 class="font-heading text-xl font-bold text-white mb-3">Quest & XP Gamification</h3>
                    <p class="text-gray-400 text-sm">Earn XP points, unlock trail badges, and earn rewards as you complete hiking routes and submit reviews.</p>
                </div>

                <div class="glass-card p-8 rounded-2xl">
                    <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/30 flex items-center justify-center text-blue-400 mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    </div>
                    <h3 class="font-heading text-xl font-bold text-white mb-3">Enterprise Filament Panel</h3>
                    <p class="text-gray-400 text-sm">High-performance admin portal for managing activities, pending moderation queues, and real-time telemetry.</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-800 bg-[#08090c] py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center space-x-3">
                <span class="font-heading font-extrabold text-xl text-white">Beyond<span class="text-amber-500">Trails</span></span>
                <span class="text-gray-600">|</span>
                <span class="text-sm text-gray-400">&copy; {{ date('Y') }} BeyondTrails KE. All rights reserved.</span>
            </div>

            <div class="flex items-center space-x-6 text-sm text-gray-400">
                <a href="/beyond" class="hover:text-amber-400 font-semibold text-amber-500 transition-colors">Admin Portal</a>
                <a href="#destinations" class="hover:text-white transition-colors">Destinations</a>
                <a href="/up" target="_blank" class="hover:text-emerald-400 transition-colors">System Health</a>
            </div>
        </div>
    </footer>

</body>
</html>
