<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in · {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{brand:{300:'#a5b4fc',400:'#818cf8',500:'#6366f1',600:'#4f46e5',700:'#4338ca',800:'#3730a3',900:'#312e81'}}}}}</script>
</head>
<body class="h-full bg-gradient-to-br from-brand-900 via-gray-900 to-brand-800 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-2 text-white">
                <svg class="w-9 h-9 text-brand-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.249-8.25-3.285z"/></svg>
                <span class="text-2xl font-bold">IT-GRC Portal</span>
            </div>
            <p class="text-brand-300 text-sm mt-2">ISO 27001:2022 Governance, Risk &amp; Compliance</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h1 class="text-xl font-semibold text-gray-900 mb-1">Sign in</h1>
            <p class="text-sm text-gray-500 mb-6">Access your ISMS workspace</p>

            @if($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-2 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input name="email" type="email" value="{{ old('email') }}" required autofocus
                           class="w-full rounded-lg border-gray-300 border px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none"
                           placeholder="admin@company.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input name="password" type="password" required
                           class="w-full rounded-lg border-gray-300 border px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none"
                           placeholder="••••••••">
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500"> Remember me
                </label>
                <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-medium py-2.5 rounded-lg transition">Sign in</button>
            </form>
        </div>
        <p class="text-center text-brand-300/70 text-xs mt-6">LAMP / Laravel edition · MySQL · Apache · PHP</p>
    </div>
</body>
</html>
