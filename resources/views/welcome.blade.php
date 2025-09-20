<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>BideshJabo.online — Find Professors & Funding in the USA</title>
    <meta name="description"
        content="Explore professors, universities, and funded research projects in the USA. Search by research interest and discover grants.">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <!-- TailwindCSS (CDN for quick start) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Optional: Inter/Manrope font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        html,
        body {
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
        }

        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
        }

        .container-narrow {
            max-width: 1080px;
        }
    </style>
</head>

<body class="bg-gradient-to-b from-sky-50 via-white to-white text-slate-800 antialiased">

    <!-- Nav -->
    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200">
        <div class="mx-auto px-4 py-3 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 font-extrabold text-xl">
                <span class="text-sky-600">BideshJabo</span>
            </a>

            <nav class="hidden md:flex items-center gap-6 text-sm">
                <a href="#why" class="hover:text-sky-700">Why Us</a>
                <a href="#how" class="hover:text-sky-700">How It Works</a>
                <a href="#features" class="hover:text-sky-700">Features</a>
                <a href="#cta" class="hover:text-sky-700">Get Started</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}"
                    class="px-4 py-2 rounded-xl border border-slate-300 hover:bg-slate-50">Login</a>
                <a href="{{ route('register') }}"
                    class="px-4 py-2 rounded-xl bg-sky-600 text-white hover:bg-sky-700">Register</a>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section class="relative">
        <div class="mx-auto container-narrow px-4 py-16 md:py-24 grid md:grid-cols-2 gap-10 items-center">
            <div>
                <div
                    class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-medium bg-sky-50 text-sky-700 border border-sky-200">
                    🚀 Connect with Professors & Research Opportunities in the USA
                </div>
                <h1 class="mt-4 text-3xl md:text-5xl font-extrabold leading-tight">
                    Find the Right Professor. Secure the Right Funding. Shape Your Future.
                </h1>
                <p class="mt-4 text-slate-600 md:text-lg">
                    Explore thousands of professors, universities, and funded research projects in the USA.
                    Register today and take the first step toward your academic journey abroad.
                </p>
            </div>

            <!-- Visual / Stats -->
            <div class="md:pl-6">
                <div class="grid gap-4">
                    <div class="p-6 rounded-2xl border border-slate-200 bg-white">
                        <div class="text-sm text-slate-500">Total Grants</div>
                        <div class="mt-2 text-3xl font-extrabold">{{ $stats['total_grants'] }}</div>
                    </div>
                    <div class="p-6 rounded-2xl border border-slate-200 bg-white">
                        <div class="text-sm text-slate-500">Universities Covered</div>
                        <div class="mt-2 text-3xl font-extrabold">{{ $stats['total_institutions'] }}</div>
                    </div>
                    <div class="p-6 rounded-2xl border border-slate-200 bg-white">
                        <div class="text-sm text-slate-500">Total Funding</div>
                        <div class="mt-2 text-3xl font-extrabold">${{ number_format($stats['total_funding'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why -->
    <section id="why" class="py-14 md:py-20 bg-white">
        <div class="mx-auto container-narrow px-4">
            <h2 class="text-2xl md:text-3xl font-extrabold">Why BideshJabo.online?</h2>
            <div class="mt-6 grid md:grid-cols-2 gap-6">
                <div class="p-6 rounded-2xl border border-slate-200 bg-slate-50">
                    <h3 class="font-bold">Comprehensive Database</h3>
                    <p class="mt-2 text-slate-600">Access detailed profiles of professors, universities, and their
                        research interests.</p>
                </div>
                <div class="p-6 rounded-2xl border border-slate-200 bg-slate-50">
                    <h3 class="font-bold">Funding Opportunities</h3>
                    <p class="mt-2 text-slate-600">Find information on available grants, scholarships, and funded
                        projects.</p>
                </div>
                <div class="p-6 rounded-2xl border border-slate-200 bg-slate-50">
                    <h3 class="font-bold">Personalized Search</h3>
                    <p class="mt-2 text-slate-600">Filter by university, research interest, or professor preference.</p>
                </div>
                <div class="p-6 rounded-2xl border border-slate-200 bg-slate-50">
                    <h3 class="font-bold">Student-Friendly</h3>
                    <p class="mt-2 text-slate-600">Designed to simplify connecting with the right academic mentor.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how" class="py-14 md:py-20">
        <div class="mx-auto container-narrow px-4">
            <h2 class="text-2xl md:text-3xl font-extrabold">How It Works</h2>
            <div class="mt-6 grid md:grid-cols-4 gap-6">
                @php
                    $steps = [
                        ['title' => 'Register & Login', 'desc' => 'Create your free account in minutes.', 'num' => '1'],
                        [
                            'title' => 'Search Professors & Universities',
                            'desc' => 'Filter by research interest, location, or department.',
                            'num' => '2',
                        ],
                        [
                            'title' => 'Discover Funding',
                            'desc' => 'View grants and projects professors are leading.',
                            'num' => '3',
                        ],
                        [
                            'title' => 'Plan Next Steps',
                            'desc' => 'Make informed decisions for applications.',
                            'num' => '4',
                        ],
                    ];
                @endphp
                @foreach ($steps as $s)
                    <div class="p-6 rounded-2xl border border-slate-200 bg-white">
                        <div
                            class="h-10 w-10 flex items-center justify-center rounded-full bg-sky-600 text-white font-bold">
                            {{ $s['num'] }}</div>
                        <h3 class="mt-4 font-bold">{{ $s['title'] }}</h3>
                        <p class="mt-2 text-slate-600">{{ $s['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-14 md:py-20 bg-slate-50">
        <div class="mx-auto container-narrow px-4">
            <h2 class="text-2xl md:text-3xl font-extrabold">Key Features</h2>
            <div class="mt-6 grid md:grid-cols-4 gap-6">
                <div class="p-6 rounded-2xl border border-slate-200 bg-white">
                    <div class="text-2xl">🔍</div>
                    <h3 class="mt-2 font-bold">Smart Search</h3>
                    <p class="mt-2 text-slate-600">Quickly find professors aligned with your research goals.</p>
                </div>
                <div class="p-6 rounded-2xl border border-slate-200 bg-white">
                    <div class="text-2xl">🎓</div>
                    <h3 class="mt-2 font-bold">University Explorer</h3>
                    <p class="mt-2 text-slate-600">Browse universities across the USA.</p>
                </div>
                <div class="p-6 rounded-2xl border border-slate-200 bg-white">
                    <div class="text-2xl">💡</div>
                    <h3 class="mt-2 font-bold">Interest Match</h3>
                    <p class="mt-2 text-slate-600">Match your interests to ongoing projects.</p>
                </div>
                <div class="p-6 rounded-2xl border border-slate-200 bg-white">
                    <div class="text-2xl">💰</div>
                    <h3 class="mt-2 font-bold">Funding Details</h3>
                    <p class="mt-2 text-slate-600">Explore grants and projects professors are involved in.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section id="cta" class="py-16 md:py-24">
        <div class="mx-auto container-narrow px-4 text-center">
            <h2 class="text-2xl md:text-4xl font-extrabold">Your Dream University. Your Dream Research. Your Dream
                Future.</h2>
            <p class="mt-3 text-slate-600 md:text-lg">Don’t wait—start exploring opportunities today!</p>
            <div class="mt-6 flex items-center justify-center gap-4">
                <a href="{{ route('register') }}"
                    class="px-6 py-3 rounded-xl bg-sky-600 text-white hover:bg-sky-700">Register Free</a>
                <a href="{{ route('login') }}"
                    class="px-6 py-3 rounded-xl border border-slate-300 hover:bg-slate-50">Login</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto container-narrow px-4 py-10 grid md:grid-cols-2 gap-6">
            <div>
                <div class="font-extrabold text-lg">BideshJabo.online</div>
                <p class="mt-2 text-slate-600">
                    Empowering students to connect with the right professors and research opportunities.
                </p>
            </div>
            <div class="md:text-right">
                <div class="text-slate-600">
                    📧 Contact: <a class="underline hover:text-sky-700"
                        href="mailto:support@bideshjabo.online">support@bideshjabo.online</a>
                </div>
                <div class="mt-2">
                    🌍 <a class="underline hover:text-sky-700" href="/">BideshJabo.online</a>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>
