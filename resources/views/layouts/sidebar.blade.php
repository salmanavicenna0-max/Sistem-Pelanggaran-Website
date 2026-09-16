<aside class="w-64 bg-[#044A87] text-white flex-shrink-0 flex flex-col justify-between hidden md:flex shadow-xl z-20">
    <div>
        {{-- Logo / School Header --}}
        <div class="h-16 flex items-center px-6 border-b border-white/10 gap-3">
            <div class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center font-bold text-[#51C4C1] text-lg">
                6
            </div>
            <div>
                <h1 class="font-bold text-sm leading-tight text-white">SMAN 6 BANDUNG</h1>
                <p class="text-[11px] text-white/70">Sistem Disiplin & Prestasi</p>
            </div>
        </div>

        {{-- User Role Badge in Sidebar --}}
        <div class="px-6 py-4 border-b border-white/10 bg-white/5">
            <p class="text-xs text-white/70">Login sebagai:</p>
            <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
            <div class="mt-1 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                @if(auth()->user()->role === 'kesiswaan_bk') bg-emerald-500/20 text-emerald-300 border border-emerald-400/30
                @elseif(auth()->user()->isHomeroomTeacher()) bg-amber-500/20 text-amber-300 border border-amber-400/30
                @elseif(auth()->user()->role === 'guru') bg-cyan-500/20 text-cyan-300 border border-cyan-400/30
                @else bg-purple-500/20 text-purple-300 border border-purple-400/30 @endif">
                @if(auth()->user()->role === 'kesiswaan_bk') Kesiswaan & BK
                @elseif(auth()->user()->isHomeroomTeacher()) Guru / Wali Kelas
                @elseif(auth()->user()->role === 'guru') Guru Pengajar
                @else Siswa @endif
            </div>
        </div>

        {{-- Nav Links --}}
        <nav class="px-3 py-4 space-y-1 text-sm font-medium">
            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dashboard*') ? 'bg-[#0360A4] text-white font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 text-[#51C4C1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            {{-- Modul Laporan --}}
            @if(auth()->user()->role !== 'siswa')
                <div class="pt-3 pb-1 px-3 text-[11px] font-bold text-white/50 uppercase tracking-wider">Pelaporan</div>
                <a href="{{ route('reports.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('reports.create') ? 'bg-[#0360A4] text-white font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 text-[#51C4C1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Buat Laporan Baru
                </a>
                <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('reports.index') ? 'bg-[#0360A4] text-white font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 text-[#51C4C1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Daftar Laporan
                </a>
            @else
                <div class="pt-3 pb-1 px-3 text-[11px] font-bold text-white/50 uppercase tracking-wider">Layanan Siswa</div>
                <a href="{{ route('reports.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('reports.create') ? 'bg-[#0360A4] text-white font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 text-[#51C4C1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Laporkan Kejadian
                </a>
                <a href="{{ route('points.history') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('points.history') ? 'bg-[#0360A4] text-white font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 text-[#51C4C1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Buku Saku & Poin Saya
                </a>
            @endif

            {{-- Modul BK & Wali Kelas --}}
            @if(auth()->user()->role === 'kesiswaan_bk' || auth()->user()->isHomeroomTeacher())
                <div class="pt-3 pb-1 px-3 text-[11px] font-bold text-white/50 uppercase tracking-wider">Disiplin & Kasus</div>
                <a href="{{ route('points.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('points.index*') ? 'bg-[#0360A4] text-white font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 text-[#51C4C1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Rekap Poin Siswa
                </a>
                <a href="{{ route('cases.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('cases.*') ? 'bg-[#0360A4] text-white font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 text-[#51C4C1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Kasus Pembinaan
                </a>
            @endif

            {{-- Master Data BK Only --}}
            @if(auth()->user()->role === 'kesiswaan_bk')
                <div class="pt-3 pb-1 px-3 text-[11px] font-bold text-white/50 uppercase tracking-wider">Master Data</div>
                <a href="{{ route('master.rules') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('master.rules*') ? 'bg-[#0360A4] text-white font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 text-[#51C4C1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Aturan Disiplin & Prestasi
                </a>
                <a href="{{ route('master.students') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('master.students*') ? 'bg-[#0360A4] text-white font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 text-[#51C4C1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Data Siswa & Import
                </a>
                <a href="{{ route('master.export') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('master.export*') ? 'bg-[#0360A4] text-white font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 text-[#51C4C1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak Rekap & Laporan
                </a>
            @endif
        </nav>
    </div>

    {{-- Logout button --}}
    <div class="p-4 border-t border-white/10">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 text-sm text-red-200 hover:bg-red-500/20 hover:text-white rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Keluar Sistem
            </button>
        </form>
    </div>
</aside>
