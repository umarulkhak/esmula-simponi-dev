@extends('layouts.app_sneat_wali')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden" style="background: #ffffff; box-shadow: 0 10px 30px rgba(0,0,0,0.06);">
                {{-- Header Card --}}
                <div class="card-body p-5 pb-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h5 class="mb-1 fw-bold text-dark">Daftar Tagihan Sekolah</h5>
                            <p class="mb-0 text-muted small">
                                {{ $siswa->nama }} - {{ $siswa->kelas ?? '—' }}
                            </p>
                        </div>
                        <a href="{{ route('wali.tagihan.index') }}" class="btn btn-outline-primary btn-sm px-3 py-1 rounded-pill">
                            <i class="bx bx-arrow-back me-1"></i> Kembali
                        </a>
                    </div>

                    @if ($tagihanList->isEmpty())
                        <div class="text-center py-5">
                            <i class="bx bx-empty fs-1 text-muted mb-3"></i>
                            <h5 class="text-muted fw-normal">Belum ada tagihan tersedia</h5>
                            <p class="text-muted small">Silakan cek kembali nanti atau hubungi operator sekolah.</p>
                        </div>
                    @else
                        @php
                            $lunasCount = $tagihanList->where('status', 'lunas')->count();
                            $totalTagihan = $tagihanList->count();
                            $grandTotal = 0;
                            $totalDibayar = 0; // Jika nanti mau hitung total yang sudah dibayar

                            // Hitung grand total semua tagihan
                            foreach ($tagihanList as $tagihan) {
                                $grandTotal += $tagihan->tagihanDetails->sum('jumlah_biaya');
                            }

                            // Tentukan status global
                            if ($lunasCount == $totalTagihan) {
                                $statusGlobal = 'lunas';
                            } elseif ($lunasCount > 0) {
                                $statusGlobal = 'angsur';
                            } else {
                                $statusGlobal = 'belum_bayar';
                            }
                        @endphp

                        {{-- STATUS GLOBAL SISWA --}}
                        <div class="alert alert-{{ $statusGlobal == 'lunas' ? 'success' : ($statusGlobal == 'angsur' ? 'warning' : 'danger') }} d-flex align-items-center mb-4" role="alert">
                            <i class="bx bx-{{ $statusGlobal == 'lunas' ? 'check-circle' : ($statusGlobal == 'angsur' ? 'history' : 'error-circle') }} fs-4 me-3"></i>
                            <div>
                                <strong>Status Pembayaran:</strong>
                                @if ($statusGlobal == 'lunas')
                                    <span class="fw-bold">Lunas Semua</span>
                                @elseif ($statusGlobal == 'angsur')
                                    <span class="fw-bold">Masih Angsur</span> — Sisa tagihan: <strong class="text-danger">{{ formatRupiah($grandTotal - $totalDibayar) }}</strong>
                                    {{-- Catatan: $totalDibayar belum dihitung, bisa diabaikan dulu atau dihitung dari relasi pembayaran --}}
                                @else
                                    <span class="fw-bold">Belum Bayar</span> — Total tagihan: <strong class="text-danger">{{ formatRupiah($grandTotal) }}</strong>
                                @endif
                            </div>
                        </div>

                        {{-- TABEL UTAMA: SEMUA TAGIHAN --}}
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 5%;">#</th>
                                        <th>Tanggal Tagihan</th>
                                        <th>Rincian Biaya</th>
                                        <th class="text-end">Subtotal</th>
                                        <th class="text-center" style="width: 15%;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tagihanList as $index => $tagihan)
                                        @php
                                            $subtotal = $tagihan->tagihanDetails->sum('jumlah_biaya');
                                        @endphp
                                        <tr class="border-bottom">
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ \Carbon\Carbon::parse($tagihan->tanggal_tagihan)->translatedFormat('d F Y') }}</td>
                                            <td>
                                                <ul class="mb-0 ps-3" style="font-size: 0.9rem; line-height: 1.4;">
                                                    @forelse ($tagihan->tagihanDetails as $detail)
                                                        <li>{{ $detail->nama_biaya ?? '–' }}</li>
                                                    @empty
                                                        <li class="text-muted">Tidak ada rincian</li>
                                                    @endforelse
                                                </ul>
                                            </td>
                                            <td class="text-end fw-medium text-success">
                                                {{ formatRupiah($subtotal) }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $tagihan->status == 'lunas' ? 'success' : 'danger' }} px-3 py-1 rounded-pill">
                                                    {{ $tagihan->status_tagihan_wali }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- ✅ INFORMASI CARA BAYAR — SELALU TAMPIL --}}
                        <div class="bg-light border border-info rounded-3 p-4 mt-4 mb-0">
                            <div class="d-flex align-items-start">
                                <i class="bx bx-info-circle fs-4 text-info me-3 mt-1"></i>
                                <div>
                                    <h6 class="fw-bold text-info mb-2">📌 Cara Pembayaran</h6>
                                    <ul class="mb-2" style="font-size: 0.95rem; line-height: 1.5;">
                                        <li>Transfer ke rekening bank sekolah</li>
                                        <li>Bayar langsung ke operator sekolah</li>
                                    </ul>
                                    <p class="mb-0 text-muted small">
                                        <i class="bx bx-time-five me-1"></i>
                                        Lakukan pembayaran sebelum jatuh tempo.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Font & Spacing */
    body {
        font-family: 'Inter', sans-serif;
    }

    .card {
        max-width: 800px;
        margin: 0 auto;
    }

    .card-body {
        padding: 2rem;
    }

    h3 {
        font-size: 1.5rem;
    }

    h5, h6 {
        font-size: 1.1rem;
    }

    .table thead th {
        font-weight: 500;
        font-size: 0.9rem;
        color: #6c757d;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa !important;
    }

    .table td {
        padding: 0.75rem 0;
    }

    .text-success {
        font-weight: 500;
    }

    .btn-outline-primary {
        border-color: #6c757d;
        color: #6c757d;
    }

    .btn-outline-primary:hover {
        background-color: #6c757d;
        color: white;
    }

    /* Badge Status */
    .badge {
        font-weight: 500;
        padding: 0.4rem 0.8rem;
    }

    /* Alert Status Global */
    .alert {
        border-radius: 12px;
        padding: 1rem 1.5rem;
    }

    /* Mobile Optimization */
    @media (max-width: 576px) {
        .card-body {
            padding: 1.5rem;
        }

        h3 {
            font-size: 1.3rem;
        }

        .table thead th {
            font-size: 0.85rem;
        }

        .text-success {
            font-size: 0.95rem;
        }

        .bg-light {
            padding: 1rem !important;
        }

        .alert {
            font-size: 0.9rem;
            padding: 0.75rem 1rem;
        }
    }
</style>
@endpush
