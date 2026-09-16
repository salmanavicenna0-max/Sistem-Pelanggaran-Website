@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[#182A3C]">Riwayat Poin & Sanksi Siswa</h2>
            <p class="text-sm text-[#64748B]">Detail riwayat transaksi poin disiplin dan apresiasi resmi</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="document.getElementById('modal-achievement').classList.remove('hidden')" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition-colors shadow-sm shadow-emerald-200">
                + Prestasi Manual
            </button>
            <button onclick="document.getElementById('modal-correction').classList.remove('hidden')" class="px-4 py-2 rounded-lg bg-amber-500 text-white text-sm font-medium hover:bg-amber-600 transition-colors shadow-sm shadow-amber-200">
                ⚖️ Koreksi Saldo
            </button>
            <a href="{{ route('points.index') }}" class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-[#64748B] hover:bg-slate-100 transition-colors">
                Kembali
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="bg-emerald-50 text-emerald-700 p-4 rounded-xl text-sm font-medium border border-emerald-200">
            {{ session('status') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-rose-50 text-rose-700 p-4 rounded-xl text-sm font-medium border border-rose-200">
            {{ session('error') }}
        </div>
    @endif

    {{-- Student Summary Card --}}
    @php
        $points = $student->getCurrentPoints();
        $zone = $student->point_zone;
        $reversedIds = \App\Models\PointTransaction::where('student_id', $student->id)->where('type', 'reversal')->pluck('reference_id')->toArray();
    @endphp
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 flex flex-wrap items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-[#044A87]/10 text-[#044A87] flex items-center justify-center font-extrabold text-xl">
                {{ strtoupper(substr($student->user->name, 0, 1)) }}
            </div>
            <div>
                <h3 class="text-xl font-bold text-[#182A3C]">{{ $student->user->name }}</h3>
                <p class="text-sm text-[#64748B]">NIS: <span class="font-mono font-medium text-slate-700">{{ $student->nis }}</span> • Kelas: <span class="font-medium text-slate-700">{{ $student->schoolClass?->name ?? 'Tanpa Kelas' }}</span></p>
            </div>
        </div>

        <div class="flex items-center gap-6">
            <div class="text-right">
                <p class="text-xs font-semibold text-[#64748B] uppercase">Saldo Saat Ini</p>
                <p class="text-3xl font-extrabold text-[#044A87]">{{ $points }} <span class="text-sm font-normal text-slate-500">poin</span></p>
            </div>
            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider"
                style="background-color: {{ $zone['color'] ?? '#10B981' }}20; color: {{ $zone['color'] ?? '#10B981' }}; border: 1px solid {{ $zone['color'] ?? '#10B981' }}40;">
                {{ $zone['name'] ?? 'Zona Hijau' }}
            </span>
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-[#182A3C]">Daftar Mutasi Poin</h3>
            <span class="text-xs text-[#64748B]">Total: {{ $transactions->count() }} transaksi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-[#64748B] uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">#</th>
                        <th class="py-3.5 px-4">Tipe</th>
                        <th class="py-3.5 px-4">Keterangan</th>
                        <th class="py-3.5 px-4 text-center">Poin</th>
                        <th class="py-3.5 px-4 text-center">Sebelum</th>
                        <th class="py-3.5 px-4 text-center">Sesudah</th>
                        <th class="py-3.5 px-4">Waktu</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transactions as $index => $tx)
                    <tr class="hover:bg-slate-50/80 transition-colors {{ in_array($tx->id, $reversedIds) ? 'opacity-50' : '' }}">
                        <td class="py-3.5 px-4 font-medium text-slate-500">{{ $index + 1 }}</td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider
                                @if($tx->type === 'achievement') bg-emerald-50 text-emerald-600 border border-emerald-200
                                @elseif($tx->type === 'violation') bg-rose-50 text-rose-600 border border-rose-200
                                @elseif($tx->type === 'reversal') bg-slate-100 text-slate-600 border border-slate-300
                                @elseif($tx->type === 'correction') bg-amber-50 text-amber-600 border border-amber-200
                                @else bg-blue-50 text-[#044A87] border border-blue-200 @endif">
                                {{ ucfirst($tx->type) }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-[#182A3C] font-medium max-w-xs">{{ $tx->description }}</td>
                        <td class="py-3.5 px-4 text-center font-bold {{ $tx->points > 0 && $tx->type !== 'violation' ? 'text-emerald-600' : ($tx->points > 0 && $tx->type === 'violation' ? 'text-rose-600' : 'text-slate-600') }}">
                            {{ $tx->points }}
                        </td>
                        <td class="py-3.5 px-4 text-center text-slate-500">{{ number_format($tx->balance_before) }}</td>
                        <td class="py-3.5 px-4 text-center font-bold text-[#044A87]">{{ number_format($tx->balance_after) }}</td>
                        <td class="py-3.5 px-4 text-xs text-slate-500 whitespace-nowrap">
                            {{ $tx->transacted_at?->format('d M Y H:i') ?? '-' }}
                            <div class="text-[10px] text-slate-400 mt-0.5">{{ $tx->performer?->name ?? 'Sistem' }}</div>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if(!in_array($tx->id, $reversedIds) && $tx->type !== 'reversal')
                                <button onclick="openReversalModal({{ $tx->id }})" class="text-xs font-medium text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 px-2 py-1 rounded transition-colors">
                                    Batalkan
                                </button>
                            @elseif(in_array($tx->id, $reversedIds))
                                <span class="text-xs text-slate-400 font-medium">Dibatalkan</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
                            Belum ada riwayat mutasi poin untuk siswa ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal: Manual Achievement --}}
<div id="modal-achievement" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <form action="{{ route('points.manual-achievement') }}" method="POST">
            @csrf
            <input type="hidden" name="student_id" value="{{ $student->id }}">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-xl font-bold text-[#182A3C]">Tambah Prestasi Manual</h3>
                <p class="text-sm text-[#64748B]">Beri penghargaan poin di luar laporan aplikasi.</p>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-[#182A3C] mb-1.5">Aturan Prestasi (Opsional)</label>
                    <select name="achievement_rule_id" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-[#044A87] focus:ring-1 focus:ring-[#044A87] outline-none transition-all">
                        <option value="">Pilih aturan prestasi...</option>
                        @foreach($achievementRules ?? [] as $rule)
                            <option value="{{ $rule->id }}">{{ $rule->name }} (+{{ $rule->points }} poin)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#182A3C] mb-1.5">Jumlah Poin (+)</label>
                    <input type="number" name="points" required min="1" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-[#044A87] focus:ring-1 focus:ring-[#044A87] outline-none transition-all" placeholder="Contoh: 150">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#182A3C] mb-1.5">Keterangan / Alasan</label>
                    <textarea name="description" required rows="2" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-[#044A87] focus:ring-1 focus:ring-[#044A87] outline-none transition-all" placeholder="Detail prestasi..."></textarea>
                </div>
            </div>
            <div class="p-6 bg-slate-50 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-achievement').classList.add('hidden')" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-200 transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition-colors shadow-sm shadow-emerald-200">Simpan Prestasi</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Correction --}}
<div id="modal-correction" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <form action="{{ route('points.correction') }}" method="POST">
            @csrf
            <input type="hidden" name="student_id" value="{{ $student->id }}">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-xl font-bold text-[#182A3C]">Koreksi Saldo Poin</h3>
                <p class="text-sm text-[#64748B]">Sesuaikan saldo akhir poin (jika terjadi selisih sistem).</p>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-[#182A3C] mb-1.5">Saldo Saat Ini</label>
                    <input type="text" readonly value="{{ $points }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-500 font-bold">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#182A3C] mb-1.5">Saldo Target Baru</label>
                    <input type="number" name="target_balance" required class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none transition-all" placeholder="Masukkan saldo benar">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#182A3C] mb-1.5">Alasan Koreksi</label>
                    <textarea name="reason" required rows="2" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none transition-all" placeholder="Kenapa dikoreksi?"></textarea>
                </div>
            </div>
            <div class="p-6 bg-slate-50 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-correction').classList.add('hidden')" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-200 transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-amber-500 text-white text-sm font-medium hover:bg-amber-600 transition-colors shadow-sm shadow-amber-200">Simpan Koreksi</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Reversal --}}
<div id="modal-reversal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <form id="form-reversal" method="POST">
            @csrf
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-xl font-bold text-rose-600">Batalkan Transaksi</h3>
                <p class="text-sm text-slate-600">Aksi ini akan membuat transaksi balikan (reversal) yang mengembalikan poin seperti semula.</p>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-[#182A3C] mb-1.5">Alasan Pembatalan</label>
                    <textarea name="reason" required rows="2" class="w-full rounded-xl border border-rose-300 px-4 py-2 text-sm focus:border-rose-500 focus:ring-1 focus:ring-rose-500 outline-none transition-all" placeholder="Alasan pembatalan..."></textarea>
                </div>
            </div>
            <div class="p-6 bg-slate-50 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-reversal').classList.add('hidden')" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-200 transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-rose-600 text-white text-sm font-medium hover:bg-rose-700 transition-colors shadow-sm shadow-rose-200">Ya, Batalkan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openReversalModal(transactionId) {
        const form = document.getElementById('form-reversal');
        form.action = `/poin/${transactionId}/reversal`;
        document.getElementById('modal-reversal').classList.remove('hidden');
    }
</script>
@endsection