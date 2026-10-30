@extends('layouts.app')

@section('title', 'Edit Arsip - Sistem Pusat Arsip')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6 border-b border-stone-200 pb-4">
        <h2 class="text-2xl font-semibold tracking-tight text-stone-900">Edit Data Arsip: {{ $archive->document_number }}</h2>
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
       <form action="{{ route('archives.update', $archive->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Nomor Dokumen</label>
                    <input type="text" name="document_number" value="{{ old('document_number', $archive->document_number) }}" required class="w-full border border-stone-300 px-3 py-2 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500 focus:border-stone-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Status</label>
                    <select name="status" required class="w-full border border-stone-300 px-3 py-2 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500 focus:border-stone-500">
                        <option value="Tersedia" {{ $archive->status == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="Dipinjam" {{ $archive->status == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="Kedaluwarsa" {{ $archive->status == 'Kedaluwarsa' ? 'selected' : '' }}>Kedaluwarsa</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Judul Arsip</label>
                <input type="text" name="title" value="{{ old('title', $archive->title) }}" required class="w-full border border-stone-300 px-3 py-2 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500 focus:border-stone-500">
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Kategori</label>
                    <select name="category_id" required class="w-full border border-stone-300 px-3 py-2 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500 focus:border-stone-500">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $archive->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Lokasi Fisik</label>
                    <select name="location_id" required class="w-full border border-stone-300 px-3 py-2 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500 focus:border-stone-500">
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ $archive->location_id == $location->id ? 'selected' : '' }}>
                                {{ $location->room_name }} (Rak {{ $location->shelf_number }})
                            </option>
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

            <div class="mb-8">
                <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Unggah Dokumen Baru (Opsional)</label>
                
                @if($archive->file_path)
                    <div class="mb-3 text-sm text-stone-600 bg-stone-50 p-3 rounded border border-stone-200 flex items-center justify-between">
                        <span>Dokumen saat ini: <strong>Tersedia</strong></span>
                        <a href="{{ asset('storage/' . $archive->file_path) }}" target="_blank" class="text-blue-600 hover:underline">Lihat Fail Lama</a>
                    </div>
                @endif
                
                <input type="file" name="file_document" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full border border-stone-300 px-3 py-2 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500 focus:border-stone-500 bg-white text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-stone-900 file:text-white hover:file:bg-stone-800">
                <p class="text-xs text-stone-400 mt-1">Biarkan kosong jika tidak ingin mengganti dokumen lama. Maksimal 5MB.</p>
            </div>

            <div class="flex justify-end gap-4">
                <a href="{{ route('archives.index') }}" class="px-4 py-2 bg-white border border-stone-300 text-stone-700 font-medium rounded-md hover:bg-stone-50 transition-colors">Batal</a>
                <button type="submit" class="px-4 py-2 bg-stone-900 text-stone-50 font-medium rounded-md hover:bg-stone-800 transition-colors shadow-sm">Perbarui Arsip</button>
            </div>
        </form>
    </div>
</div>
@endsection