<?php
namespace App\Exports;

use App\Models\Archive;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ArchivesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return Archive::with(['category', 'location'])->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return ['No. Dokumen', 'Judul Arsip', 'Kategori', 'Ruangan', 'Nomor Lemari', 'Nomor Rak', 'Status', 'Hak Akses', 'Tanggal Arsip'];
    }

    public function map($archive): array
    {
        return [
            $archive->document_number,
            $archive->title,
            $archive->category->name ?? '-',
            $archive->location->room_name ?? '-',
            $archive->location->cabinet_number ?? '-',
            $archive->location->shelf_number ?? '-',
            $archive->status,
            $archive->access_level,
            $archive->archive_date,
        ];
    }
}