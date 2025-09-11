@extends('layouts.app')

@section('content')
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="mb-4">
            <a href="{{ route('search') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                ← Back to Search Results
            </a>
        </div>

        <div class="space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $grant->title }}</h1>
                <div class="flex items-center space-x-4 text-sm text-gray-500">
                    <span class="px-2 py-1 bg-gray-200 rounded">{{ strtoupper($grant->source) }}</span>
                    <span>Award ID: {{ $grant->award_id ?? ($grant->awd_id ?? $grant->application_id) }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <h3 class="font-semibold text-gray-900">Principal Investigator</h3>
                        <p class="text-gray-700">{{ $grant->pi_name ?? 'Not specified' }}</p>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-900">Institution</h3>
                        <p class="text-gray-700">{{ $grant->institution_name }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $grant->city }}, {{ $grant->state }} {{ $grant->zipcode }}
                        </p>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-900">Funding Agency</h3>
                        <p class="text-gray-700">{{ $grant->funding_agency }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <h3 class="font-semibold text-gray-900">Award Amount</h3>
                        <p class="text-2xl font-bold text-green-600">
                            ${{ number_format($grant->award_amount ?? 0, 2) }}
                        </p>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-900">Project Period</h3>
                        <p class="text-gray-700">
                            {{ $grant->start_date ? $grant->start_date->format('M d, Y') : 'Not specified' }} -
                            {{ $grant->end_date ? $grant->end_date->format('M d, Y') : 'Not specified' }}
                        </p>
                    </div>

                    @if ($grant->award_type)
                        <div>
                            <h3 class="font-semibold text-gray-900">Award Type</h3>
                            <p class="text-gray-700">{{ $grant->award_type }}</p>
                        </div>
                    @endif
                </div>
            </div>

            @if ($grant->abstract)
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2">Abstract</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 whitespace-pre-line">{{ $grant->abstract }}</p>
                    </div>
                </div>
            @endif

            @if ($grant->project_terms)
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2">Project Terms</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700">{{ $grant->project_terms }}</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                <div class="space-y-2">
                    @if ($grant->direct_cost_amt)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Direct Cost:</span>
                            <span class="font-medium">${{ number_format($grant->direct_cost_amt, 2) }}</span>
                        </div>
                    @endif

                    @if ($grant->indirect_cost_amt)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Indirect Cost:</span>
                            <span class="font-medium">${{ number_format($grant->indirect_cost_amt, 2) }}</span>
                        </div>
                    @endif

                    @if ($grant->congressional_district)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Congressional District:</span>
                            <span class="font-medium">{{ $grant->congressional_district }}</span>
                        </div>
                    @endif
                </div>

                <div class="space-y-2">
                    @if ($grant->program_officer_name)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Program Officer:</span>
                            <span class="font-medium">{{ $grant->program_officer_name }}</span>
                        </div>
                    @endif

                    @if ($grant->po_email)
                        <div class="flex justify-between">
                            <span class="text-gray-600">PO Email:</span>
                            <span class="font-medium">{{ $grant->po_email }}</span>
                        </div>
                    @endif

                    @if ($grant->division_name)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Division:</span>
                            <span class="font-medium">{{ $grant->division_name }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
