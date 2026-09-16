@extends('layouts.app')

@section('content')

<!-- SISWA Dashboard -->
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-[#182A3C]">Buku Saku & Poin Saya</h2>
            <div class="w-8 h-8 rounded-full bg-[#044A87] text-white flex items-center justify-center text-sm font-bold">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
        </div>

        <!-- Current Points Card -->
        <div class="bg-[#F5F9FA] rounded-xl p-5 border-l-4 border-[#044A87] mb-4">
            <div class="text-sm text-[#64748B] mb-1">Saldo Poin Saat Ini</div>
            <div class="text-3xl font-extrabold text-[#044A87]">{{ number_format($currentPoints) }} poin</div>
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
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-4 mb-6">
        <a href="{{ route('reports.create') }}" class="group bg-white rounded-xl shadow-sm border border-slate-200 hover:bg-emerald-50 hover:text-emerald-700 transition-colors p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 4"></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-[#182A3C] group-hover:text-emerald-600">Laporkan Kejadian</p>
                <p class="text-xs text-[#64748B]">Tambah laporan pelanggaran/prestasi</p>
            </div>
        </a>

        <a href="#" class="group bg-white rounded-xl shadow-sm border border-slate-200 hover:bg-amber-50 hover:text-amber-600 transition-colors p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center group-hover:bg-amber-100 transition-colors">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-[#182A3C] group-hover:text-amber-500">Laporan Saya</p>
                <p class="text-xs text-[#64748B]">Riwayat laporan yang Anda buat</p>
            </div>
        </a>

        <a href="#" class="group bg-white rounded-xl shadow-sm border border-slate-200 hover:bg-blue-50 hover:text-blue-600 transition-colors p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2h2a2 2 0 002-2h2a2 2 0 002-2h4a2 2 0 002 2v6a2 2 0 002 2h7m-3-3h4m4-4h4m5-3v4m-5 3h4m-4-4h4"/></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-[#182A3C] group-hover:text-blue-500">Buku Saku</p>
                <p class="text-xs text-[#64748B]">Riwayat transaksi poin</p>
            </div>
        </a>

        <a href="#" class="group bg-white rounded-xl shadow-sm border border-slate-200 hover:bg-rose-50 hover:text-rose-600 transition-colors p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-rose-50 flex items-center justify-center group-hover:bg-rose-100 transition-colors">
                <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m2-2v2m2-2v2m-6-6h12a2 2 0 012 2v4a2 2 0 01-2 2h-12a2 2 0 01-2-2v-4a2 2 0 012-2z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-[#182A3C] group-hover:text-rose-500">Lupa Password</p>
                <p class="text-xs text-[#64748B]">Ubah password akun</p>
            </div>
        </a>
    </div>
</div>

@endsection