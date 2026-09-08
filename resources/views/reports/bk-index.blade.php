<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrian Verifikasi Laporan - Kesiswaan & BK</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F5F9FA] text-[#182A3C] min-h-full antialiased font-sans">

@include('layouts.sidebar')

<main class="flex-1 overflow-y-auto p-6 md:p-8">
    <div class="max-w-5xl mx-auto">

        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-[#182A3C]">Antrian Verifikasi Laporan</h2>
                <div class="w-8 h-8 rounded-full bg-[#044A87] text-white flex items-center justify-center text-sm font-bold">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            </div>

            <p class="text-sm text-[#64748B] mb-4">Laporan yang menunggu verifikasi dari guru dan siswa.</p>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-xl overflow-hidden">
                    <thead class="bg-[#F5F9FA] text-left text-sm text-[#64748B]">
                        <tr>
                            <th class="p-4">#</th>
                            <th class="p-4">Siswa</th>
                            <th class="p-4">Jenis</th>
                            <th class="p-4">Tanggal</th>
                            <th class="p-4">Deskripsi</th>
                            <th class="p-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $index => $report)
                        <tr class="border-b border-slate-100 hover:bg-[#F5F9FA]">
                            <td class="p-4 font-medium">{{ $index + 1 }}</td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#044A87] to-[#51C4C1] flex items-center justify-center text-white font-medium text-sm">
                                        {{ strtoupper(substr($report->student->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-[#182A3C]">{{ $report->student->user->name }}</p>
                                        <p class="text-xs text-[#64748B]">NIS: {{ $report->student->nis }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase 
                                    @if($report->type === 'violation') bg-rose-50 text-rose-400
                                    @else bg-emerald-50 text-emerald-400 @endif">
                                    {{ ucfirst($report->type) }}
                                </span>
                            </td>
                            <td class="p-4">{{ $report->occurred_on->format('d M Y') }}</td>
                            <td class="p-4 line-clamp-2">
                                <p class="font-medium text-[#182A3C]">{{ str_limit($report->description, 80) }}</p>
                            </td>
                            <td class="p-4">
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('reports.verify', $report) }}" class="inline">
                                        @l @csrf @method('PUT')
                                        <button type="submit" name="status" value="approved"
                                            class="px-3 py-1 rounded text-[10px] font-bold uppercase text-emerald-600 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 transition-colors">
                                            Setujui
                                        </button>
                                        <button type="submit" name="status" value="rejected"
                                            class="px-3 py-1 rounded text-[10px] font-bold uppercase text-rose-500 bg-rose-50 border border-rose-200 hover:bg-rose-100 transition-colors">
                                            Tolak
                                        </button>
                                        <input type="hidden" name="verification_note">
                                    </form>
                                    <a href="{{ route('reports.show', $report) }}" class="text-[10px] text-[#044A87] hover:text-[#0360A4] transition-colors">Lihat Detail</a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @if($reports->isEmpty())
                        <tr>
                            <td class="p-4" colspan="6" class="text-center text-[#9CA3AF]">Belum ada laporan menunggu verifikasi.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

</body>
</html>