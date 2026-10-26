<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Location;
use App\Models\Archive;
use App\Models\Loan;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Data Master Kategori
        $categories = ['Surat Keputusan', 'Kontrak Kerja Sama', 'Nota Dinas', 'Laporan Keuangan', 'Dokumen Legal'];
        foreach ($categories as $category) {
            Category::create(['name' => $category, 'description' => 'Kategori dokumen ' . $category]);
        }

        // 2. Buat Data Master Lokasi
        for ($i = 1; $i <= 5; $i++) {
            Location::create([
                'room_name' => 'Ruang Arsip Utama',
                'cabinet_number' => 'Lemari ' . fake()->randomElement(['A', 'B', 'C', 'D']),
                'shelf_number' => 'Rak ' . $i
            ]);
        }

        // 3. Cetak 100 Data Arsip (Transaksional)
        Archive::factory(100)->create();

        // 4. Cetak 50 Data Riwayat Peminjaman (Transaksional)
        Loan::factory(50)->create();
    }
}
