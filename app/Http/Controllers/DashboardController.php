<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Audit;
use App\Models\Control;
use App\Models\Incident;
use App\Support\Analytics;
use App\Support\Posture;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $h = Posture::headline();
        Posture::recordSnapshot();

        $byStatus = Control::query()->selectRaw('status, count(*) as c')->groupBy('status')->pluck('c', 'status');
        $byTheme = Control::where('framework', 'ISO 27001:2022')->selectRaw('theme, count(*) as c')->groupBy('theme')->pluck('c', 'theme');
        $heat = Analytics::heatmap('inherent');
        $myWork = Analytics::myWork(Auth::user());
        $recent = ActivityLog::with('user')->orderByDesc('created_at')->limit(8)->get();
        $openIncidents = Incident::whereNotIn('status', ['Resolved', 'Closed'])->orderByDesc('reported_at')->limit(5)->get();
        $upcomingAudits = Audit::whereIn('status', ['Planned', 'In Progress'])->orderByRaw('start_date is null, start_date')->limit(5)->get();

        return view('dashboard', compact('h', 'byStatus', 'byTheme', 'heat', 'myWork', 'recent', 'openIncidents', 'upcomingAudits'));
    }
}
