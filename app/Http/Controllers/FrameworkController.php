<?php

namespace App\Http\Controllers;

use App\Support\Analytics;
use Illuminate\View\View;

class FrameworkController extends Controller
{
    public function index(): View
    {
        $cov = Analytics::frameworkCoverage();

        return view('frameworks.index', ['cov' => $cov]);
    }
}
