<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { brand: { 50:'#eef2ff',100:'#e0e7ff',200:'#c7d2fe',300:'#a5b4fc',400:'#818cf8',500:'#6366f1',600:'#4f46e5',700:'#4338ca',800:'#3730a3',900:'#312e81' } } } }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body class="h-full">
@php
    // Sidebar nav. Items render only when their route exists, so the menu
    // auto-populates as modules come online across build phases.
    $nav = [
        'Overview' => [
            ['Dashboard', 'dashboard', 'home'],
            ['Analytics', 'analytics.index', 'chart'],
        ],
        'Governance' => [
            ['Controls', 'controls.index', 'shield'],
            ['Frameworks', 'frameworks.index', 'grid'],
            ['Statement of Applicability', 'soa.index', 'shield'],
            ['ISMS Clauses', 'clauses.index', 'doc'],
            ['Documented Information', 'documents.index', 'doc'],
            ['Policies', 'policies.index', 'doc'],
        ],
        'Risk' => [
            ['Risk Register', 'risks.index', 'warn'],
            ['Assets', 'assets.index', 'grid'],
            ['Incidents', 'incidents.index', 'warn'],
        ],
        'Operations' => [
            ['Tasks', 'tasks.index', 'check'],
            ['Assessments', 'assessments.index', 'check'],
            ['Audits', 'audits.index', 'check'],
            ['Evidence', 'evidence.index', 'doc'],
            ['Suppliers', 'suppliers.index', 'grid'],
            ['Awareness & Training', 'training.index', 'users'],
        ],
        'Context & Performance' => [
            ['Interested Parties', 'interested-parties.index', 'users'],
            ['IS Objectives', 'objectives.index', 'target'],
            ['Metrics (KPI/KRI/KCI)', 'metrics.index', 'chart'],
        ],
    ];
    $icons = [
        'home'  => 'M2.25 12l8.954-8.955a1.125 1.125 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75V15.75A1.125 1.125 0 0110.875 14.625h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75',
        'chart' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z',
        'shield'=> 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.249-8.25-3.285z',
        'doc'   => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z',
        'warn'  => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z',
        'check' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'grid'  => 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z',
        'users' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
        'target'=> 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    ];
@endphp
<div class="min-h-full flex">
    <!-- Sidebar -->
    <aside class="hidden md:flex md:flex-col w-64 bg-brand-900 text-brand-100 fixed inset-y-0">
        <div class="h-16 flex items-center gap-2 px-5 border-b border-brand-800">
            <svg class="w-7 h-7 text-brand-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['shield'] }}"/></svg>
            <span class="text-white font-bold tracking-tight">IT-GRC <span class="text-brand-300 font-normal">Portal</span></span>
        </div>
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-6 text-sm">
            @foreach($nav as $section => $items)
                @php $visible = collect($items)->filter(fn($i) => \Illuminate\Support\Facades\Route::has($i[1])); @endphp
                @if($visible->isNotEmpty())
                    <div>
                        <p class="px-2 mb-1 text-[10px] font-semibold uppercase tracking-wider text-brand-400">{{ $section }}</p>
                        <div class="space-y-0.5">
                            @foreach($visible as $item)
                                @php $active = request()->routeIs($item[1]) || request()->routeIs(\Illuminate\Support\Str::beforeLast($item[1], '.').'.*'); @endphp
                                <a href="{{ route($item[1]) }}" class="flex items-center gap-2.5 px-2 py-2 rounded-lg transition {{ $active ? 'bg-brand-700 text-white' : 'hover:bg-brand-800 text-brand-200' }}">
                                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$item[2]] ?? $icons['doc'] }}"/></svg>
                                    <span>{{ $item[0] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </nav>
        <div class="px-4 py-3 border-t border-brand-800 text-[11px] text-brand-400">ISO 27001:2022 · 27019 · NIST CSF · SOC 2 · IEC 62443</div>
    </aside>

    <!-- Main -->
    <div class="flex-1 md:pl-64 flex flex-col min-h-screen">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 sticky top-0 z-10">
            <h1 class="text-lg font-semibold text-gray-900">@yield('title', 'Dashboard')</h1>
            <div class="flex items-center gap-4" x-data="{ open: false }">
                <button @click="open=!open" class="flex items-center gap-2 text-sm text-gray-700 hover:text-gray-900">
                    <span class="w-8 h-8 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-semibold">{{ strtoupper(substr(auth()->user()->full_name ?? 'U', 0, 1)) }}</span>
                    <span class="hidden sm:block">{{ auth()->user()->full_name ?? auth()->user()->email }}</span>
                </button>
                <div x-show="open" @click.outside="open=false" x-cloak class="absolute right-6 top-14 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 text-sm">
                    <div class="px-4 py-2 text-xs text-gray-400 border-b border-gray-100">{{ auth()->user()->email }}</div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-50">Sign out</button>
                    </form>
                </div>
            </div>
        </header>

        @if(session('status'))
            <div class="mx-6 mt-4 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-2 text-sm">{{ session('status') }}</div>
        @endif

        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>
</div>
<style>[x-cloak]{display:none!important}</style>
</body>
</html>
