{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BideshJabo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fingerprintjs2/2.1.4/fingerprint2.min.js"></script>
</head>

<body class="bg-gray-100">
    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200">
        <div class="mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
            <!-- Brand -->
            <a href="/" class="flex items-center gap-2 font-extrabold text-xl">
                <span class="text-sky-600">BideshJabo</span>
            </a>

            <!-- Desktop nav -->
            <div class="hidden md:flex items-center gap-4">
                @auth
                    <span class="text-sm text-slate-700">Welcome, {{ auth()->user()->name }}</span>

                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.users') }}" class="hover:underline text-sm">Admin Panel</a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="hover:underline text-sm">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:underline text-sm">Login</a>
                    <a href="{{ route('register') }}" class="hover:underline text-sm">Register</a>
                @endauth
            </div>

            <!-- Mobile hamburger -->
            <button id="mobile-menu-button"
                class="md:hidden inline-flex items-center justify-center rounded-lg p-2 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500"
                aria-controls="mobile-menu" aria-expanded="false" aria-label="Open main menu" type="button">
                <!-- Icon: hamburger -->
                <svg id="icon-open" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                </svg>
                <!-- Icon: close (hidden by default) -->
                <svg id="icon-close" class="h-6 w-6 hidden" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Mobile menu panel -->
        <div id="mobile-menu" class="md:hidden hidden border-t border-slate-200 bg-white">
            <div class="px-4 py-3 space-y-2">
                @auth
                    <div class="text-sm text-slate-700">Welcome, {{ auth()->user()->name }}</div>

                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.users') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-100">Admin
                            Panel</a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-100">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-100">Login</a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-100">Register</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="container mx-auto py-8 px-4">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <script>
        // Robust device fingerprint fill for register/login forms
        document.addEventListener('DOMContentLoaded', function() {
            function setFingerprintValue(fp) {
                document.querySelectorAll('#device_fingerprint').forEach(function(el) {
                    el.value = fp;
                });
            }

            function computeFingerprint() {
                if (!window.Fingerprint2) {
                    console.warn('Fingerprint2 not loaded');
                    return;
                }

                // Optional exclusions to reduce entropy sources that may stall
                var options = {
                    excludeWebGLParameters: true,
                    excludeAdBlock: true,
                    excludeAudioIOS: true,
                };

                try {
                    // v2 API
                    Fingerprint2.get(options, function(components) {
                        var values = components.map(function(c) {
                            return c.value;
                        });
                        var fp = Fingerprint2.x64hash128(values.join(''), 31);
                        setFingerprintValue(fp);
                        // console.log('FP set:', fp);
                    });
                } catch (e) {
                    console.error('Fingerprint generation failed:', e);
                }
            }

            // Run when the browser is idle to ensure all components are ready
            if ('requestIdleCallback' in window) {
                requestIdleCallback(computeFingerprint, {
                    timeout: 1000
                });
            } else {
                // Fallback for browsers without rIC
                setTimeout(computeFingerprint, 500);
            }

            // Safety: block submit if fingerprint didn’t populate yet
            document.addEventListener('submit', function(e) {
                var input = e.target.querySelector('#device_fingerprint');
                if (input && (!input.value || input.value.length < 16)) {
                    // Try once more quickly, then allow submit on next attempt
                    computeFingerprint();
                    // Prevent the first submit if still empty
                    e.preventDefault();
                    setTimeout(function() {
                        if (input.value && input.value.length >= 16) {
                            e.target.submit();
                        } else {
                            console.log("length", input.value.length);
                            alert('Still initializing your device fingerprint. Please try again.');
                        }
                    }, 200);
                }
            }, true);
        });
    </script>

    <script>
        (function() {
            const btn = document.getElementById('mobile-menu-button');
            const panel = document.getElementById('mobile-menu');
            const iconOpen = document.getElementById('icon-open');
            const iconClose = document.getElementById('icon-close');

            if (!btn || !panel) return;

            function setOpen(open) {
                panel.classList.toggle('hidden', !open);
                iconOpen.classList.toggle('hidden', open);
                iconClose.classList.toggle('hidden', !open);
                btn.setAttribute('aria-expanded', String(open));
                btn.setAttribute('aria-label', open ? 'Close main menu' : 'Open main menu');
            }

            let isOpen = false;

            btn.addEventListener('click', () => {
                isOpen = !isOpen;
                setOpen(isOpen);
            });

            // Close when clicking outside
            document.addEventListener('click', (e) => {
                if (!isOpen) return;
                if (!panel.contains(e.target) && !btn.contains(e.target)) {
                    isOpen = false;
                    setOpen(false);
                }
            });

            // Close on escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && isOpen) {
                    isOpen = false;
                    setOpen(false);
                    btn.focus();
                }
            });

            // Reset on resize to desktop
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 768 && isOpen) {
                    isOpen = false;
                    setOpen(false);
                }
            });
        })();
    </script>
</body>

</html>
