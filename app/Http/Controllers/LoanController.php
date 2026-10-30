<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Archive;
use App\Models\ActivityLog;

class LoanController extends Controller
{
    // Menampilkan daftar peminjaman & riwayat
    public function index()
    {
        $query = Loan::with(['user', 'archive'])->latest();
        
        // Karyawan hanya melihat riwayatnya sendiri, Admin melihat semua
        if (auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }
        
        $loans = $query->paginate(15);
        
        // INTEGRASI BARU: Ambil semua dokumen fisik yang sedang "Tersedia"
        $availableArchives = Archive::where('status', 'Tersedia')->orderBy('document_number', 'asc')->get();

        return view('loans.index', compact('loans', 'availableArchives'));
    }

    // Karyawan mengajukan pinjaman fisik
    public function store(Request $request)
    {
        $request->validate([
            'archive_id' => 'required|exists:archives,id',
            'reason' => 'required|string|max:255',
        ]);

        $archive = Archive::findOrFail($request->archive_id);

        if ($archive->status !== 'Tersedia') {
            return back()->with('error', 'Dokumen fisik sedang tidak tersedia atau dipinjam orang lain.');
        }

        Loan::create([
            'user_id' => auth()->id(),
            'archive_id' => $archive->id,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        ActivityLog::record('Ajukan Pinjam', "Mengajukan pinjaman fisik untuk dokumen: {$archive->document_number}");
        return back()->with('success', 'Pengajuan pinjaman fisik berhasil dikirim ke Admin.');
    }

    // Admin menyetujui dan menetapkan tenggat waktu
    public function approve(Request $request, $id)
    {
        $request->validate(['duration_days' => 'required|integer|min:1']);
        
        $loan = Loan::findOrFail($id);
        
        $loan->update([
            'status' => 'approved',
            // Solusi: Tambahkan (int) agar teks dikonversi menjadi angka murni
            'return_deadline' => now()->addDays((int) $request->duration_days)
        ]);

        // Ubah status arsip induk menjadi dipinjam
        $loan->archive->update(['status' => 'Dipinjam']);

        ActivityLog::record('Pinjaman Disetujui', "Menyetujui peminjaman fisik {$loan->archive->document_number} untuk {$loan->user->username} selama {$request->duration_days} hari.");
        return back()->with('success', "Pinjaman fisik disetujui. Status dokumen berubah menjadi Dipinjam.");
    }

    // Admin mencatat pengembalian fisik
    public function markReturned($id)
    {
        $loan = Loan::findOrFail($id);
        
        $loan->update([
            'status' => 'returned',
            'returned_at' => now()
        ]);

        // Kembalikan status arsip menjadi tersedia
        $loan->archive->update(['status' => 'Tersedia']);

        ActivityLog::record('Dokumen Dikembalikan', "Menerima pengembalian fisik dokumen: {$loan->archive->document_number}");
        return back()->with('success', "Dokumen fisik telah dikembalikan ke ruang arsip.");
    }

    public function reject($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->update(['status' => 'rejected']);
        ActivityLog::record('Pinjaman Ditolak', "Menolak peminjaman fisik dokumen: {$loan->archive->document_number}");
        return back()->with('success', "Pengajuan pinjaman fisik telah ditolak.");
    }
}