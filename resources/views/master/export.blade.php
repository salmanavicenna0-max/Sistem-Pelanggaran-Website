@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[#182A3C]">Cetak Rekap & Laporan</h2>
            <p class="text-sm text-[#64748B]">Ekspor dan cetak berkas laporan kedisiplinan dan prestasi resmi sekolah</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 md:p-8">
        <h3 class="text-base font-bold text-[#182A3C] mb-2">Pilih Format & Jenis Dokumen</h3>
        <p class="text-sm text-[#64748B] mb-6">Pilih format laporan yang Anda butuhkan untuk dicetak atau diunduh ke format spreadsheet/dokumen.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <a href="{{ route('master.export.pdf') }}" target="_blank" class="group bg-slate-50 hover:bg-[#044A87] rounded-2xl border border-slate-200 p-6 transition-all duration-200 hover:shadow-md flex flex-col justify-between">
                <div class="w-12 h-12 rounded-xl bg-[#044A87]/10 text-[#044A87] group-hover:bg-white/20 group-hover:text-white flex items-center justify-center transition-colors mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                </div>
                <div>
                    <p class="font-bold text-[#182A3C] group-hover:text-white text-base">Cetak Dokumen / PDF</p>
                    <p class="text-xs text-[#64748B] group-hover:text-white/80 mt-1">Cetak rekap dengan kop surat resmi SMAN 6 Bandung</p>
                </div>
            </a>

            <a href="{{ route('master.export.excel') }}" class="group bg-slate-50 hover:bg-[#044A87] rounded-2xl border border-slate-200 p-6 transition-all duration-200 hover:shadow-md flex flex-col justify-between">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 group-hover:bg-white/20 group-hover:text-white flex items-center justify-center transition-colors mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <p class="font-bold text-[#182A3C] group-hover:text-white text-base">Ekspor Excel (.xlsx)</p>
                    </div>
                    <p class="text-xs text-[#64748B] group-hover:text-white/80 mt-1">Ekspor seluruh mutasi poin dan pelanggaran ke file Excel</p>
                </div>
            </a>

            <a href="{{ route('points.index') }}" class="group bg-slate-50 hover:bg-[#044A87] rounded-2xl border border-slate-200 p-6 transition-all duration-200 hover:shadow-md flex flex-col justify-between">
                <div class="w-12 h-12 rounded-xl bg-[#51C4C1]/20 text-[#0360A4] group-hover:bg-white/20 group-hover:text-white flex items-center justify-center transition-colors mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <p class="font-bold text-[#182A3C] group-hover:text-white text-base">Rekapitulasi Poin</p>
                    <p class="text-xs text-[#64748B] group-hover:text-white/80 mt-1">Lihat dan cetak tabel akumulasi poin per kelas</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection