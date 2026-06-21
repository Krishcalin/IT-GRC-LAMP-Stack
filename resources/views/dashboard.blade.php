@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl bg-gradient-to-r from-brand-700 to-brand-900 text-white p-8">
        <h2 class="text-2xl font-bold">Welcome to the IT-GRC Portal</h2>
        <p class="text-brand-200 mt-1">ISO 27001:2022 ISMS management — LAMP / Laravel edition.</p>
        <p class="text-brand-300 text-sm mt-4 max-w-2xl">
            The platform shell is live. Governance, risk, compliance and operations modules are being
            wired in — the sidebar fills in automatically as each module comes online.
        </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Users</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['users'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">RBAC Roles</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['roles'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Frameworks</p>
            <p class="text-3xl font-bold text-brand-600 mt-1">5</p>
            <p class="text-xs text-gray-400 mt-1">ISO 27001/27019 · NIST CSF · SOC 2 · IEC 62443</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Stack</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">LAMP</p>
            <p class="text-xs text-gray-400 mt-1">Linux · Apache · MySQL · PHP/Laravel</p>
        </div>
    </div>
</div>
@endsection
