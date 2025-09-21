<?php

// app/Http/Controllers/SearchController.php
namespace App\Http\Controllers;

use App\Models\ResearchGrant;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'approved']);
    }

    public function index(Request $request)
    {
        $query = ResearchGrant::query();

        // Text search
        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('pi_name', 'like', "%{$searchTerm}%")
                    ->orWhere('institution_name', 'like', "%{$searchTerm}%")
                    ->orWhere('abstract', 'like', "%{$searchTerm}%");
            });
        }

        // Institution filter
        if ($request->filled('institution')) {
            $query->byInstitution($request->institution);
        }

        // PI filter
        if ($request->filled('pi')) {
            $query->byPI($request->pi);
        }

        // State filter
        if ($request->filled('state')) {
            $query->byState($request->state);
        }

        // Funding agency filter
        if ($request->filled('agency')) {
            $query->byFundingAgency($request->agency);
        }

        // Amount range filter
        if ($request->filled('min_amount') || $request->filled('max_amount')) {
            $min = $request->min_amount ?? 0;
            $max = $request->max_amount ?? 999999999;
            $query->byAmountRange($min, $max);
        }

        // Date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->byDateRange($request->start_date, $request->end_date);
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');

        $allowedSorts = ['award_amount', 'start_date', 'end_date', 'title', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $grants = $query->paginate(20)->withQueryString();

        // Get filter options for dropdowns
        $states = Cache::remember('research_grant_states', 60 * 24 * 30, function () {
            return ResearchGrant::distinct()->pluck('state')->filter()->sort();
        });
        $agencies = Cache::remember('research_grant_agencies', 60 * 24 * 30, function () {
            return ResearchGrant::distinct()->pluck('funding_agency')->filter()->sort();
        });

        return view('search.index', compact('grants', 'states', 'agencies'));
    }

    public function show(ResearchGrant $grant)
    {
        return view('search.show', compact('grant'));
    }

    public function export(Request $request)
    {
        // Apply same filters as search
        $query = ResearchGrant::query();

        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('pi_name', 'like', "%{$searchTerm}%")
                    ->orWhere('institution_name', 'like', "%{$searchTerm}%")
                    ->orWhere('abstract', 'like', "%{$searchTerm}%");
            });
        }

        // Add other filters...

        $grants = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="research_grants_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($grants) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Award ID',
                'Title',
                'PI Name',
                'Institution',
                'City',
                'State',
                'Award Amount',
                'Start Date',
                'End Date',
                'Funding Agency',
                'Source'
            ]);

            foreach ($grants as $grant) {
                fputcsv($file, [
                    $grant->award_id,
                    $grant->title,
                    $grant->pi_name,
                    $grant->institution_name,
                    $grant->city,
                    $grant->state,
                    $grant->award_amount,
                    $grant->start_date,
                    $grant->end_date,
                    $grant->funding_agency,
                    $grant->source,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
