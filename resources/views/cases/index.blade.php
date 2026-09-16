@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[#182A3C]">Kasus Pembinaan Siswa (BK & Wali Kelas)</h2>
            <p class="text-sm text-[#64748B]">Monitoring tindak lanjut bimbingan konseling, panggilan orang tua, dan surat peringatan</p>
        </div>
        <a href="{{ route('cases.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#044A87] hover:bg-[#0360A4] text-white font-semibold text-sm transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buka Kasus Baru
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-[#182A3C]">Daftar Kasus Pembinaan ({{ $cases->count() }})</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-[#64748B] uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">#</th>
                        <th class="py-3.5 px-4">Siswa</th>
                        <th class="py-3.5 px-4">Judul Kasus</th>
                        <th class="py-3.5 px-4">Kerahasiaan</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4">Dibuat Oleh</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($cases as $index => $case)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-4 font-medium text-[#64748B]">{{ $index + 1 }}</td>
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-[#044A87]/10 text-[#044A87] flex items-center justify-center font-bold text-sm">
                                    {{ strtoupper(substr($case->student?->user?->name ?? 'S', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-[#182A3C]">{{ $case->student?->user?->name ?? '-' }}</p>
                                    <p class="text-xs text-[#64748B]">NIS: {{ $case->student?->nis }} • {{ $case->student?->schoolClass?->name ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-2">
                                <p class="font-bold text-[#182A3C]">{{ $case->title }}</p>
                                @if($case->report_id)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200 font-semibold whitespace-nowrap">Dari Laporan #{{ $case->report_id }}</span>
                                @endif
                            </div>
                            <p class="text-xs text-[#64748B] line-clamp-1 mt-0.5">{{ $case->description }}</p>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold uppercase tracking-wider
                                {{ $case->confidentiality === 'confidential' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                {{ $case->confidentiality === 'confidential' ? 'Rahasia' : 'Standar' }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-center whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                @if($case->status === 'open') bg-blue-50 text-[#044A87] border border-blue-200
                                @elseif($case->status === 'in_progress') bg-amber-50 text-amber-700 border border-amber-200
                                @elseif($case->status === 'closed') bg-emerald-50 text-emerald-700 border border-emerald-200
                                @else bg-slate-100 text-slate-600 @endif">
                                {{ str_replace('_', ' ', $case->status) }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-xs text-[#64748B] whitespace-nowrap">
                            {{ $case->creator?->name ?? 'BK' }}<br>
                            <span class="text-[10px] text-slate-400">{{ $case->created_at->diffForHumans() }}</span>
                        </td>
                        <td class="py-4 px-4 text-center whitespace-nowrap">
                            <a href="{{ route('cases.show', $case) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-[#044A87] bg-[#044A87]/10 hover:bg-[#044A87]/20 transition-colors">
                                Kelola & Tindak
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-[#64748B]">
                            <svg class="w-12 h-12 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <p class="font-medium">Tidak ada kasus pembinaan yang aktif saat ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
