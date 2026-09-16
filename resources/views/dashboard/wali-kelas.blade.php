@extends('layouts.app')

@section('content')

<!-- WALI KELAS Dashboard -->
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-xl font-bold text-[#182A3C]">Kelas: {{ $class->name }}</h2>
                <p class="text-sm text-[#64748B]">Tingkat: Grade {{ $class->grade_level }} • Tahun Ajaran: 2026/2027</p>
            </div>
            <div class="w-8 h-8 rounded-full bg-[#044A87] text-white flex items-center justify-center text-sm font-bold">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
        </div>

        <!-- Class Point Summary -->
        <div class="grid grid-cols-2 gap-3 mb-5">
            @foreach ($classPointSummary as $student)
                <div class="bg-[#F5F9FA] rounded-xl p-4 border-l-4 border-{{ str_replace('#', '', $student['zone']['color']) ?? 'gray' }}">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-[#182A3C]">{{ $student['name'] }}</span>
                        <span class="text-xs font-bold text-{{ str_replace([' ', '-'], '', $student['zone']['color']) ?? 'gray' }}">{{ number_format($student['currentPoints']) }}</span>
                    </div>
                    <p class="text-caption text-[10px] uppercase tracking-wider text-{{ str_replace([' ', '-'], '', $student['zone']['color']) ?? 'gray' }}">{{ $student['zone']['name'] }}</p>
                </div>
            @endforeach
        </div>

        <!-- Class Reports -->
        <div>
            <h3 class="text-sm font-semibold text-[#182A3C] mb-3">Laporan Kelas Terbaru</h3>
            <div class="space-y-3 max-h-56 overflow-y-auto">
                @foreach ($classReports as $report)
                    <div class="flex items-start gap-3 px-2 py-1 rounded bg-white border border-slate-100">
                        <div class="w-2 h-2 rounded-full bg-{{ $report->type === 'achievement' ? 'emerald-500' : 'rose-500' }} mt-0.5"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-[#182A3C] line-clamp-1">{{ $report->description }}</p>
                            <p class="text-xs text-[#64748B] line-clamp-1">{{ $report->occurred_on->format('d M Y') }} • {{ ucfirst($report->type) }}</p>
                        </div>
                        <span class="text-xs font-medium text-{{ $report->status === 'approved' ? 'emerald-500' : ($report->status === 'pending' ? 'amber-500' : 'rose-500') }}">{{ ucfirst($report->status) }}</span>
                    </div>
                @endforeach
                @if ($classReports->isEmpty())
                    <p class="text-xs text-[#9CA3AF]">Belum ada laporan untuk kelas ini.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Students List -->
    <div class="mt-6">
        <h3 class="text-sm font-semibold text-[#182A3C] mb-3">Siswa di Kelas Ini</h3>
        <div class="space-y-3 max-h-64 overflow-y-auto">
            @foreach ($classStudents as $s)
                <div class="flex items-center gap-3 px-2 py-1 rounded bg-white border border-slate-100">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#044A87] to-[#51C4C1] flex items-center justify-center text-white font-medium text-sm">
                        {{ strtoupper(substr($s->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-[#182A3C]">{{ $s->name }} (NIS: {{ $s->nis }})</p>
                        <p class="text-xs text-[#64748B]">Poin: {{ number_format($s->getCurrentPoints()) }} - {{ $s->point_zone['name'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@endsection