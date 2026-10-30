<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Loan;
use App\Models\ActivityLog; // Pastikan model ini sesuai dengan yang Anda gunakan
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Kartu Metrik Ringkasan
        $totalArchives = Archive::count();
        $activeLoans = Loan::where('status', 'approved')->count();
        $overdueLoans = Loan::where('status', 'approved')->where('return_deadline', '<', now())->count();
        $pendingLoans = Loan::where('status', 'pending')->count();

        // 2. Data Grafik 1: Jumlah Arsip per Kategori
        $categories = Category::withCount('archives')->get();
        $categoryLabels = $categories->pluck('name');
        $categoryData = $categories->pluck('archives_count');

        // 3. Data Grafik 2: Tren Aktivitas/Sistem 7 Hari Terakhir
        $trendLabels = collect();
        $trendData = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $trendLabels->push($date->format('d M'));
            
            // Menghitung jumlah log aktivitas pada hari tersebut
            // (Bisa diganti dengan Archive::whereDate(...) jika ingin tren penambahan arsip)
            $count = ActivityLog::whereDate('created_at', $date->format('Y-m-d'))->count();
            $trendData->push($count);
        }

        // 4. Tabel Real-Time: Dokumen Sedang Dipinjam
        $borrowedDocs = Loan::with(['archive', 'user'])
            ->where('status', 'approved')
            ->orderBy('return_deadline', 'asc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalArchives', 'activeLoans', 'overdueLoans', 'pendingLoans',
            'categoryLabels', 'categoryData',
            'trendLabels', 'trendData',
            'borrowedDocs'
        ));
    }
}