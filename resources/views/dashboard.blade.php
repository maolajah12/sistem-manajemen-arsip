@extends('layouts.app')
@section('title', 'Dasbor Eksekutif - Sistem Pusat Arsip')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header Dasbor -->
    <div class="border-b border-stone-200 pb-4">
        <h2 class="text-2xl font-semibold tracking-tight text-stone-900">Dasbor Eksekutif</h2>
        <p class="text-sm text-stone-500 mt-1">Pantau metrik sirkulasi, komposisi dokumen, dan tren aktivitas sistem secara waktu nyata (real-time).</p>
    </div>

    <!-- 1. Baris Metrik Utama (Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-stone-200 shadow-sm flex flex-col">
            <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Arsip</span>
            <span class="text-3xl font-black text-stone-800 mt-2">{{ number_format($totalArchives) }}</span>
        </div>
        <div class="bg-white p-5 rounded-xl border border-stone-200 shadow-sm flex flex-col">
            <span class="text-xs font-bold text-teal-600 uppercase tracking-wider">Sedang Dipinjam</span>
            <span class="text-3xl font-black text-teal-700 mt-2">{{ number_format($activeLoans) }}</span>
        </div>
        <div class="bg-white p-5 rounded-xl border border-rose-200 shadow-sm flex flex-col bg-rose-50/30">
            <span class="text-xs font-bold text-rose-600 uppercase tracking-wider">Terlambat Kembali</span>
            <span class="text-3xl font-black text-rose-700 mt-2">{{ number_format($overdueLoans) }}</span>
        </div>
        <div class="bg-white p-5 rounded-xl border border-amber-200 shadow-sm flex flex-col bg-amber-50/30">
            <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Menunggu Izin</span>
            <span class="text-3xl font-black text-amber-700 mt-2">{{ number_format($pendingLoans) }}</span>
        </div>
    </div>

    <!-- 2. Baris Analitik Visual (Chart.js) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Grafik Doughnut: Kategori Arsip -->
        <div class="bg-white p-6 rounded-xl border border-stone-200 shadow-sm">
            <h3 class="text-sm font-bold text-stone-800 mb-4 uppercase tracking-wider">Komposisi Kategori Arsip</h3>
            <div class="relative h-64 w-full flex justify-center">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        <!-- Grafik Line: Tren Aktivitas -->
        <div class="bg-white p-6 rounded-xl border border-stone-200 shadow-sm">
            <h3 class="text-sm font-bold text-stone-800 mb-4 uppercase tracking-wider">Tren Aktivitas (7 Hari Terakhir)</h3>
            <div class="relative h-64 w-full">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- 3. Tabel Pemantauan Real-Time -->
    <div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-stone-200 bg-stone-50">
            <h3 class="text-sm font-bold text-stone-800 uppercase tracking-wider">Peminjaman Berjalan & Mendekati Tenggat</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200">
                <thead class="bg-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-stone-500 uppercase">Peminjam</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-stone-500 uppercase">Dokumen</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-stone-500 uppercase">Tenggat Waktu</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-stone-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 bg-white">
                    @forelse($borrowedDocs as $loan)
                    <tr class="hover:bg-stone-50/50">
                        <td class="px-6 py-3 text-sm font-bold text-stone-900">{{ $loan->user->username ?? '-' }}</td>
                        <td class="px-6 py-3 text-sm text-stone-600">{{ $loan->archive->document_number ?? '-' }}</td>
                        <td class="px-6 py-3 text-sm font-medium text-stone-700">
                            {{ $loan->return_deadline ? $loan->return_deadline->format('d M Y') : '-' }}
                        </td>
                        <td class="px-6 py-3 text-center">
                            @if($loan->return_deadline && $loan->return_deadline->isPast())
                                <span class="px-2 py-1 text-[10px] font-bold uppercase rounded-md bg-rose-100 text-rose-700">Terlambat</span>
                            @else
                                <span class="px-2 py-1 text-[10px] font-bold uppercase rounded-md bg-teal-50 text-teal-700">Berjalan</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-sm text-stone-500">Tidak ada dokumen fisik yang sedang dipinjam saat ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pustaka Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Skrip Konfigurasi & Inisialisasi Grafik -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // --- 1. Grafik Komposisi Kategori (Doughnut) ---
        const ctxCategory = document.getElementById('categoryChart').getContext('2d');
        new Chart(ctxCategory, {
            type: 'doughnut',
            data: {
                labels: @json($categoryLabels),
                datasets: [{
                    data: @json($categoryData),
                    backgroundColor: [
                        '#14b8a6', // Teal 500
                        '#f59e0b', // Amber 500
                        '#6366f1', // Indigo 500
                        '#ef4444', // Rose 500
                        '#8b5cf6', // Violet 500
                        '#64748b'  // Slate 500
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8, font: { size: 11, family: "'Inter', sans-serif" } } }
                },
                cutout: '65%'
            }
        });

        // --- 2. Grafik Tren Aktivitas (Line) ---
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: @json($trendLabels),
                datasets: [{
                    label: 'Aktivitas Sistem',
                    data: @json($trendData),
                    borderColor: '#292524', // Stone 900
                    backgroundColor: 'rgba(41, 37, 36, 0.05)',
                    borderWidth: 2,
                    pointBackgroundColor: '#14b8a6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 // Membuat garis melengkung halus
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [4, 4], color: '#f5f5f4' }, ticks: { stepSize: 1, font: { size: 11 } } },
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                }
            }
        });

    });
</script>
@endsection