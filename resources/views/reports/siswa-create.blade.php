@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[#182A3C]">Formulir Pelaporan Kejadian</h2>
            <p class="text-sm text-[#64748B]">Laporkan catatan pelanggaran disiplin atau prestasi penghargaan siswa</p>
        </div>
        <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-[#64748B] hover:bg-slate-100 transition-colors">
            Kembali
        </a>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 md:p-8">
        <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Target Siswa --}}
            <div>
                <label for="student_nis" class="block text-sm font-semibold text-[#182A3C] mb-1.5">
                    Siswa Terkait <span class="text-rose-500">*</span>
                </label>
                @if(auth()->user()->role === 'siswa' && auth()->user()->student)
                    <input type="hidden" name="student_nis" value="{{ auth()->user()->student->nis }}">
                    <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-[#182A3C]">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-[#64748B]">NIS: {{ auth()->user()->student->nis }} • Kelas: {{ auth()->user()->student->schoolClass?->name ?? '-' }}</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 bg-purple-100 text-purple-700 rounded-full">Diri Sendiri</span>
                    </div>
                @else
                    <div class="relative">
                        <select id="student_nis" name="student_nis" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] focus:border-transparent text-sm bg-white">
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->nis }}" {{ old('student_nis') === $student->nis ? 'selected' : '' }}>
                                    {{ $student->nis }} - {{ $student->user->name }} ({{ $student->schoolClass?->name ?? 'Tanpa Kelas' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('student_nis')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                @endif
            </div>

            {{-- Jenis Laporan --}}
            <div>
                <label class="block text-sm font-semibold text-[#182A3C] mb-2">
                    Kategori Kejadian <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative flex items-center p-4 rounded-xl border-2 cursor-pointer transition-all border-[#044A87] bg-blue-50/50" id="label-violation">
                        <input type="radio" name="type" value="violation" checked class="sr-only" onchange="toggleType('violation')">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center font-bold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-[#182A3C]">Pelanggaran</p>
                                <p class="text-xs text-[#64748B]">Mengurangi poin perilaku</p>
                            </div>
                        </div>
                    </label>

                    <label class="relative flex items-center p-4 rounded-xl border-2 border-slate-200 cursor-pointer transition-all hover:bg-slate-50" id="label-achievement">
                        <input type="radio" name="type" value="achievement" class="sr-only" onchange="toggleType('achievement')">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-[#182A3C]">Prestasi & Kebaikan</p>
                                <p class="text-xs text-[#64748B]">Menambah poin penghargaan</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Aturan Pelanggaran --}}
            <div id="wrapper-violation">
                <label for="violation_rule_id" class="block text-sm font-semibold text-[#182A3C] mb-1.5">
                    Pilih Aturan Pelanggaran <span class="text-rose-500">*</span>
                </label>
                <select id="violation_rule_id" name="violation_rule_id"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] focus:border-transparent text-sm bg-white">
                    <option value="">-- Pilih Jenis Pelanggaran --</option>
                    @foreach($violationRules as $rule)
                        <option value="{{ $rule->id }}" {{ old('violation_rule_id') == $rule->id ? 'selected' : '' }}>
                            [{{ $rule->code }}] {{ $rule->name }} (-{{ $rule->points }} Poin)
                        </option>
                    @endforeach
                </select>
                @error('violation_rule_id')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Aturan Prestasi --}}
            <div id="wrapper-achievement" class="hidden">
                <label for="achievement_rule_id" class="block text-sm font-semibold text-[#182A3C] mb-1.5">
                    Pilih Jenis Prestasi <span class="text-rose-500">*</span>
                </label>
                <select id="achievement_rule_id" name="achievement_rule_id"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] focus:border-transparent text-sm bg-white">
                    <option value="">-- Pilih Jenis Prestasi --</option>
                    @foreach($achievementRules as $rule)
                        <option value="{{ $rule->id }}" {{ old('achievement_rule_id') == $rule->id ? 'selected' : '' }}>
                            [{{ $rule->code }}] {{ $rule->name }} (+{{ $rule->points }} Poin)
                        </option>
                    @endforeach
                </select>
                @error('achievement_rule_id')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tanggal Kejadian --}}
            <div>
                <label for="occurred_on" class="block text-sm font-semibold text-[#182A3C] mb-1.5">
                    Tanggal Kejadian <span class="text-rose-500">*</span>
                </label>
                <input id="occurred_on" name="occurred_on" type="date" required value="{{ old('occurred_on', date('Y-m-d')) }}"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] focus:border-transparent text-sm bg-white">
                @error('occurred_on')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi Kejadian --}}
            <div>
                <label for="description" class="block text-sm font-semibold text-[#182A3C] mb-1.5">
                    Kronologi / Keterangan Lengkap <span class="text-rose-500">*</span>
                </label>
                <textarea id="description" name="description" rows="4" required placeholder="Tuliskan secara jelas detail tempat, saksi, atau kronologi peristiwa..."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] focus:border-transparent text-sm resize-none">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Foto Bukti --}}
            <div>
                <label for="photo" class="block text-sm font-semibold text-[#182A3C] mb-1.5">
                    Foto Bukti Kejadian (Opsional)
                </label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-xl hover:border-[#044A87] transition-colors">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-[#64748B] justify-center">
                            <label for="photo" class="relative cursor-pointer bg-white rounded-md font-medium text-[#044A87] hover:text-[#0360A4] focus-within:outline-none">
                                <span>Unggah file foto</span>
                                <input id="photo" name="photo" type="file" accept="image/*" class="sr-only">
                            </label>
                            <p class="pl-1">atau seret ke sini</p>
                        </div>
                        <p class="text-xs text-slate-500">PNG, JPG, JPEG hingga 2MB</p>
                    </div>
                </div>
                @error('photo')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <div class="pt-4 border-t border-slate-200">
                <button type="submit"
                    class="w-full py-3 px-4 rounded-xl bg-[#044A87] hover:bg-[#0360A4] text-white font-semibold text-sm transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#044A87]">
                    Kirim Laporan untuk Diverifikasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleType(type) {
    const wrapViolation = document.getElementById('wrapper-violation');
    const wrapAchievement = document.getElementById('wrapper-achievement');
    const labelViolation = document.getElementById('label-violation');
    const labelAchievement = document.getElementById('label-achievement');
    const violationSelect = document.getElementById('violation_rule_id');
    const achievementSelect = document.getElementById('achievement_rule_id');

    if (type === 'violation') {
        wrapViolation.classList.remove('hidden');
        wrapAchievement.classList.add('hidden');
        violationSelect.setAttribute('required', 'required');
        achievementSelect.removeAttribute('required');

        labelViolation.classList.add('border-[#044A87]', 'bg-blue-50/50');
        labelViolation.classList.remove('border-slate-200');

        labelAchievement.classList.remove('border-[#044A87]', 'bg-blue-50/50');
        labelAchievement.classList.add('border-slate-200');
    } else {
        wrapViolation.classList.add('hidden');
        wrapAchievement.classList.remove('hidden');
        achievementSelect.setAttribute('required', 'required');
        violationSelect.removeAttribute('required');

        labelAchievement.classList.add('border-[#044A87]', 'bg-blue-50/50');
        labelAchievement.classList.remove('border-slate-200');

        labelViolation.classList.remove('border-[#044A87]', 'bg-blue-50/50');
        labelViolation.classList.add('border-slate-200');
    }
}
</script>
@endsection