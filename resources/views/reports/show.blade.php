<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan - {{ $report->student->user->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F5F9FA] text-[#182A3C] min-h-full antialiased font-sans">

@include('layouts.sidebar')

<main class="flex-1 overflow-y-auto p-6 md:p-8">
    <div class="max-w-3xl mx-auto">

        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-[#182A3C]">Detail Laporan</h2>
                <div class="w-8 h-8 rounded-full bg-[#044A87] text-white flex items-center justify-center text-sm font-bold">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm text-[#64748B]">Siswa</p>
                    <p class="text-2xl font-bold text-[#182A3C]">{{ $report->student->user->name }}</p>
                    <p class="text-xs text-[#64748B]">NIS: {{ $report->student->nis }}</p>
                </div>
                <div>
                    <p class="text-sm text-[#64748B]">Tanggal kejadian</p>
                    <p class="text-2xl font-bold text-[#182A3C]">{{ $report->occurred_on->format('d M Y') }}</p>
                </div>
            </div>

            <div>
                <p class="text-sm text-[#64748B]">Jenis</p>
                <p class="text-lg font-medium text-{{ $report->type === 'achievement' ? 'emerald-600' : 'rose-500' }}">
                    {{ ucfirst($report->type) }}
                </p>
            </div>

            @if($report->type === 'violation')
                <div>
                    <p class="text-sm text-[#64748B]">Aturan Pelanggaran</p>
                    <p class="text-lg font-medium text-[#D64545]">{{ $report->violationRule?->code }} - {{ $report->violationRule?->name }}</p>
                </div>
            @elseif($report->type === 'achievement')
                <div>
                    <p class="text-sm text-[#64748B]">Aturan Prestasi</p>
                    <p class="text-lg font-medium text-[#16A36A]">{{ $report->achievementRule?->code }} - {{ $report->achievementRule?->name }}</p>
                </div>
            @endif

            <p class="text-sm text-[#64748B] mt-2">Deskripsi</p>
            <p class="text-base leading-relaxed mt-1">{{ $report->description }}</p>

            <p class="text-sm text-[#64748B] mt-2">Status</p>
            <p class="text-lg font-bold text-{{ $report->status === 'approved' ? 'emerald-600' : ($report->status === 'rejected' ? 'rose-500' : 'amber-500') }}">
                {{ ucfirst($report->status) }}
            </p>

            @if($report->verified_by)
                <div class="mt-4">
                    <p class="text-sm text-[#64748B]">Verifikasi oleh</p>
                    <p class="text-base font-medium text-[#182A3C]">{{ $report->verifier?->name ?? 'Kesiswaan BK' }}</p>
                    <p class="text-xs text-[#64748B]">{{ $report->verified_at?->diffForHumans() }}</p>
                </div>
            @endif

            @if($report->verification_note)
                <div class="mt-4 p-3 rounded-lg bg-[#F5F9FA] border border-slate-200">
                    <p class="text-sm text-[#64748B]">Catatan Verifikasi</p>
                    <p class="font-medium text-[#182A3C]">{{ $report->verification_note }}</p>
                </div>
            @endif

            {{-- Foto Bukti --}}
            @if($report->attachments->isNotEmpty())
                <div class="mt-4">
                    <p class="text-sm text-[#64748B]">Foto Bukti</p>
                    <div class="grid grid-cols-2 gap-4 mt-3">
                        @foreach($report->attachments as $attach)
                            <a href="{{ storage_path('private/' . $attach->path) }}" target="_blank"
                                class="group block rounded-lg overflow-hidden border border-slate-200 hover:border-emerald-500">
                                <img src="{{ storage_path('private/' . $attach->path) }}"
                                    class="w-full h-40 object-cover transition-transform duration-300 group-hover:transform-scale-105"
                                    alt="Bukti laporan">
                            </a>
                            <p class="text-caption text-xs text-[#64748B] mt-1">{{ $attach->original_name }}</p>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</main>

</body>
</html>