<?php

namespace App\Http\Controllers;

use App\Models\AccessRequest;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AccessRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'archive_id' => 'required|exists:archives,id',
            'reason' => 'required|string|max:500',
        ]);

        // Cek apakah karyawan sudah pernah mengajukan dokumen ini
        $existing = AccessRequest::where('user_id', auth()->id())
                        ->where('archive_id', $validated['archive_id'])
                        ->first();

        if ($existing) {
            if ($existing->status === 'pending') {
                return back()->with('error', 'Anda sudah mengajukan dokumen ini. Silakan tunggu persetujuan Admin.');
            } elseif ($existing->status === 'approved') {
                return back()->with('success', 'Anda sudah memiliki akses ke dokumen ini.');
            } else {
                // Jika sebelumnya ditolak, ajukan ulang menjadi pending
                $existing->update(['status' => 'pending', 'reason' => $validated['reason']]);
                ActivityLog::record('Ajukan Akses', "Mengajukan ulang akses dokumen ID: {$validated['archive_id']}");
                return back()->with('success', 'Permohonan ulang berhasil dikirim ke Admin.');
            }
        }

        // Buat permohonan baru
        AccessRequest::create([
            'user_id' => auth()->id(),
            'archive_id' => $validated['archive_id'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        ActivityLog::record('Ajukan Akses', "Meminta izin akses untuk dokumen ID: {$validated['archive_id']}");
        return back()->with('success', 'Permohonan akses berhasil dikirim ke Admin.');
    }

    public function approve(Request $request, $id)
    {
        // Validasi input pilihan durasi dari Admin
        $request->validate([
            'duration' => 'required|in:1_hour,1_day,3_days,7_days,permanent',
        ]);

        $accessRequest = AccessRequest::findOrFail($id);

        // Hitung kapan waktu kedaluwarsanya
        $expiresAt = match ($request->duration) {
            '1_hour'    => now()->addHour(),
            '1_day'     => now()->addDay(),
            '3_days'    => now()->addDays(3),
            '7_days'    => now()->addDays(7),
            'permanent' => null,
        };

        $accessRequest->update([
            'status'     => 'approved',
            'expires_at' => $expiresAt,
        ]);

        // Format teks untuk catatan aktivitas dan notifikasi
        $durasiTeks = $expiresAt ? "berlaku hingga {$expiresAt->translatedFormat('d M Y, H:i')}" : "berlaku permanen";
        
        ActivityLog::record('Akses Disetujui', "Menyetujui akses dokumen {$accessRequest->archive->document_number} untuk {$accessRequest->user->username} ({$durasiTeks})");
        
        return back()->with('success', "Akses dokumen {$accessRequest->archive->document_number} berhasil diberikan kepada {$accessRequest->user->username} ({$durasiTeks}).");
    }

    public function reject($id)
    {
        $request = AccessRequest::findOrFail($id);
        $request->update(['status' => 'rejected']);
        
        ActivityLog::record('Akses Ditolak', "Menolak permohonan akses dokumen {$request->archive->document_number} dari karyawan: {$request->user->username}");
        
        return back()->with('success', 'Permohonan akses dokumen telah ditolak.');
    }
}