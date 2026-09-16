@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[#182A3C]">Detail Laporan Kejadian</h2>
            <p class="text-sm text-[#64748B]">Informasi rincian laporan, status verifikasi, dan tindakan yang diambil.</p>
        </div>
        <a href="{{ auth()->user()->role === 'kesiswaan_bk' ? route('reports.index') : route('dashboard') }}" 
            class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-[#64748B] hover:bg-slate-100 transition-colors">
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 md:p-8 space-y-6">
        {{-- Status Header --}}
        <div class="flex flex-wrap items-center justify-between pb-6 border-b border-slate-100 gap-4">
            <div>
                <span class="text-xs text-[#64748B] uppercase tracking-wider font-semibold">Status Laporan</span>
                <div class="mt-1">
                    @if($report->status === 'approved')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700 border border-emerald-300">
                            Disetujui (Poin Diterapkan)
                        </span>
                    @elseif($report->status === 'rejected')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-100 text-rose-700 border border-rose-300">
                            Ditolak
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-100 text-amber-700 border border-amber-300">
                            Menunggu Verifikasi BK
                        </span>
                    @endif
                </div>
            </div>

            <div class="text-right">
                <span class="text-xs text-[#64748B] uppercase tracking-wider font-semibold">Kategori</span>
                <div class="mt-1">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                        {{ $report->type === 'violation' ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'bg-emerald-50 text-emerald-600 border border-emerald-200' }}">
                        {{ $report->type === 'violation' ? 'Pelanggaran' : 'Prestasi' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Detail Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                <p class="text-xs text-[#64748B] font-semibold uppercase">Siswa Terkait</p>
                <p class="text-lg font-bold text-[#182A3C]">{{ $report->student?->user?->name ?? '-' }}</p>
                <p class="text-xs text-[#64748B]">NIS: {{ $report->student?->nis ?? '-' }} • Kelas: {{ $report->student?->schoolClass?->name ?? '-' }}</p>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                <p class="text-xs text-[#64748B] font-semibold uppercase">Waktu Kejadian</p>
                <p class="text-lg font-bold text-[#182A3C]">{{ $report->occurred_on?->format('d F Y') ?? '-' }}</p>
                <p class="text-xs text-[#64748B]">Dilaporkan: {{ $report->created_at?->diffForHumans() ?? '-' }}</p>
            </div>
        </div>

        {{-- Aturan / Bobot --}}
        <div class="p-4 rounded-xl border border-slate-200 bg-white">
            <p class="text-xs text-[#64748B] font-semibold uppercase mb-1">Aturan & Poin Terkait</p>
            @if($report->type === 'violation')
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-bold text-base text-rose-600">
                            [{{ $report->violationRule?->code ?? '-' }}] {{ $report->violationRule?->name ?? '-' }}
                        </p>
                        <p class="text-xs text-[#64748B] mt-0.5">{{ $report->violationRule?->description ?? '' }}</p>
                    </div>
                    <span class="px-3 py-1 bg-rose-100 text-rose-700 font-bold rounded-lg text-sm">
                        -{{ $report->violationRule?->points ?? 0 }} Poin
                    </span>
                </div>
            @else
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-bold text-base text-emerald-600">
                            [{{ $report->achievementRule?->code ?? '-' }}] {{ $report->achievementRule?->name ?? '-' }}
                        </p>
                        <p class="text-xs text-[#64748B] mt-0.5">{{ $report->achievementRule?->description ?? '' }}</p>
                    </div>
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 font-bold rounded-lg text-sm">
                        +{{ $report->achievementRule?->points ?? 0 }} Poin
                    </span>
                </div>
            @endif
        </div>

        {{-- Kronologi --}}
        <div>
            <p class="text-xs text-[#64748B] font-semibold uppercase mb-1.5">Kronologi / Deskripsi Kejadian</p>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-sm text-[#182A3C] leading-relaxed whitespace-pre-line">
                {{ $report->description }}
            </div>
        </div>

        {{-- Bukti Foto --}}
        @if($report->attachments && $report->attachments->isNotEmpty())
            <div>
                <p class="text-xs text-[#64748B] font-semibold uppercase mb-2">Lampiran Bukti Foto</p>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($report->attachments as $attach)
                        <div class="border border-slate-200 rounded-xl overflow-hidden shadow-xs">
                            <img src="{{ asset('storage/' . $attach->path) }}" 
                                 alt="{{ $attach->original_name }}"
                                 class="w-full h-44 object-cover">
                            <div class="p-2 bg-slate-50 text-center">
                                <a href="{{ asset('storage/' . $attach->path) }}" target="_blank" class="text-xs text-[#044A87] hover:underline font-medium">
                                    Lihat Ukuran Asli
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Verifikasi Note if any --}}
        @if($report->verified_by)
            <div class="p-4 rounded-xl bg-blue-50/50 border border-blue-200/80 text-sm">
                <p class="text-xs font-bold text-[#044A87] uppercase tracking-wider">Verifikasi oleh Petugas</p>
                <p class="font-semibold text-[#182A3C] mt-1">{{ $report->verifier?->name ?? 'Petugas BK' }} ({{ $report->verified_at?->format('d M Y H:i') }})</p>
                @if($report->verification_note)
                    <p class="text-sm text-[#64748B] mt-1">Catatan: {{ $report->verification_note }}</p>
                @endif
            </div>
        @endif

        {{-- Kasus Pembinaan Terkait / Eskalasi --}}
        @if($report->studentCase)
            <div class="p-5 rounded-2xl bg-indigo-50/70 border border-indigo-200 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#044A87] text-white flex items-center justify-center shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-[#044A87] uppercase tracking-wider">Kasus Pembinaan Terkait</span>
                        <p class="font-bold text-[#182A3C] text-sm">{{ $report->studentCase->title }}</p>
                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                            @if($report->studentCase->status === 'open') bg-blue-100 text-blue-800
                            @elseif($report->studentCase->status === 'in_progress') bg-amber-100 text-amber-800
                            @else bg-emerald-100 text-emerald-800 @endif">
                            Status: {{ str_replace('_', ' ', $report->studentCase->status) }}
                        </span>
                    </div>
                </div>
                <a href="{{ route('cases.show', $report->studentCase) }}" 
                   class="px-4 py-2 rounded-xl bg-[#044A87] hover:bg-[#0360A4] text-white text-xs font-semibold shadow-xs transition-colors">
                    Lihat Kasus Pembinaan →
                </a>
            </div>
        @elseif($report->type === 'violation' && auth()->user()->role === 'kesiswaan_bk')
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="font-bold text-sm text-[#182A3C]">Perlu Penanganan / Pembinaan Lanjutan?</p>
                    <p class="text-xs text-[#64748B]">Eskalasikan laporan pelanggaran ini menjadi kasus bimbingan konseling dan catat log tindakannya.</p>
                </div>
                <a href="{{ route('cases.create', ['report_id' => $report->id]) }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#044A87] hover:bg-[#0360A4] text-white text-xs font-semibold shadow-xs transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tindak Lanjuti Menjadi Kasus Pembinaan
                </a>
            </div>
        @endif

        {{-- Verification Action for BK if pending --}}
        @if($report->status === 'pending' && auth()->user()->role === 'kesiswaan_bk')
            <div class="pt-6 border-t border-slate-200 flex items-center justify-end gap-3">
                <form method="POST" action="{{ route('reports.verify', $report) }}" class="flex items-center gap-3">
                    @csrf 
                    @method('PUT')
                    <button type="submit" name="status" value="rejected" onclick="return confirm('Tolak laporan ini?')"
                        class="px-5 py-2.5 rounded-xl border border-rose-300 text-rose-600 hover:bg-rose-50 font-semibold text-sm transition-colors">
                        Tolak Laporan
                    </button>
                    <button type="submit" name="status" value="approved" onclick="return confirm('Setujui laporan ini? Poin siswa akan otomatis dihitung!')"
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm transition-colors shadow-sm">
                        Setujui & Perbarui Poin
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection