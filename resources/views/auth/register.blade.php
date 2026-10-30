<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Sistem Pusat Arsip</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#FAFAFA] text-stone-800 antialiased min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-stone-900 text-white font-bold text-lg mb-3 shadow-sm">
                SPA
            </div>
            <h1 class="text-2xl font-semibold tracking-tight text-stone-900">Pendaftaran Akun</h1>
            <p class="text-sm text-stone-500 mt-1">Pendaftaran memerlukan persetujuan Admin Utama</p>
        </div>

        <div class="bg-white p-8 rounded-lg border border-stone-200 shadow-sm">
            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-md mb-6 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.submit') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" required autofocus placeholder="Contoh: budi_arsip" class="w-full border border-stone-300 px-3.5 py-2.5 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-stone-800 focus:border-stone-800 bg-stone-50/40">
                </div>

                <div>
    <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Password</label>
    <div class="relative">
        <input type="password" id="password" name="password" required placeholder="Minimal 6 karakter..." class="w-full border border-stone-300 px-3.5 py-2.5 pr-10 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-stone-800 focus:border-stone-800 bg-stone-50/40">
        
        <!-- Tombol Ikon Mata -->
        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center pr-3 text-stone-400 hover:text-stone-600 focus:outline-none">
            <!-- Ikon Mata Terbuka (Default) -->
            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            <!-- Ikon Mata Tertutup/Disilang (Disembunyikan) -->
            <svg id="eye-slash-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
            </svg>
        </button>
    </div>
</div>

                <div>
                    <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Mendaftar Sebagai</label>
                    <select name="role" required class="w-full border border-stone-300 px-3.5 py-2.5 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-stone-800 focus:border-stone-800 bg-stone-50/40">
                        <option value="karyawan" {{ old('role') == 'karyawan' ? 'selected' : '' }}>Karyawan Biasa (Lihat & Cari Arsip)</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin Arsip (Kelola Penuh Data Arsip)</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-stone-900 text-stone-50 py-2.5 rounded-md text-sm font-medium hover:bg-stone-800 transition-colors shadow-sm">
                    Kirim Permintaan Pendaftaran
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-stone-100 text-center">
                <p class="text-sm text-stone-500">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" class="font-semibold text-stone-900 hover:underline">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>

    <script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        const eyeSlashIcon = document.getElementById('eye-slash-icon');

        if (passwordInput.type === 'password') {
            // Ubah ke mode teks (sandi terlihat)
            passwordInput.type = 'text';
            eyeIcon.classList.add('hidden');
            eyeSlashIcon.classList.remove('hidden');
        } else {
            // Kembalikan ke mode sandi (sandi tersembunyi)
            passwordInput.type = 'password';
            eyeIcon.classList.remove('hidden');
            eyeSlashIcon.classList.add('hidden');
        }
    }
</script>

</body>
</html>