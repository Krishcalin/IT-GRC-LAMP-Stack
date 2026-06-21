<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Phase 1 dashboard. Replaced with the full GRC posture dashboard
     * (compliance/conformity/readiness scores, risk heatmap, charts) once the
     * domain models land in Phase 2/4.
     */
    public function index(): View
    {
        $stats = [
            'users' => User::count(),
            'roles' => Role::count(),
        ];

        return view('dashboard', compact('stats'));
    }
}
