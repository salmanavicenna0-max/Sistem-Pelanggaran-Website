@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[#182A3C]">Rekap Poin Perilaku Siswa</h2>
            <p class="text-sm text-[#64748B]">Monitoring saldo poin, zona disiplin, dan riwayat sanksi siswa SMAN 6 Bandung</p>
        </div>
        <a href="{{ route('reports.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#044A87] hover:bg-[#0360A4] text-white font-semibold text-sm transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Catat Pelanggaran / Prestasi
        </a>
    </div>

    {{-- Filter / Summary Cards --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
            <h3 class="font-bold text-[#182A3C]">Daftar Seluruh Siswa ({{ $students->count() }})</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-[#64748B] uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">#</th>
                        <th class="py-3.5 px-4">Nama Siswa</th>
                        <th class="py-3.5 px-4">NIS</th>
                        <th class="py-3.5 px-4">Kelas</th>
                        <th class="py-3.5 px-4 text-center">Saldo Poin</th>
                        <th class="py-3.5 px-4 text-center">Zona Disiplin</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($students as $index => $student)
                    @php
                        $points = $student->getCurrentPoints();
                        $zone = $student->point_zone;
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-4 font-medium text-[#64748B]">{{ $index + 1 }}</td>
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-[#044A87]/10 text-[#044A87] flex items-center justify-center font-bold text-sm">
                                    {{ strtoupper(substr($student->user->name, 0, 1)) }}
                                </div>
                                <span class="font-bold text-[#182A3C]">{{ $student->user->name }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-[#64748B] font-mono text-xs">{{ $student->nis }}</td>
                        <td class="py-4 px-4 text-[#182A3C] font-medium">{{ $student->schoolClass?->name ?? '-' }}</td>
                        <td class="py-4 px-4 text-center">
                            <span class="text-base font-extrabold {{ $points < 50 ? 'text-rose-600' : ($points < 75 ? 'text-amber-600' : 'text-[#044A87]') }}">
                                {{ $points }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-center whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider"
                                style="background-color: {{ $zone['color'] ?? '#10B981' }}20; color: {{ $zone['color'] ?? '#10B981' }}; border: 1px solid {{ $zone['color'] ?? '#10B981' }}40;">
                                {{ $zone['name'] ?? 'Zona Hijau' }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-center whitespace-nowrap">
                            <a href="{{ route('points.riwayat', $student->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-[#044A87] bg-[#044A87]/10 hover:bg-[#044A87]/20 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Riwayat
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-[#64748B]">
                            <p class="font-medium">Belum ada data siswa.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
