{{--
|--------------------------------------------------------------------------
| VIEW: Wali - Daftar Tagihan SPP
|--------------------------------------------------------------------------
| Penulis     : Umar Ulkhak (Modifikasi oleh AI Assistant)
| Tujuan      : Menampilkan daftar tagihan SPP dengan UX baik & kode bersih
| Fitur       :
|   - Nomor urut per halaman (firstItem)
|   - Fallback aman untuk data kosong atau relasi null
|   - Badge warna otomatis berdasarkan kelas & status
|   - Format tanggal human-readable
|   - Pagination + info jumlah data
|   - Hover effect & responsif
|
| Variabel dari Controller:
|   - $tagihan → \Illuminate\Pagination\LengthAwarePaginator
--}}

@extends('layouts.app_sneat_wali')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Data Tagihan SPP</h5>
            </div>
            <div class="card-body">
                {{-- Tabel Data Tagihan --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="table-siswa">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 5%">#</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Tanggal Tagihan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tagihan as $index => $item)
                                <tr>
                                    {{-- Nomor urut konsisten per halaman --}}
                                    <td class="text-center fw-medium">
                                        {{ $tagihan->firstItem() ? $index + $tagihan->firstItem() : $loop->iteration }}
                                    </td>

                                    {{-- Nama Siswa --}}
                                    <td class="fw-semibold">
                                        {{ $item->siswa->nama ?? '—' }}
                                    </td>

                                    {{-- Kelas dengan badge warna --}}
                                    <td>
                                        @php
                                            $kelas = $item->siswa->kelas ?? '-';
                                            $badgeColor = match($kelas) {
                                                'VII' => 'success',
                                                'VIII' => 'warning',
                                                'IX' => 'danger',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge rounded-pill bg-label-{{ $badgeColor }}">
                                            {{ $kelas }}
                                        </span>
                                    </td>

                                    {{-- Tanggal Tagihan --}}
                                    <td>
                                        {{ \Carbon\Carbon::parse($item->tanggal_tagihan)->format('d M Y') }}
                                    </td>

                                    {{-- Status dengan badge --}}
                                    <td>
                                        @php
                                            $statusDisplay = $item->status_tagihan_wali ?? 'Belum diketahui';
                                            $statusColor = str_contains($statusDisplay, 'Sudah') ? 'success' : 'danger';
                                        @endphp
                                        <span class="badge bg-{{ $statusColor }} px-3 py-2">
                                            {{ $statusDisplay }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bx bx-empty fs-1 text-muted mb-2 d-block"></i>
                                        <p class="text-muted mb-0">Belum ada data tagihan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if(method_exists($tagihan, 'hasPages') && $tagihan->hasPages())
                    <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <small class="text-muted">
                            Menampilkan {{ $tagihan->firstItem() }}–{{ $tagihan->lastItem() }} dari {{ $tagihan->total() }} data
                        </small>
                        <div>
                            {!! $tagihan->links() !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Smooth hover effect */
    #table-siswa tbody tr:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s ease-in-out;
    }

    /* Improve badge vertical alignment */
    #table-siswa .badge {
        vertical-align: middle;
    }
</style>
@endpush
