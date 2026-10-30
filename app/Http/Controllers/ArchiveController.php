<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Exports\ArchivesExport;
use App\Imports\ArchivesImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Location;
use App\Models\ActivityLog;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        // 1. Mulai bangun kueri pangkalan data
        $query = \App\Models\Archive::with(['category', 'location'])->latest();

        // 2. Filter berdasarkan No. Dokumen
        if ($request->filled('document_number')) {
            $query->where('document_number', 'like', '%' . $request->document_number . '%');
        }

        // 3. Filter berdasarkan Judul Arsip
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        // 4. Filter Kategori (Mencari berdasarkan nama kategori yang diketik/dipilih)
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->category . '%');
            });
        }

       
        // 5. Filter Lokasi Fisik (Berdasarkan ID Unik)
        if ($request->filled('location')) {
            $query->where('location_id', $request->location);
        }
        
        // 6. Filter Tanggal
        if ($request->filled('date')) {
            $query->where(function($q) use ($request) {
                $q->whereDate('archive_date', $request->date)
                  ->orWhereDate('created_at', $request->date);
            });
        }

        // 7. Filter Status & Fisik
        if ($request->filled('status')) {
            $query->where('status', 'like', '%' . $request->status . '%');
        }

        // Eksekusi data (dengan mempertahankan url filter saat pindah halaman)
        $archives = $query->paginate(15)->withQueryString();
        
        // Ambil data acuan untuk pilihan Dropdown
        $categories = \App\Models\Category::orderBy('name', 'asc')->get();
        $locations = \App\Models\Location::all();

        return view('archives.index', compact('archives', 'categories', 'locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Mengambil master data untuk diisi ke dalam dropdown form
        $categories = Category::all();
        $locations = Location::all();
        
        return view('archives.create', compact('categories', 'locations'));
    }

    public function store(Request $request)
    {
        // Validasi ketat ala enterprise sebelum data masuk ke database
        $validated = $request->validate([
            'document_number' => 'required|unique:archives,document_number',
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'retention_date' => 'nullable|date',
            'status' => 'required|in:Tersedia,Dipinjam,Kedaluwarsa',
            'access_level' => 'required|in:Bebas,Terbatas',
        ]);

        Archive::create($validated);

        ActivityLog::record('Tambah Arsip', "Menambahkan arsip baru dengan nomor dokumen: {$validated['document_number']}");

        return redirect()->route('archives.index')->with('success', 'Arsip baru berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Archive $archive)
    {
        $categories = Category::all();
        $locations = Location::all();
        
        return view('archives.edit', compact('archive', 'categories', 'locations'));
    }

    public function update(Request $request, Archive $archive)
    {
        $validated = $request->validate([
            'document_number' => 'required|unique:archives,document_number,' . $archive->id,
            'title' => 'required',
            'category_id' => 'required',
            'location_id' => 'required',
            'status' => 'required',
            'retention_date' => 'nullable|date',
            'file_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'access_level' => 'required|in:Bebas,Terbatas',
        ]);

        // Jika ada file baru yang diunggah saat diedit
        if ($request->hasFile('file_document')) {
            // Hapus file lama jika ada (opsional, untuk menghemat ruang)
            if ($archive->file_path && \Storage::disk('public')->exists($archive->file_path)) {
                \Storage::disk('public')->delete($archive->file_path);
            }
            
            // Simpan file baru
            $path = $request->file('file_document')->store('dokumen_arsip', 'public');
            $validated['file_path'] = $path;
        }

        $archive->update($validated);

        ActivityLog::record('Edit Arsip', "Memperbarui data arsip: {$archive->document_number}");

        return redirect()->route('archives.index')->with('success', 'Data arsip berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(Archive $archive)
    {
        // INTEGRASI: Cegah penghapusan jika berkas fisik sedang dipinjam
        if ($archive->status === 'Dipinjam') {
            return back()->with('error', "Gagal menghapus! Berkas fisik {$archive->document_number} sedang dipinjam dan belum dikembalikan ke rak.");
        }

        $archive->delete();
        ActivityLog::record('Hapus Arsip', "Memindahkan arsip ke daftar terhapus: {$archive->document_number}");

        return redirect()->route('archives.index')->with('success', 'Dokumen arsip berhasil dipindahkan ke daftar Arsip Terhapus.');
    }

    public function preview(Request $request, Archive $archive)
    {
        // 1. Logika Pembatasan Hak Akses
        if ($archive->access_level === 'Terbatas' && auth()->user()->role !== 'admin') {
            
            // Cek apakah ada izin yang "Approved" DAN masa berlakunya masih aktif
            $hasApprovedAccess = \App\Models\AccessRequest::where('user_id', auth()->id())
                                    ->where('archive_id', $archive->id)
                                    ->where('status', 'approved')
                                    ->where(function ($q) {
                                        $q->whereNull('expires_at')
                                          ->orWhere('expires_at', '>', now());
                                    })
                                    ->exists();

            // Jika belum disetujui ATAU sudah kedaluwarsa
            if (!$hasApprovedAccess) {
                ActivityLog::record('Akses Ditolak', "Karyawan mencoba membuka dokumen rahasia/kedaluwarsa: {$archive->document_number}");
                if ($request->ajax()) {
                    return response()->json(['message' => 'Akses ditolak atau masa berlaku telah habis.'], 403);
                }
                return back()->with('error', 'Akses ditolak! Izin membuka dokumen ini belum disetujui atau masa berlakunya telah habis.');
            }
        }

        // 2. Sensor Pelacak Berhasil
        ActivityLog::record('Lihat File', "Membuka dokumen arsip: {$archive->document_number}");

        return redirect(asset('storage/' . $archive->file_path));
    }
    // --- FITUR ARSIP TERHAPUS (PEMULIHAN & PEMUSNAHAN BERVERIFIKASI) ---
    public function trash(Request $request)
    {
        $query = Archive::onlyTrashed()->with(['category', 'location']);

        // 1. Logika Pencarian Teks (Nomor Dokumen & Judul)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('document_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        // 2. Logika Filter Kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 3. Logika Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $archives = $query->orderBy('deleted_at', 'desc')->paginate(10)->appends($request->query());
        $categories = Category::all();
        $totalTrashed = Archive::onlyTrashed()->count();

        return view('archives.trash', compact('archives', 'categories', 'totalTrashed'));
    }

    public function restore($id)
    {
        $archive = Archive::onlyTrashed()->findOrFail($id);
        $archive->restore();
        
        ActivityLog::record('Pulihkan Arsip', "Memulihkan arsip dari daftar terhapus: {$archive->document_number}");
        return redirect()->route('archives.trash')->with('success', "Dokumen {$archive->document_number} berhasil dipulihkan ke daftar aktif.");
    }

    public function bulkForceDelete(Request $request)
    {
        $request->validate([
            'admin_password' => 'required|string',
            'destroy_mode'   => 'required|in:single,selected,all',
        ]);

        // 1. Verifikasi Password Admin yang sedang login
        if (!Hash::check($request->admin_password, auth()->user()->password)) {
            ActivityLog::record('Gagal Musnahkan', 'Gagal memusnahkan arsip karena verifikasi password Admin salah.');
            return back()->with('error', 'Verifikasi gagal! Password Admin yang Anda masukkan tidak sesuai.');
        }

        // 2. Tentukan dokumen mana saja yang akan dimusnahkan
        if ($request->destroy_mode === 'all') {
            $archivesToDestroy = Archive::onlyTrashed()->get();
        } else {
            $ids = $request->input('selected_ids', []);
            if (empty($ids)) {
                return back()->with('error', 'Tidak ada dokumen arsip yang dipilih untuk dimusnahkan.');
            }
            $archivesToDestroy = Archive::onlyTrashed()->whereIn('id', $ids)->get();
        }

        if ($archivesToDestroy->isEmpty()) {
            return back()->with('error', 'Data arsip tidak ditemukan.');
        }

        $count = $archivesToDestroy->count();
        $docNumbers = $archivesToDestroy->pluck('document_number')->implode(', ');

        // 3. Hapus file fisik dari penyimpanan dan musnahkan dari database
        foreach ($archivesToDestroy as $archive) {
            if ($archive->file_path && Storage::disk('public')->exists($archive->file_path)) {
                Storage::disk('public')->delete($archive->file_path);
            }
            $archive->forceDelete();
        }

        // 4. Catat ke Audit Trail
        ActivityLog::record('Hapus Permanen', "Memusnahkan {$count} arsip secara permanen ({$docNumbers})");

        return redirect()->route('archives.trash')->with('success', "{$count} dokumen arsip beserta berkas fisiknya berhasil dimusnahkan secara permanen.");
    }

    // Fungsi Unduh Excel
    public function export() 
    {
        return Excel::download(new ArchivesExport, 'Laporan_Data_Arsip_'.date('Y-m-d').'.xlsx');
    }

    // Fungsi Unggah Excel
    public function import(Request $request) 
    {
        $request->validate([
            'import_file' => 'required|mimes:xlsx,xls,csv|max:10240' // Maks 10MB
        ]);

        Excel::import(new ArchivesImport, $request->file('import_file'));
        
        // Catat aktivitas Admin
        ActivityLog::record('Impor Massal', "Melakukan impor ratusan data arsip melalui fail Excel/CSV.");
        
        return back()->with('success', 'Ratusan data arsip berhasil diimpor ke dalam sistem!');
    }
}