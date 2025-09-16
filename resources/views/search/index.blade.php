@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6">Search Research Grants</h2>

            <form method="GET" action="{{ route('search') }}" class="space-y-4">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Search Term</label>
                        <input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Title, PI, Institution, Abstract..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-3 border-2 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Institution</label>
                        <input type="text" name="institution" value="{{ request('institution') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-3 border-2 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">PI Name</label>
                        <input type="text" name="pi" value="{{ request('pi') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-3 border-2 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">State</label>
                        <select name="state"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-3 border-2 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <option value="">All States</option>
                            @foreach ($states as $state)
                                <option value="{{ $state }}" {{ request('state') === $state ? 'selected' : '' }}>
                                    {{ $state }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Funding Agency</label>
                        <select name="agency"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-3 border-2 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <option value="">All Agencies</option>
                            @foreach ($agencies as $agency)
                                <option value="{{ $agency }}" {{ request('agency') === $agency ? 'selected' : '' }}>
                                    {{ $agency }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Award Amount (Min)</label>
                        <input type="number" name="min_amount" value="{{ request('min_amount') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-3 border-2 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Award Amount (Max)</label>
                        <input type="number" name="max_amount" value="{{ request('max_amount') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-3 border-2 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-3 border-2 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-3 border-2 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                        Search
                    </button>

                    <div class="flex space-x-2">
                        <a href="{{ route('search.export', request()->query()) }}"
                            class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                            Export CSV
                        </a>
                        <a href="{{ route('search') }}"
                            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                            Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold">
                        Results ({{ $grants->total() }} total)
                    </h3>

                    <div class="flex items-center space-x-2">
                        <label class="text-sm">Sort by:</label>
                        <select onchange="location.href=this.value" class="rounded border-gray-300">
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => 'desc']) }}"
                                {{ request('sort') === 'created_at' ? 'selected' : '' }}>
                                Latest
                            </option>
                            <option
                                value="{{ request()->fullUrlWithQuery(['sort' => 'award_amount', 'order' => 'desc']) }}"
                                {{ request('sort') === 'award_amount' ? 'selected' : '' }}>
                                Highest Amount
                            </option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'start_date', 'order' => 'desc']) }}"
                                {{ request('sort') === 'start_date' ? 'selected' : '' }}>
                                Start Date
                            </option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'title', 'order' => 'asc']) }}"
                                {{ request('sort') === 'title' ? 'selected' : '' }}>
                                Title A-Z
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="divide-y">
                @forelse($grants as $grant)
                    <div class="p-6 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-blue-600 hover:text-blue-800">
                                    <a href="{{ route('search.show', $grant) }}">{{ $grant->title }}</a>
                                </h4>

                                <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                                    <div>
                                        <strong>PI:</strong> {{ $grant->pi_name }}
                                    </div>
                                    <div>
                                        <strong>Institution:</strong> {{ $grant->institution_name }}
                                    </div>
                                    <div>
                                        <strong>Location:</strong> {{ $grant->city }}, {{ $grant->state }}
                                    </div>
                                    <div>
                                        <strong>Agency:</strong> {{ $grant->funding_agency }}
                                    </div>
                                    <div>
                                        <strong>Amount:</strong> ${{ number_format($grant->award_amount ?? 0, 2) }}
                                    </div>
                                    <div>
                                        <strong>Period:</strong> {{ $grant->start_date }} to {{ $grant->end_date }}
                                    </div>
                                </div>

                                @if ($grant->abstract)
                                    <p class="mt-3 text-sm text-gray-700">
                                        {{ Str::limit($grant->abstract, 200) }}
                                    </p>
                                @endif
                            </div>

                            <div class="ml-4 text-right">
                                <span class="inline-block px-2 py-1 text-xs rounded-full bg-gray-200 text-gray-800">
                                    {{ strtoupper($grant->source) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">
                        No grants found matching your criteria.
                    </div>
                @endforelse
            </div>

            @if ($grants->hasPages())
                <div class="p-6 border-t">
                    {{ $grants->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
