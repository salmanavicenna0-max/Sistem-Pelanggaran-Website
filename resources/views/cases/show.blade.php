@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[#182A3C]">{{ $case->title }}</h2>
            <p class="text-sm text-[#64748B]">Kasus pembinaan untuk siswa: <span class="font-bold text-[#182A3C]">{{ $case->student?->user?->name }}</span> (NIS: {{ $case->student?->nis }})</p>
        </div>
        <a href="{{ route('cases.index') }}" class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-[#64748B] hover:bg-slate-100 transition-colors">
            Kembali
        </a>
    </div>

    {{-- Case Details Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 md:p-8 space-y-6">
        <div class="flex flex-wrap items-center justify-between pb-6 border-b border-slate-100 gap-4">
            <div>
                <span class="text-xs text-[#64748B] uppercase tracking-wider font-semibold">Status Kasus</span>
                <div class="mt-1 flex items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                        @if($case->status === 'open') bg-blue-50 text-[#044A87] border border-blue-200
                        @elseif($case->status === 'in_progress') bg-amber-50 text-amber-700 border border-amber-200
                        @elseif($case->status === 'closed') bg-emerald-50 text-emerald-700 border border-emerald-200
                        @else bg-slate-100 text-slate-600 @endif">
                        {{ $case->status === 'in_progress' ? 'In Progress (Pembinaan)' : ($case->status === 'closed' ? 'Closed (Selesai)' : 'Open (Kasus Baru)') }}
                    </span>
                    @if($case->closed_at)
                        <span class="text-xs text-slate-400">Ditutup: {{ $case->closed_at->format('d M Y, H:i') }}</span>
                    @endif
                </div>
            </div>

            <div class="text-right">
                <span class="text-xs text-[#64748B] uppercase tracking-wider font-semibold">Kerahasiaan</span>
                <div class="mt-1">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider
                        {{ $case->confidentiality === 'confidential' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                        {{ $case->confidentiality === 'confidential' ? 'Rahasia / Khusus BK' : 'Standar / Internal' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Asal Laporan if any --}}
        @if($case->report)
            <div class="p-4 rounded-xl bg-blue-50/60 border border-blue-200 flex flex-wrap items-center justify-between gap-3 text-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-[#044A87]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <div>
                        <p class="font-bold text-[#182A3C]">Kasus dari Laporan #{{ $case->report->id }} ({{ $case->report->occurred_on?->format('d M Y') }})</p>
                        <p class="text-xs text-[#64748B]">{{ $case->report->violationRule?->name ?? 'Pelanggaran' }} ({{ -$case->report->violationRule?->points }} Poin)</p>
                    </div>
                </div>
                <a href="{{ route('reports.show', $case->report) }}" class="text-xs font-semibold text-[#044A87] hover:underline">
                    Buka Laporan Asal →
                </a>
            </div>
        @endif

        <div>
            <p class="text-xs text-[#64748B] font-semibold uppercase mb-1">Latar Belakang & Deskripsi</p>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-sm text-[#182A3C] leading-relaxed whitespace-pre-line">
                {{ $case->description }}
            </div>
        </div>

        {{-- Timeline / Actions --}}
        <div>
            <h3 class="font-bold text-[#182A3C] text-base mb-4">Catatan & Log Tindakan Pembinaan ({{ $case->actions->count() }})</h3>
            <div class="space-y-4">
                @forelse($case->actions as $action)
                    <div class="p-4 rounded-xl border border-slate-200 bg-white">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-bold text-sm text-[#044A87]">{{ $action->action }}</span>
                            <span class="text-xs text-slate-400">{{ $action->performed_at?->format('d M Y, H:i') ?? '-' }}</span>
                        </div>
                        @if($action->note)
                            <p class="text-xs text-slate-600 mt-1">{{ $action->note }}</p>
                        @endif
                        <p class="text-[11px] text-slate-400 mt-2">Oleh: {{ $action->performer?->name ?? 'Petugas' }}</p>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">Belum ada log tindakan yang dicatat.</p>
                @endforelse
            </div>
        </div>

        {{-- Add Action Form --}}
        <div class="pt-6 border-t border-slate-200">
            <h4 class="font-bold text-sm text-[#182A3C] mb-3">Tambahkan Catatan Tindakan Baru</h4>
            <form method="POST" action="{{ route('cases.action', $case) }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="action" class="block text-xs font-semibold text-[#182A3C] mb-1">Jenis Tindakan / Langkah</label>
                        <input id="action" name="action" type="text" required placeholder="Contoh: Pemanggilan Orang Tua ke Sekolah"
                            class="w-full px-3.5 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] text-sm">
                    </div>
                    <div>
                        <label for="status" class="block text-xs font-semibold text-[#182A3C] mb-1">Perbarui Status Kasus</label>
                        <select id="status" name="status"
                            class="w-full px-3.5 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] text-sm bg-white">
                            <option value="open" {{ $case->status === 'open' ? 'selected' : '' }}>Open (Kasus Baru / Buka Kembali)</option>
                            <option value="in_progress" {{ $case->status === 'in_progress' ? 'selected' : '' }}>In Progress (Dalam Proses Pembinaan)</option>
                            <option value="closed" {{ $case->status === 'closed' ? 'selected' : '' }}>Closed (Pembinaan Selesai / Ditutup)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="note" class="block text-xs font-semibold text-[#182A3C] mb-1">Catatan Hasil Konseling / Kesepakatan</label>
                    <textarea id="note" name="note" rows="3" placeholder="Catatan hasil pertemuan atau bimbingan konseling..."
                        class="w-full px-3.5 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] text-sm resize-none"></textarea>
                </div>

                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-[#044A87] hover:bg-[#0360A4] text-white font-semibold text-sm transition-colors shadow-sm">
                    Simpan Tindakan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
