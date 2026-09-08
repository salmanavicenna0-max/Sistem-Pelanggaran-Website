@extends('layouts.app')

@section('content')

<!-- GURU Dashboard -->
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-[#182A3C]">Dashboard Guru</h2>
            <div class="w-8 h-8 rounded-full bg-[#044A87] text-white flex items-center justify-center text-sm font-bold">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 gap-3 mb-5">
            <a href="{{ route('reports.create') }}" class="group bg-[#044A87] text-white rounded-xl py-3 flex items-center justify-center hover:bg-[#0360A4] transition-colors">
                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 4"></svg>
                <span class="hidden md:inline">Buat Laporan</span>
            </a>
            <div class="group bg-white rounded-xl py-3 flex items-center justify-center border border-slate-200 hover:bg-emerald-50 hover:text-emerald-600 transition-colors">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 4"></svg>
                <span>{{ number_format($totalReports) }} Laporan</span>
            </div>
            <div class="group bg-white rounded-xl py-3 flex items-center justify-center border border-slate-200 hover:bg-emerald-50 hover:text-emerald-600 transition-colors">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 4"></svg>
                <span class="text-emerald-600">{{ number_format($approvedReports) }}-approved</span>
            </div>
            <div class="group bg-white rounded-xl py-3 flex items-center justify-center border border-slate-200 hover:bg-amber-50 hover:text-amber-600 transition-colors">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 4"></svg>
                <span class="text-amber-500">{{ number_format($pendingReports) }}-pending</span>
            </div>
        </div>

        <!-- Recent Reports -->
        <div>
            <h3 class="text-sm font-semibold text-[#182A3C] mb-3">Laporan Terbaru</h3>
            <div class="space-y-2 max-h-48 overflow-y-auto">
                @foreach ($reports as $report)
                    <div class="flex items-center gap-3 px-2 py-1 rounded bg-white border border-slate-100">
                        <div class="w-2 h-2 rounded-full bg-{{ $report->type === 'achievement' ? 'emerald-500' : 'rose-500' }}"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-[#182A3C]">{{ $report->description }}</p>
                            <p class="text-xs text-[#64748B]">{{ $report->occurred_on->format('d M Y') }} • {{ ucfirst($report->type) }}</p>
                        </div>
                        <span class="text-xs text-{{ $report->status === 'approved' ? 'emerald-500' : ($report->status === 'pending' ? 'amber-500' : 'rose-500') }} font-medium">{{ ucfirst($report->status) }}</span>
                    </div>
                @endforeach
                @if ($reports->isEmpty())
                    <p class="text-xs text-[#9CA3AF] pt-2">Belum ada laporan.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection