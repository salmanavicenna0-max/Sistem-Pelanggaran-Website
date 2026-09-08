<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Poin - {{ $student->user->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F5F9FA] text-[#182A3C] min-h-full antialiased font-sans">

@include('layouts.sidebar')

<main class="flex-1 overflow-y-auto p-6 md:p-8">
    <div class="max-w-3xl mx-auto">

        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-[#182A3C]">Riwayat Poin Siswa</h2>
                <div class="w-8 h-8 rounded-full bg-[#044A87] text-white flex items-center justify-center text-sm font-bold">
                    {{ substr($student->user->name, 0, 1) }}
                </div>
                <p class="text-sm text-[#64748B]">NIS: {{ $student->nis }}</p>
            </div>

            <p class="text-sm text-[#64748B] mb-4">Riwayat lengkap transaksi poin resmi.</p>

            <table class="min-w-full bg-white rounded-xl overflow-hidden">
                <thead class="bg-[#F5F9FA] text-left text-sm text-[#64748B]">
                    <tr>
                        <th class="p-4">#</th>
                        <th class="p-4">Tipe</th>
                        <th class="p-4">Keterangan</th>
                        <th class="p-4">Poin</th>
                        <th class="p-4">Saldo Sebelum</th>
                        <th class="p-4">Saldo Sesudah</th>
                        <th class="p-4">Tanggal</th>
                        <th class="p-4">Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $index => $tx)
                    <tr class="border-b border-slate-100 hover:bg-[#F5F9FA]">
                        <td class="p-4 font-medium">{{ $index + 1 }}</td>
                        <td class="p-4">
                            <span class="px-2 py-1 rounded text-[10px] font-bold uppercase 
                                @if($tx->type === 'achievement') bg-emerald-50 text-emerald-400
                                @elseif($tx->type === 'violation') bg-rose-50 text-rose-400
                                @elseif($tx->type === 'initial_balance') bg-[#044A87] text-white
                                @else bg-amber-50 text-amber-600 @endif">
                                {{ ucfirst($tx->type) }}
                            </span>
                        </td>
                        <td class="p-4">{{ $tx->description }}</td>
                        <td class="p-4">
                            <span class="font-{{ $tx->points > 0 ? 'extrabold' : 'normal' }} text-{{ $tx->points > 0 ? 'emerald-600' : ($tx->points < 0 ? 'rose-500' : 'gray-600') }}">
                                {{ $tx->points > 0 ? '+' : '' }}{{ $tx->points }}
                            </span>
                        </td>
                        <td class="p-4 font-medium text-[#182A3C]">{{ number_format($tx->balance_before) }}</td>
                        <td class="p-4 font-medium text-[#182A3C]">{{ number_format($tx->balance_after) }}</td>
                        <td class="p-4">{{ $tx->transacted_at->diffForHumans() }}</td>
                        <td class="p-4">
                            <p class="text-xs text-[#64748B]">{{ $tx->performer?->name ?? 'Kesiswaan BK' }}</p>
                        </td>
                    </tr>
                    @endforeach
                    @if($transactions->isEmpty())
                    <tr>
                        <td class="p-4" colspan="8" class="text-center text-[#9CA3FN]">Belum ada riwayat transaksi poin.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</main>

</body>
</html>