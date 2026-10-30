@extends('layouts.app')
@section('title', 'Sirkulasi Peminjaman Fisik')

@section('content')
<div class="max-w-7xl mx-auto">

    <!-- Header tanpa tombol Ajukan Pinjaman Baru -->
    <div class="mb-6 border-b border-stone-200 pb-4">
        <h2 class="text-2xl font-semibold tracking-tight text-stone-900">Sirkulasi & Riwayat Peminjaman Fisik</h2>
        <p class="text-sm text-stone-500 mt-1">Lacak peminjaman map dokumen asli, tenggat waktu pengembalian, dan posisi berkas fisik.</p>
    </div>

    <!-- Ringkasan Statistik Sirkulasi -->
    @php
        $baseQuery = auth()->user()->role === 'admin' ? \App\Models\Loan::query() : \App\Models\Loan::where('user_id', auth()->id());
        $countPending  = (clone $baseQuery)->where('status', 'pending')->count();
        $countActive   = (clone $baseQuery)->where('status', 'approved')->count();
        $countOverdue  = (clone $baseQuery)->where('status', 'approved')->where('return_deadline', '<', now())->count();
        $countReturned = (clone $baseQuery)->where('status', 'returned')->count();
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg border border-stone-200 shadow-sm">
            <p class="text-xs font-semibold text-stone-500 uppercase">Menunggu Persetujuan</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $countPending }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg border border-stone-200 shadow-sm">
            <p class="text-xs font-semibold text-stone-500 uppercase">Sedang Dipinjam</p>
            <p class="text-2xl font-bold text-teal-700 mt-1">{{ $countActive }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg border border-stone-200 shadow-sm">
            <p class="text-xs font-semibold text-stone-500 uppercase">Melewati Tenggat (Terlambat)</p>
            <p class="text-2xl font-bold text-rose-600 mt-1">{{ $countOverdue }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg border border-stone-200 shadow-sm">
            <p class="text-xs font-semibold text-stone-500 uppercase">Telah Dikembalikan</p>
            <p class="text-2xl font-bold text-stone-800 mt-1">{{ $countReturned }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-teal-50 border border-teal-200 text-teal-800 px-4 py-3 rounded-md mb-6 text-sm shadow-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-lg border border-stone-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-stone-500 uppercase">Peminjam</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-stone-500 uppercase">Dokumen & Tujuan</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-stone-500 uppercase">Status & Tenggat</th>
                    @if(auth()->user()->role === 'admin')
                    <th class="px-6 py-4 text-center text-xs font-semibold text-stone-500 uppercase">Otorisasi Admin</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($loans as $loan)
                <tr class="hover:bg-stone-50/50">
                    <td class="px-6 py-4 text-sm font-bold text-stone-900">
                        {{ $loan->user->username ?? 'Akun Terhapus' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-stone-600">
                        <span class="font-bold text-stone-900">{{ $loan->archive->document_number ?? 'Dokumen Terhapus' }}</span>
                        <span class="text-xs text-stone-500">— {{ $loan->archive->title ?? '' }}</span><br>
                        <span class="text-xs text-stone-500">Tujuan: {{ $loan->reason }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($loan->status === 'pending')
                            <span class="px-2.5 py-1 text-xs font-semibold bg-amber-50 text-amber-700 rounded-md border border-amber-200">Menunggu Persetujuan Admin</span>
                        @elseif($loan->status === 'approved')
                            @if($loan->return_deadline && $loan->return_deadline->isPast())
                                <span class="px-2.5 py-1 text-xs font-semibold bg-rose-100 text-rose-700 rounded-md border border-rose-300">Terlambat (Lewat Tenggat)</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold bg-teal-50 text-teal-700 rounded-md border border-teal-200">Dipinjam (Berjalan)</span>
                            @endif
                            <div class="text-xs text-stone-500 mt-1">Wajib Kembali: <strong>{{ $loan->return_deadline->format('d M Y') }}</strong></div>
                        @elseif($loan->status === 'returned')
                            <span class="px-2.5 py-1 text-xs font-semibold bg-stone-100 text-stone-600 rounded-md border border-stone-200">Sudah Dikembalikan ke Rak</span>
                            <div class="text-xs text-stone-500 mt-1">Pada: {{ $loan->returned_at->format('d M Y, H:i') }}</div>
                        @else
                            <span class="px-2.5 py-1 text-xs font-semibold bg-rose-50 text-rose-600 rounded-md border border-rose-200">Permohonan Ditolak</span>
                        @endif
                    </td>

                    @if(auth()->user()->role === 'admin')
                    <td class="px-6 py-4 text-center">
                        @if($loan->status === 'pending')
                            <div class="flex justify-center items-center gap-2">
                                <form action="{{ route('loans.approve', $loan->id) }}" method="POST" class="flex items-center gap-1">
                                    @csrf
                                    <select name="duration_days" class="text-xs border border-stone-300 rounded-md px-2 py-1.5 bg-white text-stone-700">
                                        <option value="1">1 Hari</option>
                                        <option value="3" selected>3 Hari</option>
                                        <option value="7">7 Hari</option>
                                        <option value="14">14 Hari</option>
                                    </select>
                                    <button type="submit" class="bg-teal-50 text-teal-700 border border-teal-200 px-3 py-1.5 rounded-md text-xs font-semibold hover:bg-teal-100">Setujui</button>
                                </form>
                                <form action="{{ route('loans.reject', $loan->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-rose-50 text-rose-700 border border-rose-200 px-3 py-1.5 rounded-md text-xs font-semibold hover:bg-rose-100">Tolak</button>
                                </form>
                            </div>
                        @elseif($loan->status === 'approved')
                            <form action="{{ route('loans.return', $loan->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-stone-900 text-white px-3.5 py-1.5 rounded-md text-xs font-semibold hover:bg-stone-800 shadow-sm">
                                    Terima Pengembalian Fisik
                                </button>
                            </form>
                        @else
                            <span class="text-xs text-stone-400">Selesai</span>
                        @endif
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->role === 'admin' ? 4 : 3 }}" class="px-6 py-8 text-center text-sm text-stone-500">
                        Belum ada riwayat peminjaman fisik. Silakan ajukan melalui menu <strong>Data Arsip</strong>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $loans->links() }}</div>
</div>


@endsection