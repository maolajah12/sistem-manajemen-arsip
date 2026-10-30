<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index()
    {
        // Menampilkan log terbaru, 50 data per halaman
        $logs = ActivityLog::with('user')->latest()->paginate(50);
        return view('logs.index', compact('logs'));
    }
}