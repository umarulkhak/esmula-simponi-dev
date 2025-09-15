{{--
|--------------------------------------------------------------------------
| VIEW: Wali - Daftar Tagihan Sekolah (Tanpa Accordion)
|--------------------------------------------------------------------------
| Penulis     : Umar Ulkhak + AI Assistant
| Tujuan      : Menampilkan daftar tagihan per siswa tanpa accordion
| Fitur       :
|   - Tampilan sederhana & rapi
|   - Nama, kelas, jumlah tagihan, status, tombol aksi
|   - Tombol "Bayar" → arahkan ke halaman show semua tagihan siswa
|   - Tombol "Lihat Riwayat" jika sudah lunas semua
|
| Variabel dari Controller:
|   - $tagihanGrouped → \Illuminate\Support\Collection (groupBy siswa_id)
--}}

@extends('layouts.app_sneat_wali')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                {{-- Header Card --}}
                <div class="card-header bg-white d-flex justify-content-between align-items-center px-5 py-4">
                    <div>
                        <h4 class="mb-0 fw-bold">
                            Tagihan Sekolah
                        </h4>
                        <small class="text-muted">Klik tombol untuk melihat atau bayar tagihan</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bx bx-info-circle fs-5 text-muted"></i>
                        <small class="text-muted">Total {{ $tagihanGrouped->count() ?? 0 }} siswa</small>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if ($tagihanGrouped->isEmpty())
                        <div class="text-center py-5">
                            <i class="bx bx-empty fs-1 text-muted mb-3"></i>
                            <h5 class="text-muted fw-normal">Belum ada tagihan tersedia</h5>
                            <p class="text-muted small">Silakan cek kembali nanti atau hubungi operator sekolah.</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach ($tagihanGrouped as $siswaId => $tagihanList)
                                @php
                                    $siswa = $tagihanList->first()->siswa;
                                    $kelas = $siswa->kelas ?? '-';
                                    $accentColor = match($kelas) {
                                        'VII' => '#28a745',
                                        'VIII' => '#fd7e14',
                                        'IX' => '#6f42c1',
                                        default => '#6c757d'
                                    };

                                    $totalTagihan = $tagihanList->count();
                                    $sudahDibayar = $tagihanList->where('status', 'lunas')->count();
                                    $statusPembayaran = $sudahDibayar == $totalTagihan ? 'Lunas' : ($sudahDibayar > 0 ? 'Angsur' : 'Belum Dibayar');
                                    $statusColor = $sudahDibayar == $totalTagihan ? 'success' : ($sudahDibayar > 0 ? 'warning' : 'danger');
                                @endphp

                                {{-- ITEM PER SISWA --}}
                                <div class="list-group-item px-4 py-4 d-flex justify-content-between align-items-center"
                                     style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                                    <div class="d-flex align-items-center gap-3">
                                        {{-- Icon User --}}
                                        <div class="rounded-circle bg-white border p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-color: {{ $accentColor }};">
                                            <i class="fas fa-user" style="color: {{ $accentColor }};"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $siswa->nama ?? 'Tanpa Nama' }}</div>
                                            <small class="text-muted">
                                                <span class="badge rounded-pill" style="background: {{ $accentColor }}; color: white; font-size: 0.75rem; font-weight: 500;">
                                                    Kelas {{ $kelas }}
                                                </span>
                                            </small>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center gap-3 ms-auto">
                                        {{-- STATUS --}}
                                        <span class="badge badge-status bg-{{ $statusColor }} text-white">
                                            <i class="bx bx-{{ $statusColor == 'success' ? 'check-circle' : ($statusColor == 'warning' ? 'history' : 'error-circle') }}"></i>
                                            {{ $statusPembayaran }}
                                        </span>

                                        {{-- JUMLAH TAGIHAN --}}
                                        <span class="text-muted small">
                                            {{ $totalTagihan }} tagihan
                                        </span>

                                        {{-- TOMBOL AKSI --}}
                                        @if ($sudahDibayar == $totalTagihan)
                                            <a href="{{ route('wali.tagihan.show', $siswaId) }}"
                                               class="btn btn-sm px-3 py-1"
                                               style="background: #6c757d; color: white; border-radius: 50px; font-weight: 500;"
                                               title="Lihat riwayat pembayaran {{ $siswa->nama ?? 'siswa ini' }}">
                                                <i class="bx bx-history me-1"></i> Lihat
                                            </a>
                                        @else
                                            <a href="{{ route('wali.tagihan.show', $siswaId) }}"
                                               class="btn btn-sm px-3 py-1"
                                               style="background: {{ $accentColor }}; color: white; border-radius: 50px; font-weight: 500;"
                                               title="Bayar tagihan untuk {{ $siswa->nama ?? 'siswa ini' }}">
                                                <i class="bx bx-credit-card me-1"></i> Bayar
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Status Badge - Rapi & Simetris */
    .badge-status {
        font-size: 0.85rem;
        font-weight: 600;
        padding: 0.45rem 1.2rem;
        border-radius: 20px;
        min-width: 130px;
        text-align: center;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .badge-status.success {
        background-color: #28a745 !important;
        color: white !important;
    }
    .badge-status.warning {
        background-color: #fd7e14 !important;
        color: white !important;
    }
    .badge-status.danger {
        background-color: #dc3545 !important;
        color: white !important;
    }

    .badge-status:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .badge-status i {
        font-size: 0.9rem;
        margin-right: 0.2rem;
    }

    /* Tombol */
    .list-group-item .btn {
        white-space: nowrap;
    }

    /* Spacing */
    .list-group-item {
        transition: background-color 0.2s ease;
    }
    .list-group-item:hover {
        background-color: rgba(0,0,0,0.02);
    }
</style>
@endpush
