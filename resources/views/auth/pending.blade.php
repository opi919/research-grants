@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="text-center">
            <div class="mx-auto w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <h2 class="text-2xl font-bold text-gray-900 mb-2">Account Pending Approval</h2>

            <p class="text-gray-600 mb-6">
                Your account has been created successfully and is currently pending admin approval.
                You will receive an email notification once your account is approved.
            </p>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-yellow-800">
                    <strong>Account Status:</strong> {{ ucfirst(auth()->user()->status) }}
                </p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600">
                    Logout
                </button>
            </form>
        </div>
    </div>
@endsection
