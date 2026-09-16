@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[#182A3C]">Buka Kasus Pembinaan Baru</h2>
            <p class="text-sm text-[#64748B]">Inisiasi penanganan kasus kedisiplinan dan pembinaan siswa</p>
        </div>
        <a href="{{ route('cases.index') }}" class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-[#64748B] hover:bg-slate-100 transition-colors">
            Kembali
        </a>
    </div>

    @if(isset($report) && $report)
        <div class="p-4 rounded-xl bg-blue-50/70 border border-blue-200 flex items-start gap-3">
            <svg class="w-5 h-5 text-[#044A87] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="text-sm text-[#182A3C]">
                <span class="font-bold text-[#044A87]">Dibuat dari Laporan #{{ $report->id }}:</span>
                {{ $report->violationRule?->name ?? 'Pelanggaran' }} — Tanggal Kejadian: {{ $report->occurred_on?->format('d M Y') ?? '-' }}.
                <p class="text-xs text-[#64748B] mt-0.5">Data siswa dan ringkasan telah diisikan secara otomatis di bawah.</p>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 md:p-8">
        <form method="POST" action="{{ route('cases.store') }}" class="space-y-6">
            @csrf

            @if(isset($report) && $report)
                <input type="hidden" name="report_id" value="{{ $report->id }}">
            @endif

            <div>
                <label for="student_id" class="block text-sm font-semibold text-[#182A3C] mb-1.5">
                    Pilih Siswa <span class="text-rose-500">*</span>
                </label>
                @if(isset($report) && $report)
                    <input type="hidden" name="student_id" value="{{ $report->student_id }}">
                    <input type="text" disabled 
                        value="{{ $report->student?->nis }} - {{ $report->student?->user?->name }} ({{ $report->student?->schoolClass?->name ?? 'Tanpa Kelas' }})"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-100 text-slate-700 text-sm font-medium">
                @else
                    <select id="student_id" name="student_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] focus:border-transparent text-sm bg-white">
                        <option value="">-- Pilih Siswa yang Ditindak --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->nis }} - {{ $student->user->name }} ({{ $student->schoolClass?->name ?? 'Tanpa Kelas' }})
                            </option>
                        @endforeach
                    </select>
                @endif
                @error('student_id')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="title" class="block text-sm font-semibold text-[#182A3C] mb-1.5">
                    Judul Kasus Pembinaan <span class="text-rose-500">*</span>
                </label>
                <input id="title" name="title" type="text" required 
                    value="{{ old('title', isset($report) && $report ? 'Pembinaan: ' . ($report->violationRule?->name ?? 'Tindak Lanjut Pelanggaran') : '') }}" 
                    placeholder="Contoh: Akumulasi Keterlambatan dan Peringatan I"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] focus:border-transparent text-sm bg-white">
                @error('title')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="confidentiality" class="block text-sm font-semibold text-[#182A3C] mb-1.5">
                    Tingkat Kerahasiaan <span class="text-rose-500">*</span>
                </label>
                <select id="confidentiality" name="confidentiality" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] focus:border-transparent text-sm bg-white">
                    <option value="normal" {{ old('confidentiality', 'normal') === 'normal' ? 'selected' : '' }}>Standar / Internal (BK & Wali Kelas)</option>
                    <option value="confidential" {{ old('confidentiality') === 'confidential' ? 'selected' : '' }}>Rahasia / Khusus (Hanya Tim BK)</option>
                </select>
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-[#182A3C] mb-1.5">
                    Deskripsi Masalah / Latar Belakang <span class="text-rose-500">*</span>
                </label>
                <textarea id="description" name="description" rows="4" required placeholder="Jelaskan ringkasan masalah atau kejadian yang melatarbelakangi pembukaan kasus..."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] focus:border-transparent text-sm resize-none">{{ old('description', isset($report) && $report ? "Tindak lanjut dari laporan tanggal " . ($report->occurred_on?->format('d/m/Y') ?? '-') . ":\n" . $report->description : '') }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-slate-200">
                <button type="submit"
                    class="w-full py-3 px-4 rounded-xl bg-[#044A87] hover:bg-[#0360A4] text-white font-semibold text-sm transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#044A87]">
                    Buka Kasus Pembinaan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
