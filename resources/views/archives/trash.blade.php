@extends('layouts.app')

@section('title', 'Arsip Terhapus - Sistem Pusat Arsip')

@section('content')
<div class="max-w-7xl mx-auto">

    <!-- Header & Tombol Aksi Massal -->
    <div class="flex flex-wrap justify-between items-end gap-4 mb-6 border-b border-stone-200 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('archives.index') }}" class="text-stone-400 hover:text-stone-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="text-2xl font-semibold tracking-tight text-stone-900">Arsip Terhapus</h2>
            </div>
            <p class="text-sm text-stone-500 mt-1 ml-7">Dokumen yang telah dihapus disimpan sementara di area ini dan dapat dipulihkan atau dimusnahkan permanen.</p>
        </div>

        @if($totalTrashed > 0)
        <div class="flex items-center gap-3">
            <!-- Tombol Musnahkan Terpilih (Muncul/Aktif saat ada checkbox yang dicentang) -->
            <button type="button" id="btn-destroy-selected" onclick="openVerifyModal('selected')" disabled class="px-4 py-2 text-sm font-medium rounded-md border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 transition-colors shadow-sm disabled:opacity-40 disabled:cursor-not-allowed inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Musnahkan Terpilih (<span id="selected-count">0</span>)
            </button>

            <!-- Tombol Musnahkan Seluruhnya -->
            <button type="button" onclick="openVerifyModal('all')" class="px-4 py-2 text-sm font-medium rounded-md bg-rose-600 text-white hover:bg-rose-700 transition-colors shadow-sm inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Musnahkan Seluruhnya ({{ $totalTrashed }})
            </button>
        </div>
        @endif
    </div>

    <!-- Notifikasi Sukses -->
    @if(session('success'))
        <div class="bg-teal-50 border border-teal-200 text-teal-800 px-4 py-3 rounded-md mb-6 text-sm flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Form Search & Filter (Sama dengan Halaman Data Arsip) -->
    <div class="bg-white p-5 rounded-lg border border-stone-200 shadow-sm mb-6">
        <form action="{{ route('archives.trash') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[240px]">
                <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Cari Dokumen</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nomor atau judul..." class="w-full border border-stone-300 px-3.5 py-2 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-stone-800 focus:border-stone-800">
            </div>

            <div class="w-48">
                <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Kategori</label>
                <select name="category_id" class="w-full border border-stone-300 px-3.5 py-2 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-stone-800 focus:border-stone-800 bg-white">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-44">
                <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Status</label>
                <select name="status" class="w-full border border-stone-300 px-3.5 py-2 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-stone-800 focus:border-stone-800 bg-white">
                    <option value="">Semua Status</option>
                    <option value="Tersedia" {{ request('status') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="Dipinjam" {{ request('status') == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="Kedaluwarsa" {{ request('status') == 'Kedaluwarsa' ? 'selected' : '' }}>Kedaluwarsa</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-stone-900 text-white px-5 py-2 rounded-md text-sm font-medium hover:bg-stone-800 transition-colors">
                    Filter
                </button>
                <a href="{{ route('archives.trash') }}" class="bg-white border border-stone-300 text-stone-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-stone-50 transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Arsip Terhapus -->
    <div class="bg-white rounded-lg border border-stone-200 shadow-sm overflow-hidden mb-4">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200">
                <thead>
                    <tr class="bg-stone-50/80">
                        <th class="px-6 py-4 text-left w-10">
                            <input type="checkbox" id="select-all" class="rounded border-stone-300 text-stone-900 focus:ring-stone-800 cursor-pointer w-4 h-4">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">No. Dokumen</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Judul Arsip</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Waktu Dihapus</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-stone-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-stone-100">
                    @forelse($archives as $archive)
                    <tr class="hover:bg-stone-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" value="{{ $archive->id }}" data-doc="{{ $archive->document_number }}" class="archive-checkbox rounded border-stone-300 text-stone-900 focus:ring-stone-800 cursor-pointer w-4 h-4">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-stone-900">{{ $archive->document_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-600">{{ $archive->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-500">{{ $archive->category->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-rose-600">{{ $archive->deleted_at->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center gap-3">
                                <!-- Tombol Pulihkan -->
                                <form action="{{ route('archives.restore', $archive->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-teal-700 hover:text-teal-900 bg-teal-50 border border-teal-200 px-3 py-1 rounded-md text-xs font-semibold transition-colors">
                                        Pulihkan
                                    </button>
                                </form>
                                
                                <!-- Tombol Musnahkan Satuan -->
                                <button type="button" onclick="openVerifyModal('single', '{{ $archive->id }}', '{{ $archive->document_number }}')" class="text-rose-600 hover:text-rose-800 bg-rose-50 border border-rose-200 px-3 py-1 rounded-md text-xs font-semibold transition-colors">
                                    Musnahkan
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-stone-500">Tidak ada data arsip yang terhapus saat ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $archives->links() }}
    </div>

</div>

<!-- Pop-up Eror Verifikasi Password (Muncul otomatis jika password salah) -->
@if(session('error'))
<div id="error-verify-popup" class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6 border border-stone-100">
        <div class="flex items-center justify-center w-14 h-14 mx-auto bg-rose-50 rounded-full mb-4 border border-rose-100">
            <svg class="w-7 h-7 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <h3 class="text-lg font-bold text-center text-stone-900 mb-2">Verifikasi Ditolak</h3>
        <p class="text-sm text-center text-stone-500 mb-6">{{ session('error') }}</p>
        <button onclick="document.getElementById('error-verify-popup').style.display='none'" class="px-4 py-2.5 bg-stone-900 text-white text-sm font-medium rounded-md hover:bg-stone-800 transition-colors w-full">
            Tutup & Coba Lagi
        </button>
    </div>
</div>
@endif

<!-- Modal Pop-up Verifikasi Password Admin untuk Pemusnahan Permanen -->
<div id="verify-destroy-modal" class="fixed inset-0 z-50 items-center justify-center bg-stone-900/60 backdrop-blur-sm transition-opacity duration-300" style="display: none; opacity: 0;">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 transform transition-all scale-100 opacity-100 border border-stone-200">
        <div class="flex items-center justify-center w-14 h-14 mx-auto bg-rose-100 rounded-full mb-4">
            <svg class="w-7 h-7 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
        </div>

        <h3 class="text-lg font-bold text-center text-stone-900 mb-2">Verifikasi Pemusnahan Arsip</h3>
        <p id="verify-modal-desc" class="text-sm text-center text-stone-500 mb-5"></p>

        <form id="master-destroy-form" action="{{ route('archives.bulk-force-delete') }}" method="POST">
            @csrf
            @method('DELETE')
            <input type="hidden" name="destroy_mode" id="destroy-mode-input" value="single">
            <div id="selected-ids-container"></div>

            <!-- Informasi Akun Admin & Input Password -->
            <div class="bg-stone-50 p-4 rounded-lg border border-stone-200 mb-6">
                <div class="flex items-center justify-between text-xs text-stone-500 mb-3 pb-2 border-b border-stone-200">
                    <span>Akun Otorisasi:</span>
                    <span class="font-bold text-stone-900">{{ auth()->user()->username }} (Admin)</span>
                </div>

                <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider mb-2">
                    Masukkan Password Admin
                </label>
                <div class="relative">
                    <input type="password" id="admin-password-input" name="admin_password" required placeholder="Ketik password Anda..." class="w-full border border-stone-300 px-3.5 py-2 pr-10 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-rose-600 focus:border-rose-600 bg-white">
                    
                    <!-- Tombol Tampilkan / Sembunyikan Password -->
                    <button type="button" onclick="toggleAdminPassword()" class="absolute inset-y-0 right-0 flex items-center pr-3 text-stone-400 hover:text-stone-600 focus:outline-none">
                        <svg id="eye-open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg id="eye-closed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex justify-center gap-3">
                <button type="button" onclick="closeVerifyModal()" class="px-4 py-2.5 bg-stone-100 text-stone-700 text-sm font-medium rounded-md hover:bg-stone-200 transition-colors w-full">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2.5 bg-rose-600 text-white text-sm font-medium rounded-md hover:bg-rose-700 transition-colors w-full shadow-sm">
                    Verifikasi & Musnahkan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const selectAllCheckbox = document.getElementById('select-all');
    const rowCheckboxes = document.querySelectorAll('.archive-checkbox');
    const selectedCountSpan = document.getElementById('selected-count');
    const btnDestroySelected = document.getElementById('btn-destroy-selected');

    // Fungsi memperbarui jumlah berkas yang dicentang
    function updateSelectionState() {
        const checked = document.querySelectorAll('.archive-checkbox:checked');
        if (selectedCountSpan) selectedCountSpan.innerText = checked.length;
        if (btnDestroySelected) btnDestroySelected.disabled = checked.length === 0;
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(cb => cb.checked = this.checked);
            updateSelectionState();
        });
    }

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            if (!this.checked && selectAllCheckbox) selectAllCheckbox.checked = false;
            updateSelectionState();
        });
    });

    // Membuka Pop-up Verifikasi sesuai mode (Satuan, Terpilih, atau Seluruhnya)
    function openVerifyModal(mode, singleId = null, singleDocNumber = '') {
        const modal = document.getElementById('verify-destroy-modal');
        const desc = document.getElementById('verify-modal-desc');
        const modeInput = document.getElementById('destroy-mode-input');
        const idsContainer = document.getElementById('selected-ids-container');
        const passInput = document.getElementById('admin-password-input');

        idsContainer.innerHTML = '';
        passInput.value = '';
        modeInput.value = mode;

        if (mode === 'single') {
            desc.innerHTML = `Dokumen <strong class="text-stone-900">${singleDocNumber}</strong> beserta berkas fisiknya akan dimusnahkan secara permanen dan tidak dapat dipulihkan.`;
            idsContainer.innerHTML = `<input type="hidden" name="selected_ids[]" value="${singleId}">`;
        } else if (mode === 'selected') {
            const checked = document.querySelectorAll('.archive-checkbox:checked');
            desc.innerHTML = `<strong class="text-stone-900">${checked.length} dokumen arsip terpilih</strong> beserta berkas fisiknya akan dimusnahkan secara permanen.`;
            checked.forEach(cb => {
                idsContainer.innerHTML += `<input type="hidden" name="selected_ids[]" value="${cb.value}">`;
            });
        } else if (mode === 'all') {
            desc.innerHTML = `<strong class="text-rose-600">SELURUH dokumen ({{ $totalTrashed }} arsip)</strong> di dalam daftar Arsip Terhapus akan dimusnahkan secara menyeluruh dan permanen.`;
        }

        modal.style.display = 'flex';
        setTimeout(() => {
            modal.style.opacity = '1';
            passInput.focus();
        }, 10);
    }

    function closeVerifyModal() {
        const modal = document.getElementById('verify-destroy-modal');
        modal.style.opacity = '0';
        setTimeout(() => modal.style.display = 'none', 300);
    }

    // Fitur Tampilkan / Sembunyikan Password Admin di Pop-up
    function toggleAdminPassword() {
        const input = document.getElementById('admin-password-input');
        const eyeOpen = document.getElementById('eye-open');
        const eyeClosed = document.getElementById('eye-closed');

        if (input.type === 'password') {
            input.type = 'text';
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        } else {
            input.type = 'password';
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
        }
    }
</script>
@endsection