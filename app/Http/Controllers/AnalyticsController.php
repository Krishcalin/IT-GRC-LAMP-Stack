<?php

namespace App\Http\Controllers;

use App\Models\PostureSnapshot;
use App\Support\Analytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(Request $r): View
    {
        $basis = $r->input('basis') === 'residual' ? 'residual' : 'inherent';
        $heat = Analytics::heatmap($basis);
        $trend = PostureSnapshot::orderBy('snapshot_date')->get();
        $myWork = Analytics::myWork(Auth::user());

        return view('analytics.index', compact('heat', 'trend', 'myWork', 'basis'));
    }
}
