<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Saku & Poin Saya - {{ auth()->user()->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F5F9FA] text-[#182A3C] min-h-full antialiased font-sans">

@include('layouts.sidebar')

<main class="flex-1 overflow-y-auto p-6 md:p-8">
    <div class="max-w-2xl mx-auto">

        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-[#182A3C]">Buku Saku & Poin Saya</h2>
                <div class="w-8 h-8 rounded-full bg-[#044A87] text-white flex items-center justify-center text-sm font-bold">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            </div>

            <!-- Current Points -->
            <div class="bg-[#F5F9FA] rounded-xl p-5 border-l-4 border-[#044A87] mb-5">
                <div class="text-sm text-[#64748B] mb-1">Saldo Poin Saat Ini</div>
                <div class="text-3xl font-extrabold text-[#044A87]"> {{ number_format($currentPoints)} }} poin</div>
                <div class="text-sm mt-1">
                    <span class="text-[#64748B]">Zona: </span>
                    <span class="font-medium text-{{ str_replace([' ', '-'], '', $zone['color']) ?? 'gray' }}">{{ $zone['name'] }}</span>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div>
                <h3 class="text-sm font-semibold text-[#182A3C] mb-3">Riwayat Transaksi Terbaru</h3>
                <div class="space-y-2 max-h-40 overflow-y-auto">
                    @foreach ($recentTransactions as $tx)
                        <div class="flex items-center gap-3 px-2 py-1 rounded bg-white border border-slate-100">
                            <div class="w-2 h-2 rounded-full bg-{{ $tx->type === 'achievement' ? 'emerald-500' : 'rose-500' }}"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-[#182A3C] {{ $tx->type === 'achievement' ? 'text-emerald-600' : 'text-rose-600' }}">{{ $tx->description }}</p>
                                <p class="text-xs text-[#64748B]">+{{ $tx->points > 0 ? '+' : '' }}${{ $tx->points }} poin</p>
                            </div>
                            <p class="text-xs text-[#9CA3AF]">{{ $tx->transacted_at->diffForHumans() }}</p>
                        </div>
                    @endforeach
                    @if($recentTransactions->isEmpty())
                        <p class="text-xs text-[#9CA3AF] pt-2">Belum ada transaksi poin.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>

</body>
</html>