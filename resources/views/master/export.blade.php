<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap & Laporan - Kesiswaan & BK</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F5F9FA] text-[#182A3C] min-h-full antialiased font-sans">

@include('layouts.sidebar')

<main class="flex-1 overflow-y-auto p-6 md:p-8">
    <div class="max-w-6xl mx-auto">

        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-[#182A3C]">Cetak Rekap & Laporan</h2>
                <div class="w-8 h-8 rounded-full bg-[#044A87] text-white flex items-center justify-center text-sm font-bold">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            </div>

            <p class="text-sm text-[#64748B] mb-4">Pilih jenis laporan yang akan diekspor.</p>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <a href="#" class="group bg-white rounded-xl shadow-sm border border-slate-200 hover:bg-[#044A87] hover:text-white transition-colors p-6 flex items-center justify-center">
                    <div class="w-12 h-12 rounded-lg bg-[#044A87] flex items-center justify-center group-hover:bg-[#0360A4] transition-colors">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-[#182A3C] group-hover:text-white">Cetak Rekap Poin</p>
                        <p class="text-xs text-[#64748B]">Rekapitulasi poin siswa per kelas/tahun</p>
                    </div>
                </a>

                <a href="#" class="group bg-white rounded-xl shadow-sm border border-slate-200 hover:bg-[#044A87] hover:text-white transition-colors p-6 flex items-center justify-center">
                    <div class="w-12 h-12 rounded-lg bg-[#044A87] flex items-center justify-center group-hover:bg-[#0360A4] transition-colors">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v10c7-6 7-6 14 0v-10m-7 3h5"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-[#182A3C] group-hover:text-white">Laporan Excel</p>
                        <p class="text-xs text-[#64748B]">Ekspor data pelanggaran, prestasi, poin ke Excel</p>
                    </div>
                </a>

                <a href="#" class="group bg-white rounded-xl shadow-sm border border-slate-200 hover:bg-[#044A87] hover:text-white transition-colors p-6 flex items-center justify-center">
                    <div class="w-12 h-12 rounded-lg bg-[#044A87] flex items-center justify-center group-hover:bg-[#0360A4] transition-colors">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v10c7-6 7-6 14 0v-10m-7 3h5"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-[#182A3C] group-hover:text-white">PDF & Kop Surat</p>
                        <p class="text-xs text-[#64748B]">Laporan dengan kop surat resmi SMAN 6 Bandung</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</main>

</body>
</html>