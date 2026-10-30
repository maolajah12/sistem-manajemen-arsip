@extends('layouts.app')

@section('title', 'Tambah Arsip Baru - Sistem Pusat Arsip')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6 border-b border-stone-200 pb-4">
        <h2 class="text-2xl font-semibold tracking-tight text-stone-900">Formulir Pendaftaran Arsip Baru</h2>
    </div>

    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-md mb-6 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-8 rounded-lg border border-stone-200 shadow-sm">
        <!-- Perhatikan penambahan enctype="multipart/form-data" di bawah ini -->
        <form action="{{ route('archives.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Nomor Dokumen</label>
                    <input type="text" name="document_number" value="{{ old('document_number') }}" required class="w-full border border-stone-300 px-3 py-2 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500 focus:border-stone-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Status</label>
                    <select name="status" required class="w-full border border-stone-300 px-3 py-2 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500 focus:border-stone-500">
                        <option value="Tersedia">Tersedia</option>
                        <option value="Dipinjam">Dipinjam</option>
                        <option value="Kedaluwarsa">Kedaluwarsa</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Judul Arsip</label>
                <input type="text" name="title" value="{{ old('title') }}" required class="w-full border border-stone-300 px-3 py-2 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500 focus:border-stone-500">
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Kategori</label>
                    <select name="category_id" required class="w-full border border-stone-300 px-3 py-2 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500 focus:border-stone-500">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Lokasi Fisik</label>
                    <select name="location_id" required class="w-full border border-stone-300 px-3 py-2 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500 focus:border-stone-500">
                        <option value="">-- Pilih Lokasi --</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->room_name }} (Rak {{ $location->shelf_number }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

           <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Tanggal Retensi</label>
                    <input type="date" name="retention_date" value="{{ old('retention_date', $archive->retention_date ?? '') }}" class="w-full border border-stone-300 px-3 py-2 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500 focus:border-stone-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Tingkat Akses File</label>
                    <select name="access_level" required class="w-full border border-stone-300 px-3 py-2 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500 focus:border-stone-500">
                        <option value="Bebas" {{ (old('access_level', $archive->access_level ?? '') == 'Bebas') ? 'selected' : '' }}>Bebas (Semua Karyawan)</option>
                        <option value="Terbatas" {{ (old('access_level', $archive->access_level ?? '') == 'Terbatas') ? 'selected' : '' }}>Terbatas (Hanya Admin)</option>
                    </select>
                </div>
            </div>

            <!-- Bagian ini adalah fitur unggah dokumen yang ditambahkan -->
            <div class="mb-8">
                <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Unggah Dokumen (Opsional)</label>
                <input type="file" name="file_document" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full border border-stone-300 px-3 py-2 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500 focus:border-stone-500 bg-white text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-stone-900 file:text-white hover:file:bg-stone-800">
                <p class="text-xs text-stone-400 mt-1">Format: PDF, DOCX, JPG, PNG. Maksimal 5MB.</p>
            </div>

            <div class="flex justify-end gap-4">
                <a href="{{ route('archives.index') }}" class="px-4 py-2 bg-white border border-stone-300 text-stone-700 font-medium rounded-md hover:bg-stone-50 transition-colors">Batal</a>
                <button type="submit" class="px-4 py-2 bg-stone-900 text-stone-50 font-medium rounded-md hover:bg-stone-800 transition-colors shadow-sm">Simpan Arsip</button>
            </div>
        </form>
    </div>
</div>
@endsection