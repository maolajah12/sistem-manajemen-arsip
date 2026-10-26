<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Sistem Arsip</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-7xl mx-auto">
        
        <!-- Navigasi Sederhana -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Dasbor Statistik Arsip</h1>
            <a href="{{ route('archives.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">Kelola Data Arsip</a>
        </div>

        <!-- Kartu Metrik Utama -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
                <h3 class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Total Arsip Dokumen</h3>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalArchives }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-red-500">
                <h3 class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Arsip Sedang Dipinjam</h3>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $borrowedArchives }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
                <h3 class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Total Kategori Aktif</h3>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalCategories }}</p>
            </div>
        </div>

        <!-- Tabel Distribusi Kategori -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Distribusi Arsip Berdasarkan Kategori</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Arsip Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($archivesPerCategory as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $category->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->archives_count }} Dokumen</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>