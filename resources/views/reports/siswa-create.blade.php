<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporkan Kejadian - {{ auth()->user()->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F5F9FA] text-[#182A3C] min-h-full antialiased font-sans">

@include('layouts.sidebar')

<main class="flex-1 overflow-y-auto p-6 md:p-8">
    <div class="max-w-2xl mx-auto">

        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-[#182A3C]">Laporkan Kejadian</h2>
                <div class="w-8 h-8 rounded-full bg-[#044A87] text-white flex items-center justify-center text-sm font-bold">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            </div>

            <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label for="student_nis" class="block text-sm font-medium text-[#182A3C] mb-1">NIS Siswa</label>
                    <input id="student_nis" name="student_nis" type="text" required autocomplete="username"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] focus:border-transparent text-sm">
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-[#182A3C] mb-1">Jenis Kejadian</label>
                    <select id="type" name="type" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] focus:border-transparent text-sm">
                        <option value="violation">Pelanggaran</option>
                        <option value="achievement">Prestasi</option>
                    </select>
                </div>

                @if($request->type === 'violation')
                    <div>
                        <label for="violation_rule_id" class="block text-sm font-medium text-[#182A3C] mb-1">Aturan Pelanggaran</label>
                        <select id="violation_rule_id" name="violation_rule_id" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] focus:border-transparent text-sm">
                            @foreach(\App\Models\ViolationRule::where('is_active', true)->get() as $rule)
                                <option value="{{ $rule->id }}">{{ $rule->code }} - {{ $rule->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @elseif($request->type === 'achievement')
                    <div>
                        <label for="achievement_rule_id" class="block text-sm font-medium text-[#182A3C] mb-1">Aturan Prestasi</label>
                        <select id="achievement_rule_id" name="achievement_rule_id" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] focus:border-transparent text-sm">
                            @foreach(\App\Models\AchievementRule::where('is_active', true)->get() as $rule)
                                <option value="{{ $rule->id }}">{{ $rule->code }} - {{ $rule->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label for="occurred_on" class="block text-sm font-medium text-[#182A3C] mb-1">Tanggal Kejadian</label>
                    <input id="occurred_on" name="occurred_on" type="date" required
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] focus:border-transparent text-sm">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-[#182A3C] mb-1">Deskripsi</label>
                    <textarea id="description" name="description" rows="3" required
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] focus:border-transparent text-sm resize-none"></textarea>
                </div>

                <div>
                    <label for="photo" class="block text-sm font-medium text-[#182A3C] mb-1">Foto Bukti (Opsional)</label>
                    <input id="photo" name="photo" type="file" accept="image/*"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] focus:border-transparent text-sm">
                    <p class="text-xs text-[#64748B] mt-1">Format: JPG, PNG, Maksimal 2MB</p>
                </div>

                <div>
                    <button type="submit"
                        class="w-full py-2.5 px-4 rounded-lg bg-[#044A87] hover:bg-[#0360A4] text-white font-medium text-sm transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#044A87]">
                        Kirim Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

</body>
</html>