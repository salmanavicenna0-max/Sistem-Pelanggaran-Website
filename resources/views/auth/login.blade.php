<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Sistem Pelanggaran Siswa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F5F9FA] min-h-screen flex items-center justify-center p-4 font-sans text-[#182A3C]">
    <main class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-100 p-8">
        <div class="mb-6 text-center">
            <h1 class="text-2xl font-bold text-[#182A3C]">Masuk Sistem</h1>
            <p class="text-sm text-[#64748B] mt-1">Sistem Pencatatan Pelanggaran Siswa</p>
        </div>

        @if (session('status'))
            <div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-sm text-[#16A36A]">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="login" class="block text-sm font-medium text-[#182A3C] mb-1">Username atau NIS</label>
                <input id="login" name="login" type="text" value="{{ old('login') }}" required autofocus autocomplete="username"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] focus:border-transparent text-sm">
                @error('login')
                    <p class="text-xs text-[#D64545] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-[#182A3C] mb-1">Password</label>
                <input id="password" name="password" type="password" required autocomplete="current-password"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] focus:border-transparent text-sm">
                @error('password')
                    <p class="text-xs text-[#D64545] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full py-2.5 px-4 rounded-lg bg-[#044A87] hover:bg-[#0360A4] text-white font-medium text-sm transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#044A87]">
                    Masuk
                </button>
            </div>
        </form>
    </main>
</body>
</html>
