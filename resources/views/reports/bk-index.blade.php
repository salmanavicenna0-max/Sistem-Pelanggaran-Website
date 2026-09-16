@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[#182A3C]">Antrian Verifikasi Laporan</h2>
            <p class="text-sm text-[#64748B]">Daftar laporan kejadian dari guru atau siswa yang menunggu peninjauan dan persetujuan BK.</p>
        </div>
        <a href="{{ route('reports.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#044A87] hover:bg-[#0360A4] text-white font-semibold text-sm transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Laporan Baru
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-[#182A3C]">Laporan Menunggu Verifikasi ({{ $reports->count() }})</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-[#64748B] uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">#</th>
                        <th class="py-3.5 px-4">Siswa</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4">Aturan Terkait</th>
                        <th class="py-3.5 px-4">Tanggal</th>
                        <th class="py-3.5 px-4">Kronologi</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($reports as $index => $report)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-4 font-medium text-[#64748B]">{{ $index + 1 }}</td>
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-[#044A87] to-[#51C4C1] flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                    {{ strtoupper(substr($report->student?->user?->name ?? 'S', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-[#182A3C]">{{ $report->student?->user?->name ?? 'Siswa Tidak Ditemukan' }}</p>
                                    <p class="text-xs text-[#64748B]">NIS: {{ $report->student?->nis ?? '-' }} • {{ $report->student?->schoolClass?->name ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider 
                                @if($report->type === 'violation') bg-rose-50 text-rose-600 border border-rose-200
                                @else bg-emerald-50 text-emerald-600 border border-emerald-200 @endif">
                                {{ $report->type === 'violation' ? 'Pelanggaran' : 'Prestasi' }}
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            @if($report->type === 'violation')
                                <span class="font-medium text-rose-600 text-xs">
                                    {{ $report->violationRule?->name ?? '-' }}
                                    @if($report->violationRule) (-{{ $report->violationRule->points }} poin) @endif
                                </span>
                            @else
                                <span class="font-medium text-emerald-600 text-xs">
                                    {{ $report->achievementRule?->name ?? '-' }}
                                    @if($report->achievementRule) (+{{ $report->achievementRule->points }} poin) @endif
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap text-xs text-[#64748B]">
                            {{ $report->occurred_on?->format('d M Y') ?? '-' }}
                        </td>
                        <td class="py-4 px-4 max-w-xs">
                            <p class="text-xs text-[#182A3C] line-clamp-2">{{ \Illuminate\Support\Str::limit($report->description, 100) }}</p>
                        </td>
                        <td class="py-4 px-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                <form method="POST" action="{{ route('reports.verify', $report) }}" class="inline">
                                    @csrf 
                                    @method('PUT')
                                    <button type="submit" name="status" value="approved" onclick="return confirm('Setujui laporan ini? Poin siswa akan otomatis diupdate.')"
                                        class="px-2.5 py-1 rounded-lg text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-300 hover:bg-emerald-100 transition-colors">
                                        Setujui
                                    </button>
                                    <button type="submit" name="status" value="rejected" onclick="return confirm('Tolak laporan ini?')"
                                        class="px-2.5 py-1 rounded-lg text-xs font-semibold text-rose-700 bg-rose-50 border border-rose-300 hover:bg-rose-100 transition-colors">
                                        Tolak
                                    </button>
                                </form>
                                <a href="{{ route('reports.show', $report) }}" class="px-2 py-1 rounded text-xs text-[#044A87] hover:underline">
                                    Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-[#64748B]">
                            <svg class="w-12 h-12 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="font-medium">Belum ada laporan yang menunggu verifikasi.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection