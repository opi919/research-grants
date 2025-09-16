{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Research Grant Database</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fingerprintjs2/2.1.4/fingerprint2.min.js"></script>
</head>

<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold"><a href="/">Research Grant Database</a></h1>
            <div class="flex items-center space-x-4">
                @auth
                    <span>Welcome, {{ auth()->user()->name }}</span>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.users') }}" class="hover:underline">Admin Panel</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="hover:underline">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:underline">Login</a>
                    <a href="{{ route('register') }}" class="hover:underline">Register</a>
                @endauth
            </div>
        </div>
    </nav>

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
        // Generate device fingerprint for single device authentication
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Fingerprint2 !== 'undefined') {
                Fingerprint2.get(function(components) {
                    const fingerprint = Fingerprint2.x64hash128(components.map(function(pair) {
                        return pair.value;
                    }).join(), 31);

                    // Store fingerprint in hidden input if login/register form exists
                    const deviceInput = document.querySelector('input[name="device_fingerprint"]');
                    if (deviceInput) {
                        deviceInput.value = fingerprint;
                    }
                });
            }
        });
    </script>
</body>

</html>
