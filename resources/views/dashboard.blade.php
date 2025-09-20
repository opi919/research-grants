@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6">Dashboard</h2>

            @if (auth()->user()->status === 'temporary')
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Account Notice</p>
                    <p>Your account is temporarily active and locked to this device. Please pay one time fee to gain full
                        access.</p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-blue-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-blue-800">Total Grants</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ number_format($stats['total_grants']) }}</p>
                </div>

                <div class="bg-green-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-green-800">Total Funding</h3>
                    <p class="text-3xl font-bold text-green-600">${{ number_format($stats['total_funding'], 0) }}</p>
                </div>

                <div class="bg-purple-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-purple-800">NIH Grants</h3>
                    <p class="text-3xl font-bold text-purple-600">{{ number_format($stats['nih_grants']) }}</p>
                </div>

                <div class="bg-orange-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-orange-800">NSF Grants</h3>
                    <p class="text-3xl font-bold text-orange-600">{{ number_format($stats['nsf_grants']) }}</p>
                </div>
            </div>

            <div class="text-center">
                <a href="{{ route('search') }}"
                    class="inline-block bg-blue-500 text-white px-8 py-3 rounded-lg hover:bg-blue-600 text-lg font-semibold">
                    Start Searching Grants
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Recent Grants</h3>
                <div class="space-y-3">
                    @foreach ($recent_grants as $grant)
                        <div class="border-l-4 border-blue-500 pl-4">
                            <h4 class="font-medium text-sm">
                                <a href="{{ route('search.show', $grant) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ Str::limit($grant->title, 60) }}
                                </a>
                            </h4>
                            <p class="text-xs text-gray-600">{{ $grant->institution_name }}</p>
                            <p class="text-xs text-gray-500">${{ number_format($grant->award_amount ?? 0) }}</p>
                        </div>
                    @endforeach
                    @if ($recent_grants->isEmpty())
                        <p class="text-sm text-gray-500">No recent grants available.</p>
                    @endif
                </div>
                <div class="mt-2">
                    {{ $recent_grants->links() }}
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Top Institutions by Grant Count</h3>
                <div class="space-y-3">
                    @foreach ($top_institutions as $institution)
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-medium text-sm">{{ Str::limit($institution->institution_name, 40) }}</h4>
                                <p class="text-xs text-gray-600">{{ $institution->grant_count }} grants</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium">${{ number_format($institution->total_funding ?? 0) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-2">
                    {{ $top_institutions->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
