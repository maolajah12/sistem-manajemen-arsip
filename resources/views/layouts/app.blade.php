<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Pusat Arsip')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#FAFAFA] text-stone-800 antialiased font-sans selection:bg-stone-200 flex h-screen overflow-hidden">

    <!-- Sidebar Kiri -->
    <aside id="sidebar" class="w-64 flex-shrink-0 bg-stone-900 text-stone-300 flex flex-col border-r border-stone-800 transition-all duration-300 ease-in-out z-20">
        <div class="h-16 flex items-center px-6 border-b border-stone-800 bg-stone-950">
            <h2 class="text-lg font-bold text-white tracking-tight">SPA <span class="text-stone-500 font-normal text-sm">Panel</span></h2>
        </div>
        
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md transition-colors {{ request()->routeIs('dashboard') ? 'bg-stone-800 text-white font-medium shadow-sm' : 'hover:bg-stone-800 hover:text-white' }}">
                <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Dasbor Statistik
            </a>
            
            <a href="{{ route('archives.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md transition-colors {{ request()->routeIs('archives.*') ? 'bg-stone-800 text-white font-medium shadow-sm' : 'hover:bg-stone-800 hover:text-white' }}">
                <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                Data Arsip
            </a>
            
            <a href="{{ route('loans.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md transition-colors {{ request()->routeIs('loans.*') ? 'bg-stone-800 text-white font-medium shadow-sm' : 'hover:bg-stone-800 hover:text-white' }}">
                <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Riwayat Peminjaman
            </a>

            @if(auth()->check() && auth()->user()->role === 'admin')
                @php
                    $pendingCount = \App\Models\User::where('is_approved', false)->count();
                @endphp
                <div class="pt-4 mt-4 border-t border-stone-800">
                    <p class="px-3 text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Administrator</p>
                    <a href="{{ route('approvals.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-md transition-colors {{ request()->routeIs('approvals.*') ? 'bg-stone-800 text-white font-medium shadow-sm' : 'hover:bg-stone-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <span>Persetujuan Akun</span>
                        </div>
                        @if($pendingCount > 0)
                            <span class="bg-amber-500 text-stone-950 text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('logs.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md transition-colors {{ request()->routeIs('logs.*') ? 'bg-stone-800 text-white font-medium shadow-sm' : 'hover:bg-stone-800 hover:text-white' }}">
    <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
    Catatan Aktivitas
</a>
                </div>
                <!-- TAUTAN ARSIP TERHAPUS -->
                    <a href="{{ route('archives.trash') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md transition-colors {{ request()->routeIs('archives.trash') ? 'bg-stone-800 text-white font-medium shadow-sm' : 'hover:bg-stone-800 hover:text-white' }}">
                        <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Arsip Terhapus
                    </a>
            @endif
        </nav>
    </aside>

    <!-- Area Konten Utama -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        
        <header class="h-16 bg-white border-b border-stone-200 flex items-center justify-between px-6 shadow-sm z-10">
            <div class="flex items-center gap-4">
                <button id="toggleSidebar" class="p-1.5 text-stone-500 hover:text-stone-900 hover:bg-stone-100 rounded-md transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h1 class="text-xl font-bold text-stone-800 tracking-tight">Sistem Pusat Arsip</h1>
            </div>
            
            @auth
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-sm font-semibold text-stone-800 leading-none">{{ auth()->user()->username }}</p>
                    <p class="text-xs text-stone-500 mt-1">{{ auth()->user()->role === 'admin' ? 'Admin Arsip' : 'Karyawan' }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-md transition-colors">
                        Keluar
                    </button>
                </form>
            </div>
            @endauth
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto p-8">
            @yield('content')
        </main>
        
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleSidebar');
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('-ml-64');
        });
    </script>
</body>
</html>