@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-xl font-bold text-[#182A3C]">Data Pengguna & Kelas</h2>
            <p class="text-sm text-[#64748B]">Kelola tahun ajaran, kelas, data guru, dan siswa</p>
        </div>
    </div>

    <!-- Vue Master Data Components -->
    <school-year-manager></school-year-manager>
    
    <school-class-manager></school-class-manager>
    
    <teacher-manager></teacher-manager>
    
    <student-manager></student-manager>
</div>
@endsection