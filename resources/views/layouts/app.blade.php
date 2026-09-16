<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sistem Pencatatan Pelanggaran Siswa' }} - SMAN 6 Bandung</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-[#F5F9FA] text-[#182A3C] min-h-full flex antialiased font-sans">

    {{-- SIDEBAR --}}
    @include('layouts.sidebar')


    {{-- MAIN WRAPPER --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        {{-- HEADER TOPBAR --}}
        <header class="h-16 bg-white border-b border-slate-200/80 px-6 flex items-center justify-between shadow-xs sticky top-0 z-10">
            <div class="flex items-center gap-4">
                <span class="text-xs font-semibold px-2.5 py-1 rounded bg-[#044A87]/10 text-[#044A87]">
                    Tahun Ajaran: 2026/2027 (Aktif)
                </span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-[#64748B] hidden sm:inline">SMAN 6 BANDUNG • Terakreditasi A</span>
                <div class="w-8 h-8 rounded-full bg-[#044A87] text-white flex items-center justify-center text-xs font-bold">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            </div>
        </header>

        {{-- FLASH MESSAGES --}}
        @if (session('status'))
            <div class="mx-6 mt-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-[#16A36A] text-sm flex items-center gap-3 shadow-xs">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mx-6 mt-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-[#D64545] text-sm flex items-center gap-3 shadow-xs">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- CONTENT BODY --}}
        <main id="app" class="flex-1 overflow-y-auto p-6 md:p-8">
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
