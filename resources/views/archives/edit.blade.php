<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Arsip</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Edit Data Arsip: {{ $archive->document_number }}</h2>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('archives.update', $archive->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Dokumen</label>
                    <input type="text" name="document_number" value="{{ old('document_number', $archive->document_number) }}" required class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" required class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:border-blue-500">
                        <option value="Tersedia" {{ $archive->status == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="Dipinjam" {{ $archive->status == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="Kedaluwarsa" {{ $archive->status == 'Kedaluwarsa' ? 'selected' : '' }}>Kedaluwarsa</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Judul Arsip</label>
                <input type="text" name="title" value="{{ old('title', $archive->title) }}" required class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:border-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select name="category_id" required class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:border-blue-500">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $archive->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi Fisik</label>
                    <select name="location_id" required class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:border-blue-500">
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ $archive->location_id == $location->id ? 'selected' : '' }}>
                                {{ $location->room_name }} ({{ $location->cabinet_number }}, {{ $location->shelf_number }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Retensi</label>
                <input type="date" name="retention_date" value="{{ old('retention_date', $archive->retention_date) }}" class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:border-blue-500">
            </div>

            <div class="flex justify-end gap-4">
                <a href="{{ route('archives.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Perbarui Arsip</button>
            </div>
        </form>
    </div>
</body>
</html>