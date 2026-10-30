<?php
namespace App\Imports;

use App\Models\Archive;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ArchivesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Archive([
            'document_number' => $row['no_dokumen'],
            'title'           => $row['judul_arsip'],
            'category_id'     => $row['id_kategori'] ?? null,
            'location_id'     => $row['id_lokasi'] ?? null,
            'status'          => $row['status'] ?? 'Tersedia',
            'access_level'    => $row['hak_akses'] ?? 'Bebas',
            'archive_date'    => $row['tanggal'] ?? now(),
        ]);
    }
}