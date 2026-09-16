@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[#182A3C]">Buku Saku & Poin Saya</h2>
            <p class="text-sm text-[#64748B]">Pantau riwayat saldo poin perilaku dan apresiasi prestasi Anda</p>
        </div>
        <a href="{{ route('reports.create') }}" class="px-4 py-2.5 rounded-xl bg-[#044A87] hover:bg-[#0360A4] text-white font-semibold text-sm transition-colors shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Laporkan Kejadian
        </a>
    </div>

    {{-- Point Summary Card --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="md:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-[#64748B] uppercase tracking-wider">Saldo Poin Perilaku</p>
                    <div class="flex items-baseline gap-2 mt-2">
                        <span class="text-5xl font-extrabold text-[#044A87] tracking-tight">{{ number_format($currentPoints) }}</span>
                        <span class="text-sm font-semibold text-[#64748B]">/ 100 Poin Awal</span>
                    </div>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-[#044A87]/10 flex items-center justify-center text-[#044A87]">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
            </div>

            <div class="mt-6 pt-5 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs text-[#64748B]">Status Zona Disiplin:</span>
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider"
                    style="background-color: {{ $zone['color'] ?? '#10B981' }}20; color: {{ $zone['color'] ?? '#10B981' }}; border: 1px solid {{ $zone['color'] ?? '#10B981' }}40;">
                    {{ $zone['name'] ?? 'Zona Hijau (Aman)' }}
                </span>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 flex flex-col justify-between">
            <div>
                <p class="text-xs font-bold text-[#64748B] uppercase tracking-wider">Identitas Siswa</p>
                <div class="mt-3">
                    <p class="font-bold text-[#182A3C] text-base">{{ $student->user->name }}</p>
                    <p class="text-xs text-[#64748B] mt-0.5">NIS: {{ $student->nis }}</p>
                    <p class="text-xs text-[#64748B]">Kelas: {{ $student->schoolClass?->name ?? 'Belum ada kelas' }}</p>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 text-xs text-[#64748B]">
                Tahun Ajaran: {{ $student->schoolClass?->schoolYear?->name ?? '2026/2027' }}
            </div>
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-[#182A3C]">Riwayat Transaksi Poin Terbaru</h3>
            <span class="text-xs text-[#64748B]">10 transaksi terakhir</span>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($recentTransactions as $tx)
                <div class="p-4 sm:px-6 flex items-center justify-between hover:bg-slate-50 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold flex-shrink-0
                            {{ $tx->type === 'achievement' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600' }}">
                            @if($tx->type === 'achievement')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-bold text-[#182A3C]">{{ $tx->description }}</p>
                            <p class="text-xs text-[#64748B] mt-0.5">
                                {{ $tx->transacted_at?->format('d M Y, H:i') ?? '-' }} 
                                • Saldo: {{ $tx->balance_before }} → <span class="font-semibold">{{ $tx->balance_after }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-extrabold {{ $tx->type === 'achievement' ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $tx->type === 'achievement' ? '+' : '' }}{{ $tx->points }} Poin
                        </span>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-[#64748B]">
                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="font-medium text-sm">Belum ada catatan pelanggaran maupun prestasi.</p>
                    <p class="text-xs text-slate-400 mt-1">Pertahankan sikap dan disiplin yang baik!</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection