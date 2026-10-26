<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Archive;
use App\Models\Category;

class DashboardController extends Controller
{
    public function index()
    {
        // Menghitung ringkasan metrik utama
        $totalArchives = Archive::count();
        $borrowedArchives = Archive::where('status', 'Dipinjam')->count();
        $totalCategories = Category::count();
        
        // Menghitung distribusi arsip per kategori menggunakan Eloquent withCount
        $archivesPerCategory = Category::withCount('archives')->get();

        return view('dashboard', compact(
            'totalArchives', 
            'borrowedArchives', 
            'totalCategories', 
            'archivesPerCategory'
        ));
    }
}