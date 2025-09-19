<?php

namespace App\Http\Controllers;

use App\Models\ResearchGrant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_grants' => ResearchGrant::count(),
            'total_funding' => ResearchGrant::sum('award_amount'),
            'nih_grants' => ResearchGrant::where('source', 'csv1')->count(),
            'excel_grants' => ResearchGrant::where('source', 'csv2')->count(),
            'nsf_grants' => ResearchGrant::where('source', 'json')->count(),
        ];

        $recent_grants = Cache::remember('recent_grants', 60, function () {
            return ResearchGrant::orderBy('created_at', 'desc')
                ->paginate(10);
        });

        $top_institutions = Cache::remember('top_institutions', 60, function () {
            return ResearchGrant::selectRaw('institution_name, COUNT(*) as grant_count, SUM(award_amount) as total_funding')
                ->whereNotNull('institution_name')
                ->groupBy('institution_name')
                ->orderBy('grant_count', 'desc')
                ->paginate(10);
        });

        return view('dashboard', compact('stats', 'recent_grants', 'top_institutions'));
    }
}
