<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Password Pertama Kali</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F5F9FA] min-h-screen flex items-center justify-center p-4 font-sans text-[#182A3C]">
    <main class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-100 p-8">
        <div class="mb-6 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-amber-100 text-amber-600 mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-[#182A3C]">Wajib Ubah Password</h1>
            <p class="text-sm text-[#64748B] mt-1">Akun Anda masih menggunakan password default. Demi keamanan (US-08), silakan buat password baru sebelum melanjutkan.</p>
        </div>

        @if ($errors->any())
            <div class="mb-5 p-3 rounded-lg bg-red-50 border border-red-200 text-sm text-[#D64545]">
                Terdapat kesalahan dalam pengisian form. Silakan periksa kembali.
            </div>
        @endif

        <form method="POST" action="{{ route('password.change.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="block text-sm font-medium text-[#182A3C] mb-1">Password Saat Ini</label>
                <input id="current_password" name="current_password" type="password" required autocomplete="current-password"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] focus:border-transparent text-sm">
                @error('current_password')
                    <p class="text-xs text-[#D64545] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-[#182A3C] mb-1">Password Baru (min. 8 karakter)</label>
                <input id="password" name="password" type="password" required autocomplete="new-password"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] focus:border-transparent text-sm">
                @error('password')
                    <p class="text-xs text-[#D64545] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-[#182A3C] mb-1">Konfirmasi Password Baru</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#044A87] focus:border-transparent text-sm">
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full py-2.5 px-4 rounded-lg bg-[#044A87] hover:bg-[#0360A4] text-white font-medium text-sm transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#044A87]">
                    Simpan Password Baru
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">
            @csrf
            <button type="submit" class="text-xs text-slate-500 hover:text-slate-700 underline">
                Keluar dan ganti password nanti
            </button>
        </form>
    </main>
</body>
</html>
