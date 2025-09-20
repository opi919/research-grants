@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6 text-center">Register</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <input type="hidden" name="device_fingerprint" value="">

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Institution</label>
                <input type="text" name="institution" value="{{ old('institution') }}"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Mobile Number</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
            </div>
            <input type="hidden" name="device_fingerprint" value="" id="device_fingerprint">

            <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">
                Register
            </button>
        </form>

        <p class="mt-4 text-center text-sm text-gray-600">
            Already have an account? <a href="{{ route('login') }}" class="text-blue-500 hover:underline">Login</a>
        </p>
    </div>
@endsection
