@extends('layouts.app')

@section('title', 'Data Arsip - Sistem Pusat Arsip')

@section('content')
<!-- Pustaka Visual Searchable Dropdown (Tom Select CSS) -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    .ts-control { border-radius: 0.375rem; border-color: #d6d3d1; padding: 0.5rem 0.75rem; font-size: 0.875rem; background-color: #ffffff; }
    .ts-control.focus { border-color: #292524; box-shadow: 0 0 0 1px #292524; }
    .ts-dropdown { font-size: 0.875rem; border-radius: 0.375rem; }
</style>

<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex justify-between items-end mb-6 border-b border-stone-200 pb-4">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight text-stone-900">Data Arsip</h2>
            <p class="text-sm text-stone-500 mt-1">Kelola dan telusuri seluruh dokumen fisik maupun digital perusahaan.</p>
        </div>
       @if(auth()->user()->role === 'admin')
        <div class="flex items-center gap-2">
            <button type="button" onclick="openImportModal()" class="bg-white border border-stone-300 text-stone-700 px-4 py-2 text-sm font-medium rounded-md hover:bg-stone-50 transition-colors shadow-sm focus:outline-none flex items-center gap-2">
                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Impor Data
            </button>
            <a href="{{ route('archives.export') }}" class="bg-white border border-stone-300 text-stone-700 px-4 py-2 text-sm font-medium rounded-md hover:bg-stone-50 transition-colors shadow-sm focus:outline-none flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Ekspor Laporan
            </a>
            <a href="{{ route('archives.create') }}" class="bg-stone-900 text-stone-50 px-4 py-2 text-sm font-medium rounded-md hover:bg-stone-800 transition-colors shadow-sm focus:outline-none ml-2">
                + Tambah Arsip
            </a>
        </div>
        @endif
    </div>

    <!-- Notifikasi Sukses & Error -->
    @if(session('success'))
        <div class="bg-teal-50 border border-teal-200 text-teal-800 px-4 py-3 rounded-md mb-6 text-sm flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 text-teal-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-md mb-6 text-sm flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Form Pencarian & Filter Ekstensif -->
    <div class="bg-white p-5 rounded-lg border border-stone-200 shadow-sm mb-6">
        <form action="{{ route('archives.index') }}" method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 items-end">
                
                <!-- 1. Filter No. Dokumen -->
                <div>
                    <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">No. Dokumen</label>
                    <input type="text" name="document_number" value="{{ request('document_number') }}" placeholder="Contoh: DOC-123..." class="w-full border border-stone-300 px-3 py-2 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-stone-800 bg-white shadow-inner">
                </div>

                <!-- 2. Filter Judul -->
                <div>
                    <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Judul Arsip</label>
                    <input type="text" name="title" value="{{ request('title') }}" placeholder="Ketik kata kunci..." class="w-full border border-stone-300 px-3 py-2 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-stone-800 bg-white shadow-inner">
                </div>

                <!-- 3. Filter Kategori (Searchable Dropdown) -->
                <div>
                    <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Kategori</label>
                    <select name="category" class="searchable-select" placeholder="Pilih / ketik kategori...">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Filter Lokasi Fisik (Searchable Dropdown) -->
                <div>
                    <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Lokasi Fisik</label>
                    <select name="location" class="searchable-select" placeholder="Pilih / ketik lokasi...">
                        <option value="">Semua Lokasi</option>
                        @foreach($locations as $loc)
                            @php 
                                $room = $loc->room_name ?? '';
                                $cabinet = $loc->cabinet_number ?? '';
                                $shelf = $loc->shelf_number ?? '';
                                
                                $details = [];
                                if($room) $details[] = $room;
                                // Mencegah kata ganda jika di database sudah ada kata "lemari" atau "rak"
                                if($cabinet) $details[] = stripos($cabinet, 'lemari') !== false ? $cabinet : 'Lemari ' . $cabinet;
                                if($shelf) $details[] = stripos($shelf, 'rak') !== false ? $shelf : 'Rak ' . $shelf;
                                
                                $fullLoc = !empty($details) ? implode(' - ', $details) : '-';
                            @endphp
                            
                            @if($fullLoc !== '-')
                                <!-- Sekarang menggunakan $loc->id agar semua lokasi muncul tanpa tertimpa -->
                                <option value="{{ $loc->id }}" {{ request('location') == $loc->id ? 'selected' : '' }}>
                                    {{ $fullLoc }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <!-- 5. Filter Tanggal -->
                <div>
                    <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Tanggal</label>
                    <input type="date" name="date" value="{{ request('date') }}" class="w-full border border-stone-300 px-3 py-[9px] rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-stone-800 bg-white cursor-pointer shadow-inner">
                </div>

                <!-- 6. Filter Status & Fisik (Searchable Dropdown) -->
                <div>
                    <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Status & Fisik</label>
                    <select name="status" class="searchable-select" placeholder="Pilih / ketik status...">
                        <option value="">Semua Status</option>
                        <option value="Tersedia" {{ request('status') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="Dipinjam" {{ request('status') == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="Kedaluwarsa" {{ request('status') == 'Kedaluwarsa' ? 'selected' : '' }}>Kedaluwarsa</option>
                    </select>
                </div>
            </div>

            <!-- Tombol Aksi Filter -->
            <div class="flex justify-end gap-2 mt-5 pt-4 border-t border-stone-100">
                <a href="{{ route('archives.index') }}" class="bg-white border border-stone-300 text-stone-700 px-5 py-2 rounded-md text-sm font-medium hover:bg-stone-50 transition-colors focus:outline-none">
                    Bersihkan Filter
                </a>
                <button type="submit" class="bg-stone-900 text-white px-8 py-2 rounded-md text-sm font-medium hover:bg-stone-800 transition-colors shadow-sm focus:outline-none">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Arsip Utama -->
    <div class="bg-white rounded-lg border border-stone-200 shadow-sm overflow-hidden mb-4">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200">
                <thead>
                    <tr class="bg-stone-50/80">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">No. Dokumen</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Judul Arsip</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Lokasi Fisik</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-stone-500 uppercase tracking-wider">Status & Fisik</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-stone-500 uppercase tracking-wider">Berkas Digital</th>
                        <!-- Kolom Aksi sekarang ditampilkan untuk Karyawan dan Admin -->
                        <th class="px-6 py-4 text-center text-xs font-semibold text-stone-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-stone-100">
                    @forelse($archives as $archive)
                    <tr class="hover:bg-stone-50/50 transition-colors">
                        
                        <!-- 1. Nomor Dokumen & Rahasia -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-stone-900">{{ $archive->document_number }}</span>
                                @if($archive->access_level === 'Terbatas')
                                    <span title="Dokumen Terbatas / Rahasia" class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <svg class="w-3 h-3 mr-0.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        Terbatas
                                    </span>
                                @endif
                            </div>
                        </td>

                        <!-- 2. Judul Arsip -->
                        <td class="px-6 py-4 text-sm text-stone-600 max-w-xs truncate" title="{{ $archive->title }}">
                            {{ $archive->title }}
                        </td>

                        <!-- 3. Kategori -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-500">
                            {{ $archive->category->name ?? '-' }}
                        </td>

                        <!-- 4. Lokasi Fisik -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-500">
                            <span class="inline-flex items-center gap-1.5 text-stone-600">
                                <svg class="w-4 h-4 text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                @if($archive->location)
                                    @php
                                        $loc = $archive->location;
                                        $room = $loc->room_name ?? '';
                                        $cabinet = $loc->cabinet_number ?? '';
                                        $shelf = $loc->shelf_number ?? '';
                                        
                                        $details = [];
                                        if($room) $details[] = $room;
                                        if($cabinet) $details[] = stripos($cabinet, 'lemari') !== false ? $cabinet : 'Lemari ' . $cabinet;
                                        if($shelf) $details[] = stripos($shelf, 'rak') !== false ? $shelf : 'Rak ' . $shelf;
                                    @endphp
                                    {{ !empty($details) ? implode(' - ', $details) : '-' }}
                                @else
                                    {{ $archive->physical_location ?? '-' }}
                                @endif
                            </span>
                        </td>

                        <!-- 5. Tanggal Arsip -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-500">
                            {{ $archive->archive_date ? \Carbon\Carbon::parse($archive->archive_date)->format('d M Y') : $archive->created_at->format('d M Y') }}
                        </td>

                        <!-- 6. Status Fisik (Sekarang Hanya Menampilkan Indikator Saja) -->
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @php
                                $myPendingLoan = \App\Models\Loan::where('archive_id', $archive->id)->where('user_id', auth()->id())->where('status', 'pending')->exists();
                                $activeLoan = $archive->activeLoan;
                            @endphp

                            @if($archive->status === 'Tersedia')
                                <div class="flex flex-col items-center gap-1.5">
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-teal-50 text-teal-700 border border-teal-200">Tersedia</span>
                                    @if($myPendingLoan)
                                        <span class="text-[11px] font-semibold text-amber-600">Menunggu Persetujuan</span>
                                    @endif
                                </div>
                           @elseif($archive->status === 'Dipinjam')
                                <div class="flex flex-col items-center gap-1">
                                    @if($activeLoan && $activeLoan->return_deadline && $activeLoan->return_deadline->isPast())
                                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-rose-100 text-rose-700 border border-rose-300">Terlambat Kembali</span>
                                    @else
                                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">Dipinjam</span>
                                    @endif
                                    
                                    <!-- Menampilkan nama dan tanggal HANYA jika yang login adalah Admin -->
                                    @if($activeLoan && auth()->user()->role === 'admin')
                                        <span class="text-[11px] text-stone-600 font-medium">Oleh: <strong class="text-stone-900">{{ $activeLoan->user->username ?? '-' }}</strong></span>
                                        <span class="text-[10px] text-stone-400">s/d {{ $activeLoan->return_deadline ? $activeLoan->return_deadline->format('d M Y') : '-' }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-50 text-rose-700 border border-rose-200">{{ $archive->status }}</span>
                            @endif
                        </td>

                        <!-- 7. Kolom Berkas Digital -->
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($archive->file_path)
                                @php
                                    $isRestricted = $archive->access_level === 'Terbatas' && auth()->user()->role !== 'admin';
                                    $accessReq = null;
                                    $isExpired = false;

                                    if($isRestricted) {
                                        $accessReq = \App\Models\AccessRequest::where('user_id', auth()->id())->where('archive_id', $archive->id)->latest()->first();
                                        if($accessReq && $accessReq->status === 'approved' && $accessReq->expires_at && $accessReq->expires_at->isPast()) {
                                            $isExpired = true;
                                        }
                                    }
                                @endphp

                                @if($isRestricted && (!$accessReq || $accessReq->status !== 'approved' || $isExpired))
                                    @if($accessReq && $accessReq->status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-600 bg-amber-50 px-2.5 py-1.5 rounded-md border border-amber-200 shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Menunggu Izin
                                        </span>
                                    @else
                                        <button type="button" onclick="openRequestModal('{{ $archive->id }}', '{{ $archive->document_number }}', '{{ route('archives.preview', $archive->id) }}')" class="inline-flex items-center gap-1 text-xs font-bold text-stone-600 hover:text-stone-900 bg-stone-100 hover:bg-stone-200 border border-stone-200 px-3 py-1.5 rounded-md transition-colors shadow-sm focus:outline-none">
                                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                            {{ $isExpired ? 'Izin Habis (Minta Lagi)' : 'Minta Akses' }}
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ route('archives.preview', $archive->id) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-800 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                        Lihat File
                                    </a>
                                @endif
                            @else
                                <span class="text-xs text-stone-400">-</span>
                            @endif
                        </td>

                        <!-- 8. Kolom Aksi Terpadu (Semua Interaksi Berada di Sini) -->
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center gap-3">
                                
                                <!-- Tombol Pinjam Fisik (Ditampilkan untuk Karyawan dan Admin jika dokumen tersedia) -->
                                @if($archive->status === 'Tersedia' && !$myPendingLoan)
                                    <button type="button" onclick="openLoanModal('{{ $archive->id }}', '{{ $archive->document_number }}')" class="text-stone-600 bg-stone-100 hover:bg-stone-200 border border-stone-200 px-3 py-1.5 rounded-md text-xs font-semibold transition-colors shadow-sm focus:outline-none flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                        Pinjam Fisik
                                    </button>
                                @endif

                                <!-- Aksi Edit dan Hapus (Khusus Admin) -->
                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('archives.edit', $archive->id) }}" class="text-amber-600 hover:text-amber-800 transition-colors text-xs font-semibold">
                                        Edit
                                    </a>
                                    <form id="delete-form-{{ $archive->id }}" action="{{ route('archives.destroy', $archive->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="openDeleteModal('{{ $archive->id }}', '{{ $archive->document_number }}')" class="text-rose-600 hover:text-rose-800 transition-colors text-xs font-semibold focus:outline-none">
                                            Hapus
                                        </button>
                                    </form>
                                @endif

                                <!-- Tanda Strip jika tidak ada aksi yang bisa dilakukan (Karyawan melihat dokumen sedang dipinjam) -->
                                @if(auth()->user()->role !== 'admin' && ($archive->status !== 'Tersedia' || $myPendingLoan))
                                    <span class="text-xs text-stone-400">-</span>
                                @endif

                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <!-- Colspan sekarang 8 secara default karena kolom Aksi selalu tampil -->
                        <td colspan="8" class="px-6 py-8 text-center text-sm text-stone-500">
                            Tidak ada data arsip yang ditemukan berdasarkan pencarian.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginasi (Tombol Halaman) -->
    <div class="mt-4 mb-8">
        {{ $archives->links() }}
    </div>

</div>

<!-- ================= MODAL SECTION ================= -->

<!-- 1. Modal Hapus Arsip Utama (Admin) -->
<div id="delete-modal" class="fixed inset-0 z-50 items-center justify-center bg-stone-900/40 backdrop-blur-sm transition-opacity duration-300" style="display: none; opacity: 0;">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6 transform transition-all border border-stone-100">
        <div class="flex items-center justify-center w-14 h-14 mx-auto bg-amber-50 rounded-full mb-4 border border-amber-100">
            <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
        </div>
        <h3 class="text-lg font-bold text-center text-stone-900 mb-2">Hapus Arsip?</h3>
        <p class="text-sm text-center text-stone-500 mb-6">
            Pindahkan dokumen <span id="delete-doc-name" class="font-bold text-stone-900"></span> ke daftar Arsip Terhapus? Anda masih dapat memulihkannya nanti.
        </p>
        <div class="flex justify-center gap-3">
            <button type="button" onclick="closeDeleteModal()" class="px-4 py-2.5 bg-white border border-stone-200 text-stone-700 text-sm font-medium rounded-md hover:bg-stone-50 transition-colors w-full focus:outline-none">
                Batal
            </button>
            <button type="button" id="confirm-delete-btn" class="px-4 py-2.5 bg-rose-600 text-white text-sm font-medium rounded-md hover:bg-rose-700 transition-colors w-full focus:outline-none">
                Ya, Pindahkan
            </button>
        </div>
    </div>
</div>

<!-- 2. Modal Permohonan Akses File Digital (Karyawan) -->
<div id="request-access-modal" class="fixed inset-0 z-50 items-center justify-center bg-stone-900/60 backdrop-blur-sm transition-opacity duration-300" style="display: none; opacity: 0;">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 transform transition-all border border-stone-200">
        <div class="flex items-center justify-center w-14 h-14 mx-auto bg-amber-50 rounded-full mb-4 border border-amber-100">
            <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
        </div>
        <h3 class="text-lg font-bold text-center text-stone-900 mb-1">Dokumen Terbatas</h3>
        <p class="text-sm text-center text-stone-500 mb-5">
            Dokumen <span id="request-doc-number" class="font-bold text-stone-900"></span> bersifat rahasia. Silakan ajukan permohonan akses kepada Administrator.
        </p>
        <form action="{{ route('access-requests.store') }}" method="POST">
            @csrf
            <input type="hidden" name="archive_id" id="request-archive-id">
            <div class="mb-5">
                <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider mb-2">Alasan Keperluan</label>
                <textarea name="reason" required rows="3" placeholder="Contoh: Membutuhkan data untuk laporan rekap bulanan..." class="w-full border border-stone-300 px-3.5 py-2.5 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-stone-800 focus:border-stone-800 bg-stone-50/50"></textarea>
            </div>
            <div class="flex justify-center gap-3">
                <button type="button" onclick="closeRequestModal()" class="px-4 py-2.5 bg-white border border-stone-200 text-stone-700 text-sm font-medium rounded-md hover:bg-stone-50 transition-colors w-full focus:outline-none">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2.5 bg-stone-900 text-white text-sm font-medium rounded-md hover:bg-stone-800 transition-colors w-full shadow-sm focus:outline-none">
                    Kirim Permohonan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 3. Modal Pinjam Berkas Fisik -->
<div id="loan-modal" class="fixed inset-0 z-50 items-center justify-center bg-stone-900/60 backdrop-blur-sm transition-opacity duration-300" style="display: none; opacity: 0;">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 border border-stone-200">
        <h3 class="text-lg font-bold text-stone-900 mb-1">Ajukan Peminjaman Fisik</h3>
        <p class="text-sm text-stone-500 mb-5">
            Dokumen: <span id="loan-doc-number" class="font-bold text-stone-900"></span>
        </p>
        <form action="{{ route('loans.store') }}" method="POST">
            @csrf
            <input type="hidden" name="archive_id" id="loan-archive-id">
            <div class="mb-5">
                <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider mb-2">Tujuan Peminjaman</label>
                <textarea name="reason" required rows="3" placeholder="Contoh: Untuk keperluan rapat evaluasi proyek..." class="w-full border border-stone-300 px-3.5 py-2.5 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-stone-800 focus:border-stone-800 bg-stone-50/50"></textarea>
            </div>
            <div class="flex justify-center gap-3">
                <button type="button" onclick="closeLoanModal()" class="px-4 py-2.5 bg-white border border-stone-200 text-stone-700 text-sm font-medium rounded-md hover:bg-stone-50 transition-colors w-full focus:outline-none">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2.5 bg-stone-900 text-white text-sm font-medium rounded-md hover:bg-stone-800 transition-colors w-full shadow-sm focus:outline-none">
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Skrip Penggerak Tom Select (Searchable Dropdown) -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.searchable-select').forEach((el) => {
            new TomSelect(el, {
                create: false,
                maxOptions: 50,
                sortField: { field: "text", direction: "asc" }
            });
        });
    });

    // 1. Script Modal Hapus (Admin)
    let formToSubmitId = null;
    function openDeleteModal(id, name) {
        formToSubmitId = 'delete-form-' + id;
        document.getElementById('delete-doc-name').innerText = name;
        const modal = document.getElementById('delete-modal');
        modal.style.display = 'flex';
        setTimeout(() => modal.style.opacity = '1', 10);
    }
    function closeDeleteModal() {
        const modal = document.getElementById('delete-modal');
        modal.style.opacity = '0';
        setTimeout(() => modal.style.display = 'none', 300);
        formToSubmitId = null;
    }
    document.getElementById('confirm-delete-btn').addEventListener('click', function() {
        if(formToSubmitId) document.getElementById(formToSubmitId).submit();
    });

    // 2. Script Modal Permohonan Akses Digital
    function openRequestModal(archiveId, docNumber, logUrl) {
        document.getElementById('request-archive-id').value = archiveId;
        document.getElementById('request-doc-number').innerText = docNumber;
        const modal = document.getElementById('request-access-modal');
        modal.style.display = 'flex';
        setTimeout(() => modal.style.opacity = '1', 10);
        fetch(logUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).catch(e => console.error(e));
    }
    function closeRequestModal() {
        const modal = document.getElementById('request-access-modal');
        modal.style.opacity = '0';
        setTimeout(() => modal.style.display = 'none', 300);
    }

    // 3. Script Modal Pinjam Fisik
    function openLoanModal(id, docNumber) {
        document.getElementById('loan-archive-id').value = id;
        document.getElementById('loan-doc-number').innerText = docNumber;
        const modal = document.getElementById('loan-modal');
        modal.style.display = 'flex';
        setTimeout(() => modal.style.opacity = '1', 10);
    }
    function closeLoanModal() {
        const modal = document.getElementById('loan-modal');
        modal.style.opacity = '0';
        setTimeout(() => modal.style.display = 'none', 300);
    }
</script>

<!-- Modal Impor Excel (Admin) -->
<div id="import-modal" class="fixed inset-0 z-50 items-center justify-center bg-stone-900/60 backdrop-blur-sm transition-opacity duration-300" style="display: none; opacity: 0;">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 border border-stone-200">
        <div class="flex items-center justify-center w-14 h-14 mx-auto bg-teal-50 rounded-full mb-4 border border-teal-100">
            <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
        </div>
        <h3 class="text-lg font-bold text-center text-stone-900 mb-1">Impor Arsip Massal</h3>
        <p class="text-sm text-center text-stone-500 mb-5">Unggah fail Excel (.xlsx) atau CSV yang berisi ratusan data arsip lama. Pastikan tajuk kolom sesuai (no_dokumen, judul_arsip, dll).</p>
        
        <form action="{{ route('archives.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-5">
                <input type="file" name="import_file" required accept=".xlsx, .xls, .csv" class="w-full text-sm text-stone-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer border border-stone-300 rounded-md p-2 bg-stone-50">
            </div>
            <div class="flex justify-center gap-3">
                <button type="button" onclick="closeImportModal()" class="px-4 py-2.5 bg-white border border-stone-200 text-stone-700 text-sm font-medium rounded-md hover:bg-stone-50 transition-colors w-full focus:outline-none">Batal</button>
                <button type="submit" class="px-4 py-2.5 bg-stone-900 text-white text-sm font-medium rounded-md hover:bg-stone-800 transition-colors w-full shadow-sm focus:outline-none">Mulai Impor</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openImportModal() {
        const modal = document.getElementById('import-modal');
        modal.style.display = 'flex';
        setTimeout(() => modal.style.opacity = '1', 10);
    }
    function closeImportModal() {
        const modal = document.getElementById('import-modal');
        modal.style.opacity = '0';
        setTimeout(() => modal.style.display = 'none', 300);
    }
</script>
@endsection