<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa & Import - Kesiswaan & BK</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F5F9FA] text-[#182A3C] min-h-full antialiased font-sans">

@include('layouts.sidebar')

<main class="flex-1 overflow-y-auto p-6 md:p-8">
    <div class="max-w-6xl mx-auto">

        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-[#182A3C]">Data Siswa & Import</h2>
                <div class="w-8 h-8 rounded-full bg-[#044A87] text-white flex items-center justify-center text-sm font-bold">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <h3 class="text-sm font-semibold text-[#182A3C] mb-3">Tahun Ajaran</h3>
                    <select class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] focus:border-transparent text-sm">
                        @foreach($schoolYears as $sy)
                            <option value="{{ $sy->id }}">{{ $sy->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-[#182A3C] mb-3">Kelas</select>
                    <select class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] focus:border-transparent text-sm">
                        <option value="">-- Pilih Tahun Ajaran Terlebih Dahulu --</option>
                        @foreach($schoolClasses as $sc)
                            <option value="{{ $sc->id }}">{{ $sc->name }} (Wali: {{ $sc->homeroomTeacher?->name }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-[#182A3C] mb-3">Import Siswa dari CSV</h3>
                <p class="text-sm text-[#64748B] mb-3">Format CSV wajib memiliki kolom: NIS, NISN, Nama, Kelas, Gender</p>
                <form action="#" class="space-y-3">
                    <input type="file" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] focus:border-transparent text-sm">
                    <button type="submit"
                        class="w-full py-2.5 px-4 rounded-lg bg-[#044A87] hover:bg-[#0360A4] text-white font-medium text-sm transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#044A87]">
                        Upload CSV
                    </button>
                </form>
                <p class="text-xs text-[#64748B]">* Template unduh di bawah ini</p>
                <a href="#" class="text-[10px] text-[#044A87] hover:text-[#0360A4] transition-colors mt-2">Unduh Template CSV</a>
            </div>
        </div>
    </div>
</main>

</body>
</html>