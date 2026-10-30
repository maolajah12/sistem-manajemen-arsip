@extends('layouts.app')

@section('title', 'Catatan Aktivitas - Sistem Pusat Arsip')

@section('content')
<div class="max-w-7xl mx-auto">

    <div class="mb-6 border-b border-stone-200 pb-4">
        <h2 class="text-2xl font-semibold tracking-tight text-stone-900">Catatan Aktivitas (Audit Trail)</h2>
        <p class="text-sm text-stone-500 mt-1">Rekam jejak seluruh perubahan data dan interaksi pengguna di dalam sistem</p>
    </div>

    <div class="bg-white rounded-lg border border-stone-200 shadow-sm overflow-hidden mb-4">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200">
                <thead>
                    <tr class="bg-stone-50/80">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Pengguna</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Aksi</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Deskripsi Detail</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Alamat IP</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-stone-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-stone-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-500">
                            {{ $log->created_at->format('d M Y, H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-stone-900">
                            {{ $log->user ? $log->user->username : 'Sistem' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $bgColor = 'bg-stone-100 text-stone-700 border-stone-200';
                                if(str_contains(strtolower($log->action), 'tambah')) $bgColor = 'bg-teal-50 text-teal-700 border-teal-200';
                                if(str_contains(strtolower($log->action), 'hapus')) $bgColor = 'bg-rose-50 text-rose-700 border-rose-200';
                                if(str_contains(strtolower($log->action), 'edit')) $bgColor = 'bg-amber-50 text-amber-700 border-amber-200';
                            @endphp
                            <span class="px-2.5 py-1 inline-flex text-xs font-medium rounded-md border {{ $bgColor }}">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-stone-600">
                            {{ $log->description }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-400 font-mono">
                            {{ $log->ip_address }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-stone-500">Belum ada catatan aktivitas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>

</div>
@endsection