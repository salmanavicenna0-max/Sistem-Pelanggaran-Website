@extends('layouts.app')

@section('content')

<!-- KESISWAAN / BK Dashboard -->
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 mb-6">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4 mb-5">
            <div class="bg-[#044A87] text-white rounded-xl py-4 flex items-center justify-center">
                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 4"></svg>
                <span class="hidden md:inline">Siswa</span>
                <div class="text-2xl font-bold">{{ number_format($stats['total_students']) }}</div>
            </div>
            <div class="bg-[#51C4C1] text-white rounded-xl py-4 flex items-center justify-center">
                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 4"></svg>
                <span class="hidden md:inline">Total Laporan</span>
                <div class="text-2xl font-bold">{{ number_format($stats['total_reports']) }}</div>
            </div>
            <div class="bg-[#E9A23B] text-white rounded-xl py-4 flex items-center justify-center">
                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 4"></svg>
                <span class="hidden md:inline">Pending Approval</span>
                <div class="text-2xl font-bold text-amber-200">{{ number_format($stats['pending_approval']) }}</div>
            </div>
            <div class="bg-[#D64545] text-white rounded-xl py-4 flex items-center justify-center">
                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 4"></svg>
                <span class="hidden md:inline">Kasus Aktif</span>
                <div class="text-2xl font-bold">{{ number_format($stats['active_cases']) }}</div>
            </div>
        </div>

        {{-- Zone Distribution Chart --}}
        <div>
            <h3 class="text-sm font-semibold text-[#182A3C] mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#044A87]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v10c7-6 7-6 14 0v-10m-7 3h5"></svg>
                Distribusi Zona Poin Siswa
            </h3>
            <div class="bg-[#F5F9FA] rounded-xl p-4">
                <canvas id="zoneChart" height="120"></canvas>
            </div>
        </div>

        {{-- Top Students --}}
        <div>
            <h3 class="text-sm font-semibold text-[#182A3C] mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#044A87]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002 2h2a2 2 0 002-2h-2m-3-3h4m4-4h4m5-3v4m-5 3h4"/></svg>
                Siswa Terbanyak Poin
            </h3>
            <div class="space-y-3 max-h-40 overflow-y-auto">
                @foreach ($topStudents as $s)
                    <div class="flex items-center gap-3 px-2 py-1 rounded bg-white border border-slate-100">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#044A87] to-[#51C4C1] flex items-center justify-center text-white font-medium text-sm">
                            {{ strtoupper(substr($s->user->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-[#182A3C]">{{ $s->user->name }}</p>
                            <p class="text-xs text-[#64748B]">Poin: {{ number_format($s->getCurrentPoints()) }} - {{ $s->getPointZone['name'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection