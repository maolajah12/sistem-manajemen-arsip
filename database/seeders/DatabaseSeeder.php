<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Location;
use App\Models\Archive;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun Pengguna (Untuk Login)
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@arsip.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        User::create([
            'name' => 'Karyawan',
            'username' => 'karyawan',
            'email' => 'karyawan@arsip.com',
            'password' => Hash::make('password'),
            'role' => 'karyawan' // <-- Ubah dari 'user' menjadi 'karyawan'
        ]);

        // 2. Buat Data Master Kategori
        $categories = ['Surat Keputusan', 'Kontrak Kerja Sama', 'Nota Dinas', 'Laporan Keuangan', 'Dokumen Legal'];
        foreach ($categories as $category) {
            Category::create(['name' => $category, 'description' => 'Kategori dokumen ' . $category]);
        }

        // 3. Buat Data Master Lokasi
        for ($i = 1; $i <= 5; $i++) {
            Location::create([
                'room_name' => 'Ruang Arsip Utama',
                'cabinet_number' => 'Lemari ' . fake()->randomElement(['A', 'B', 'C', 'D']),
                'shelf_number' => 'Rak ' . $i
            ]);
        }

        // 4. Cetak 100 Data Arsip (Transaksional)
        Archive::factory(100)->create();

        // 5. Cetak 50 Data Riwayat Peminjaman (Transaksional)
        Loan::factory(50)->create();
    }
}