<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Archive;   // Tambahkan baris ini
use App\Models\Category;  // Tambahkan baris ini
use App\Models\Location;  // Tambahkan baris ini

class ArchiveController extends Controller
{
    public function index(Request $request)
{
    // Mulai kerangka query dasar beserta relasinya
    $query = Archive::with(['category', 'location']);

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

    // Eksekusi query dengan pengurutan ID mutlak dan Pagination
    // appends() berfungsi agar filter tidak hilang saat pindah halaman
    $archives = $query->orderBy('id', 'desc')->paginate(10)->appends($request->query());
    
    // Tarik master data kategori untuk mengisi opsi dropdown filter
    $categories = Category::all();

    return view('archives.index', compact('archives', 'categories'));
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
        ]);

        Archive::create($validated);

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
        // Pengecualian ID agar nomor dokumen yang sama tetap bisa disimpan jika tidak diubah
        'document_number' => 'required|unique:archives,document_number,' . $archive->id,
        'title' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'location_id' => 'required|exists:locations,id',
        'retention_date' => 'nullable|date',
        'status' => 'required|in:Tersedia,Dipinjam,Kedaluwarsa',
    ]);

    $archive->update($validated);

    return redirect()->route('archives.index')->with('success', 'Data arsip berhasil diperbarui!');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Archive $archive)
{
    $archive->delete(); // Laravel akan menggunakan SoftDeletes sesuai migration awal kita
    
    return redirect()->route('archives.index')->with('success', 'Data arsip berhasil dihapus dari sistem.');
}
}
