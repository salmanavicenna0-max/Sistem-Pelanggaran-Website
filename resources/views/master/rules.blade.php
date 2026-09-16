@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-xl font-bold text-[#182A3C]">Manajemen Master Data Aturan</h2>
            <p class="text-sm text-[#64748B]">Kelola aturan pelanggaran dan apresiasi prestasi siswa</p>
        </div>
    </div>

    <!-- Vue Components -->
    <violation-rule-manager></violation-rule-manager>
    
    <achievement-rule-manager></achievement-rule-manager>
</div>
@endsection