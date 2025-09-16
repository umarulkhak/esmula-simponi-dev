@extends('layouts.app_sneat_wali')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden" style="background: #ffffff; box-shadow: 0 10px 30px rgba(0,0,0,0.06);">
            {{-- Header --}}
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
                        // Hitung status global
                        $lunasCount = $tagihanList->where('status', 'lunas')->count();
                        $totalTagihan = $tagihanList->count();
                        $grandTotal = $tagihanList->sum(fn($t) => $t->tagihanDetails->sum('jumlah_biaya'));
                        $statusGlobal = $lunasCount == $totalTagihan ? 'lunas' : ($lunasCount > 0 ? 'angsur' : 'belum_bayar');
                    @endphp

                    {{-- Status Global --}}
                    <div class="alert alert-{{ $statusGlobal == 'lunas' ? 'success' : ($statusGlobal == 'angsur' ? 'warning' : 'danger') }} d-flex align-items-center mb-4" role="alert">
                        <i class="bx bx-{{ $statusGlobal == 'lunas' ? 'check-circle' : ($statusGlobal == 'angsur' ? 'history' : 'error-circle') }} fs-4 me-3"></i>
                        <div>
                            <strong>Status Pembayaran:</strong>
                            @if ($statusGlobal == 'lunas')
                                <span class="fw-bold">Lunas Semua</span>
                            @elseif ($statusGlobal == 'angsur')
                                <span class="fw-bold">Masih Angsur</span> — Sisa tagihan: <strong class="text-danger">{{ formatRupiah($grandTotal - $totalDibayar) }}</strong>
                            @else
                                <span class="fw-bold">Belum Bayar</span> — Total tagihan: <strong class="text-danger">{{ formatRupiah($grandTotal) }}</strong>
                            @endif
                        </div>
                    </div>

                    {{-- Tabel Tagihan --}}
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
                                        <td class="text-end fw-medium text-success">{{ formatRupiah($subtotal) }}</td>
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

                    {{-- TAMPILKAN UCAPAN TERIMA KASIH JIKA LUNAS --}}
                    @if ($statusGlobal == 'lunas')
                        <div class="bg-success-light text-dark rounded-3 p-4 mt-4 d-flex align-items-start">
                            <i class="bx bx-check-circle fs-4 me-3 text-success"></i>
                            <div>
                                <h5 class="mb-2 fw-bold">🎉 Terima Kasih!</h5>
                                <p class="mb-0">
                                    Semua tagihan untuk <strong>{{ $siswa->nama }}</strong> telah lunas.
                                    <br>Terima kasih atas kepercayaan dan kerja sama Anda dalam mendukung pendidikan anak.
                                </p>
                            </div>
                        </div>
                    @else
                        {{-- TAMPILKAN REKENING BANK JIKA BELUM LUNAS --}}
                        <div class="bg-light border border-info rounded-3 p-4 mt-4 mb-0">
                            <p class="mb-3 text-muted">
                                Pembayaran bisa dilakukan dengan cara langsung ke Operator sekolah atau transfer melalui rekening milik sekolah di bawah ini.
                                Jangan melakukan transfer ke rekening selain dari rekening di bawah ini.
                            </p>

                            <div class="d-flex flex-wrap gap-3">
                                @foreach ($banksekolah as $bank)
                                    <div class="flex-grow-1 min-w-250px bg-white border border-gray-200 rounded-3 p-4 shadow-sm">
                                        <div class="mb-3">
                                            <strong class="text-muted">Bank Tujuan</strong>
                                            <p class="mb-0">{{ $bank->nama_bank }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <strong class="text-muted">Nomor Rekening</strong>
                                            <p class="mb-0">{{ $bank->nomor_rekening }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <strong class="text-muted">Atas Nama</strong>
                                            <p class="mb-0">{{ $bank->nama_rekening }}</p>
                                        </div>
                                        <a href="#" class="btn btn-primary w-100 rounded-pill">
                                            Konfirmasi Pembayaran
                                        </a>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4">
                                <ul class="mb-2">
                                    <li><a href="#" class="text-decoration-none text-primary">Lihat Cara Pembayaran Melalui ATM</a></li>
                                    <li><a href="#" class="text-decoration-none text-primary">Lihat Cara Pembayaran Melalui Internet Banking</a></li>
                                </ul>
                                <p class="text-muted small">
                                    Setelah melakukan pembayaran, silakan upload bukti pembayaran melalui tombol konfirmasi yang ada di bawah ini.
                                </p>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    body {
        font-family: 'Inter', sans-serif;
    }

    .card {
        max-width: 800px;
        margin: 0 auto;
    }

    .table thead th {
        font-weight: 500;
        font-size: 0.9rem;
        color: #6c757d;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa !important;
    }

    .btn-outline-primary {
        border-color: #6c757d;
        color: #6c757d;
    }

    .btn-outline-primary:hover {
        background-color: #6c757d;
        color: white;
    }

    /* Soft green for thank you card */
    .bg-success-light {
        background-color: #e8f5e8 !important;
        border: 1px solid #c8e6c9 !important;
        color: #2e7d32 !important;
        border-radius: 12px;
        padding: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }

    .bg-success-light:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.08);
    }

    /* Mobile Responsive */
    @media (max-width: 576px) {
        .card-body { padding: 1.5rem; }
        .table thead th { font-size: 0.85rem; }
        .flex-grow-1 { min-width: 100%; }
    }
</style>
@endpush
