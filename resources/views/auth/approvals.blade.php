@extends('layouts.app')

@section('title', 'Persetujuan Akun - Sistem Pusat Arsip')

@section('content')
<div class="max-w-7xl mx-auto">

    <div class="mb-6 border-b border-stone-200 pb-4">
        <h2 class="text-2xl font-semibold tracking-tight text-stone-900">Manajemen & Persetujuan Akun</h2>
        <p class="text-sm text-stone-500 mt-1">Verifikasi permintaan pendaftaran akun karyawan dan admin baru</p>
    </div>

    @if(session('success'))
        <div class="bg-teal-50 border border-teal-200 text-teal-800 px-4 py-3 rounded-md mb-6 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-md mb-6 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Tabel Menunggu Persetujuan -->
    <div class="bg-white rounded-lg border border-stone-200 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-stone-200 bg-amber-50/60 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-stone-800">Menunggu Persetujuan ({{ $pendingUsers->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200">
                <thead>
                    <tr class="bg-stone-50/80">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Peran Diminta</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Waktu Daftar</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold text-stone-500 uppercase tracking-wider">Keputusan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-stone-100">
                    @forelse($pendingUsers as $user)
                    <tr class="hover:bg-stone-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-stone-900">{{ $user->username }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 inline-flex text-xs font-medium rounded-md {{ $user->role === 'admin' ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-700 border border-stone-200' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-500">{{ $user->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center gap-2">
                                <form action="{{ route('approvals.approve', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-teal-600 text-white px-3 py-1.5 rounded-md text-xs font-medium hover:bg-teal-700 transition-colors shadow-sm">
                                        Setujui
                                    </button>
                                </form>
                                <form action="{{ route('approvals.reject', $user->id) }}" method="POST" onsubmit="return confirm('Tolak dan hapus pendaftaran akun ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-white border border-rose-200 text-rose-600 px-3 py-1.5 rounded-md text-xs font-medium hover:bg-rose-50 transition-colors">
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-sm text-stone-500">Tidak ada permintaan pendaftaran baru saat ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- SEKSI BARU: PERMOHONAN AKSES DOKUMEN TERBATAS -->
    <div class="mb-8">
        @php
            // Menarik data permohonan akses yang berstatus 'pending' beserta relasinya
            $pendingAccessRequests = \App\Models\AccessRequest::with(['user', 'archive'])->where('status', 'pending')->latest()->get();
        @endphp
        
        <h3 class="text-lg font-bold text-stone-800 mb-3">
            Permohonan Akses Dokumen Terbatas ({{ $pendingAccessRequests->count() }})
        </h3>
        
        <div class="bg-white rounded-lg border border-stone-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200">
                    <thead>
                        <tr class="bg-stone-50/80">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Karyawan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Dokumen</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Alasan Keperluan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Waktu Pengajuan</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-stone-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-stone-100">
                        @forelse($pendingAccessRequests as $req)
                        <tr class="hover:bg-stone-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-stone-900">
                                {{ $req->user->username }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-600">
                                <span class="font-bold text-stone-800">{{ $req->archive->document_number }}</span><br>
                                <span class="text-xs text-stone-500">{{ $req->archive->title }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-stone-600 max-w-xs">
                                <p class="truncate" title="{{ $req->reason }}">{{ $req->reason }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-500">
                                {{ $req->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('access-requests.approve', $req->id) }}" method="POST" class="flex items-center gap-2">
    @csrf
    <select name="duration" required class="text-xs border border-stone-300 rounded-md px-2 py-1.5 bg-white text-stone-700 focus:outline-none focus:ring-1 focus:ring-teal-600">
        <option value="1_hour">1 Jam</option>
        <option value="1_day" selected>1 Hari</option>
        <option value="3_days">3 Hari</option>
        <option value="permanent">Permanen</option>
    </select>
    <button type="submit" class="text-teal-700 bg-teal-50 hover:bg-teal-100 border border-teal-200 px-3 py-1.5 rounded-md text-xs font-semibold transition-colors shadow-sm focus:outline-none">
        Setujui
    </button>
</form>
                                    <form action="{{ route('access-requests.reject', $req->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 px-3 py-1.5 rounded-md text-xs font-semibold transition-colors shadow-sm focus:outline-none">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-stone-500">
                                Tidak ada permohonan akses dokumen saat ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tabel Akun Aktif -->
    <div class="bg-white rounded-lg border border-stone-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-stone-200 bg-stone-50/50">
            <h3 class="text-sm font-semibold text-stone-800">Daftar Akun Aktif ({{ $approvedUsers->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200">
                <thead>
                    <tr class="bg-stone-50/80">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Peran</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold text-stone-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold text-stone-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-stone-100">
                    @foreach($approvedUsers as $user)
                    <tr class="hover:bg-stone-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-stone-900">
                            {{ $user->username }}
                            @if($user->username === 'Maulana321')
                                <span class="ml-2 text-xs text-amber-600 font-semibold">(Admin Utama)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 inline-flex text-xs font-medium rounded-md {{ $user->role === 'admin' ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-700 border border-stone-200' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-2.5 py-1 inline-flex text-xs font-medium rounded-md bg-teal-50 text-teal-700 border border-teal-200">Aktif</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            @if($user->username !== 'Maulana321')
                                <form action="{{ route('approvals.reject', $user->id) }}" method="POST" onsubmit="return confirm('Hapus akses pengguna ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-500 hover:text-rose-700 text-xs font-medium">Cabut Akses</button>
                                </form>
                            @else
                                <span class="text-xs text-stone-400">Permanen</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection