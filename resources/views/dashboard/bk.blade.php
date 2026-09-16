@extends('layouts.app')

@section('content')

<!-- KESISWAAN / BK Dashboard -->
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 mb-6">
        {{-- Filters --}}
        <form method="GET" action="{{ route('dashboard') }}" class="mb-6 bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Kelas</label>
                <select name="class_id" class="w-full rounded-lg border-slate-200 shadow-sm focus:border-[#044A87] focus:ring focus:ring-[#044A87]/20 text-sm">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->grade_level }} - {{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 w-full">
                <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-lg border-slate-200 shadow-sm focus:border-[#044A87] focus:ring focus:ring-[#044A87]/20 text-sm">
            </div>
            <div class="flex-1 w-full">
                <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-lg border-slate-200 shadow-sm focus:border-[#044A87] focus:ring focus:ring-[#044A87]/20 text-sm">
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-[#044A87] text-white rounded-lg hover:bg-[#033663] text-sm font-medium shadow transition-colors">Terapkan</button>
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-white text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 text-sm font-medium ml-2 shadow-sm transition-colors">Reset</a>
            </div>
        </form>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4 mb-5">
            <div class="bg-[#044A87] text-white rounded-xl py-4 flex items-center justify-center">
                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 4"></svg>
                <span class="hidden md:inline">Siswa</span>
                <div class="text-2xl font-bold">{{ number_format($stats['total_students']) }}</div>
            </div>
            <div class="bg-[#51C4C1] text-white rounded-xl py-4 flex items-center justify-center">
                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 4"></svg>
                <span class="hidden md:inline">Total Laporan</span>
                <div class="text-2xl font-bold">{{ number_format($stats['total_reports']) }}</div>
            </div>
            <div class="bg-[#E9A23B] text-white rounded-xl py-4 flex items-center justify-center">
                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 4"></svg>
                <span class="hidden md:inline">Pending Approval</span>
                <div class="text-2xl font-bold text-amber-200">{{ number_format($stats['pending_approval']) }}</div>
            </div>
            <div class="bg-[#D64545] text-white rounded-xl py-4 flex items-center justify-center">
                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 4"></svg>
                <span class="hidden md:inline">Kasus Aktif</span>
                <div class="text-2xl font-bold">{{ number_format($stats['active_cases']) }}</div>
            </div>
        </div>

        {{-- Charts Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Zone Distribution Chart --}}
            <div>
                <h3 class="text-sm font-semibold text-[#182A3C] mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#044A87]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v10c7-6 7-6 14 0v-10m-7 3h5"></svg>
                    Distribusi Zona Poin Siswa
                </h3>
                <div class="bg-[#F5F9FA] rounded-xl p-4 flex justify-center">
                    <div style="height: 250px; width: 100%;">
                        <canvas id="zoneChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Status Report Chart --}}
            <div>
                <h3 class="text-sm font-semibold text-[#182A3C] mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#E9A23B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                    Distribusi Status Laporan
                </h3>
                <div class="bg-[#F5F9FA] rounded-xl p-4 flex justify-center">
                    <div style="height: 250px; width: 100%;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Students --}}
        <div>
            <h3 class="text-sm font-semibold text-[#182A3C] mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#044A87]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002 2h2a2 2 0 002-2h-2m-3-3h4m4-4h4m5-3v4m-5 3h4"/></svg>
                Siswa Terbanyak Poin
            </h3>
            <div class="space-y-3 max-h-40 overflow-y-auto">
                @foreach ($topStudents as $s)
                    <div class="flex items-center gap-3 px-2 py-1 rounded bg-white border border-slate-100">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#044A87] to-[#51C4C1] flex items-center justify-center text-white font-medium text-sm">
                            {{ strtoupper(substr($s->user->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-[#182A3C]">{{ $s->user->name }}</p>
                            <p class="text-xs text-[#64748B]">Poin: {{ number_format($s->getCurrentPoints()) }} - {{ $s->point_zone['name'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.Chart) {
        // Zone Chart
        const zoneCtx = document.getElementById('zoneChart').getContext('2d');
        const zoneData = @json($zoneData);
        new window.Chart(zoneCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(zoneData),
                datasets: [{
                    data: Object.values(zoneData),
                    backgroundColor: [
                        '#3B82F6', '#10B981', '#F59E0B', '#EF4444',
                        '#8B5CF6', '#EC4899', '#14B8A6', '#6366F1'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });

        // Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusData = @json($statusData);
        new window.Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(statusData),
                datasets: [{
                    data: Object.values(statusData),
                    backgroundColor: ['#F59E0B', '#10B981', '#EF4444']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });
    }
});
</script>
@endpush