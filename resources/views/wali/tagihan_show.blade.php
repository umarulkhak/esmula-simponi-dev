{{--
|--------------------------------------------------------------------------
| VIEW: Wali - Detail Tagihan Siswa (Mobile Optimized)
|--------------------------------------------------------------------------
| Penulis     : Umar Ulkhak
| Tujuan      : Menampilkan daftar tagihan per siswa dengan desain mobile-first
| Fitur       :
|   - Header info siswa responsif
|   - Tabel tagihan horizontal scrollable di mobile
|   - Tombol cetak invoice jika lunas
|   - Rekening bank + modal konfirmasi pembayaran
|   - ✅ TEMA DARK: warna primary #2A363B
|
| Variabel dari Controller:
|   - $siswa → object siswa
|   - $tagihanList → \Illuminate\Support\Collection tagihan
|   - $banksekolah → \Illuminate\Support\Collection rekening bank
|   - $grandTotal → total tagihan keseluruhan
|   - $totalDibayar → total pembayaran yang sudah dilakukan
|   - $statusGlobal → 'lunas', 'angsur', atau 'belum_bayar'
--}}

@extends('layouts.app_sneat_wali')

@section('content')

@include('wali.modals.modal_cara_bayar_atm')


<div class="col-12">
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden" style="background: #ffffff; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">

        {{-- Header Info Siswa --}}
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row align-items-start gap-4 position-relative">

                {{-- Tombol Kembali --}}
                <div class="position-absolute top-0 end-0 me-2 mt-1 me-md-3 mt-md-2">
                    <a href="{{ route('wali.tagihan.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                        <i class="bx bx-arrow-back"></i>
                    </a>
                </div>

                {{-- Foto Siswa --}}
                <div class="flex-shrink-0 text-center mb-3 mb-md-0">
                    @php
                        $fotoPath = ($siswa->foto && \Storage::exists($siswa->foto))
                            ? \Storage::url($siswa->foto)
                            : asset('images/no-image.png');
                    @endphp
                    <img
                        src="{{ $fotoPath }}"
                        alt="Foto {{ $siswa->nama }}"
                        class="rounded shadow-sm border"
                        style="width: 100px; height: 120px; object-fit: cover;"
                        onerror="this.src='{{ asset('images/no-image.png') }}'">
                </div>

                {{-- Detail Siswa --}}
                <div class="flex-grow-1 w-100">
                    <h6 class="fw-bold mb-2 fs-6 fs-md-5">
                        <i class="bx bx-user-circle me-1"></i> Informasi Siswa
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0 fs-7 fs-md-6">
                            <tbody>
                                <tr>
                                    <td class="fw-semibold text-muted" style="width: 35%;">Nama</td>
                                    <td>{{ $siswa->nama ?? '–' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">NISN</td>
                                    <td>{{ $siswa->nisn ?? '–' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">Kelas</td>
                                    <td>
                                        <span class="badge bg-label-{{ $siswa->kelas == 'VII' ? 'success' : ($siswa->kelas == 'VIII' ? 'warning' : 'danger') }} rounded-pill px-2 py-1 fs-7">
                                            {{ $siswa->kelas }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">Angkatan</td>
                                    <td>{{ $siswa->angkatan ?? '–' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Jika tidak ada tagihan --}}
        @if ($tagihanList->isEmpty())
            <div class="text-center py-5 px-3">
                <i class="bx bx-empty fs-1 text-muted mb-3"></i>
                <h5 class="text-muted fw-normal fs-5">Belum ada tagihan tersedia</h5>
                <p class="text-muted small">Silakan cek kembali nanti atau hubungi operator sekolah.</p>
            </div>
        @else
            {{-- Status Global --}}
            <div class="alert alert-{{ $statusGlobal == 'lunas' ? 'success' : ($statusGlobal == 'angsur' ? 'warning' : 'danger') }} d-flex align-items-center mb-3 mx-3 mx-md-4 p-3" role="alert">
                <i class="bx bx-{{ $statusGlobal == 'lunas' ? 'check-circle' : ($statusGlobal == 'angsur' ? 'history' : 'error-circle') }} fs-4 me-2 me-md-3"></i>
                <div class="fs-7 fs-md-6">
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
            <div class="table-responsive mx-3 mx-md-4 mb-3">
                <table class="table table-hover align-middle mb-0 fs-7">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 5%;">#</th>
                            <th>Tanggal</th>
                            <th>Rincian</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-center" style="width: 15%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tagihanList as $index => $tagihan)
                            @php
                                $subtotal = $tagihan->tagihanDetails->sum('jumlah_biaya');
                            @endphp
                            <tr class="border-bottom">
                                <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                <td class="align-middle">{{ \Carbon\Carbon::parse($tagihan->tanggal_tagihan)->translatedFormat('d M Y') }}</td>
                                <td class="align-middle">
                                    <ul class="mb-0 ps-3" style="font-size: 0.85rem; line-height: 1.3;">
                                        @forelse ($tagihan->tagihanDetails as $detail)
                                            <li>{{ $detail->nama_biaya ?? '–' }}</li>
                                        @empty
                                            <li class="text-muted">Tidak ada rincian</li>
                                        @endforelse
                                    </ul>
                                </td>
                                <td class="text-end fw-medium {{ $tagihan->status == 'lunas' ? 'text-success' : 'text-danger' }} align-middle">
                                    {{ formatRupiah($subtotal) }}
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge bg-{{ $tagihan->status == 'lunas' ? 'success' : 'danger' }} px-2 py-1 rounded-pill fs-7">
                                        {{ $tagihan->status_tagihan_wali }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Jika Lunas — Tampilkan ucapan & tombol cetak --}}
            @if ($statusGlobal == 'lunas')
                <div class="bg-success-light text-dark rounded-3 p-3 p-md-4 mt-3 mx-3 mx-md-4 d-flex align-items-start">
                    <i class="bx bx-check-circle fs-4 me-2 me-md-3 text-success flex-shrink-0"></i>
                    <div>
                        <h5 class="mb-2 fw-bold fs-6">🎉 Terima Kasih!</h5>
                        <p class="mb-0 fs-7">
                            Semua tagihan untuk <strong>{{ $siswa->nama }}</strong> telah lunas.
                            <br>Terima kasih atas kepercayaan Anda.
                        </p>
                    </div>
                </div>

                <div class="text-center my-3 mx-3 mx-md-4">
                    <button type="button" class="btn btn-dark w-100 w-md-auto px-4 py-2 fs-6" onclick="cetakInvoice()">
                        <i class="bx bx-printer me-2"></i> Cetak Invoice
                    </button>
                </div>
            @else
                {{-- Rekening Bank --}}
                <div class="bg-light border border-info rounded-3 p-3 p-md-4 mt-3 mx-3 mx-md-4 mb-4">
                    <p class="mb-3 text-muted fs-7">
                        Pembayaran bisa dilakukan langsung ke sekolah atau transfer ke rekening di bawah ini.
                    </p>

                    <div class="row g-3">
                        @foreach ($banksekolah as $bank)
                            <div class="col-12">
                                <div class="bg-white border rounded-3 p-3 shadow-sm">
                                    <div class="mb-2">
                                        <strong class="text-muted fs-7">Bank Tujuan</strong>
                                        <p class="mb-0 fs-6">{{ $bank->nama_bank }}</p>
                                    </div>
                                    <div class="mb-2">
                                        <strong class="text-muted fs-7">Nomor Rekening</strong>
                                        <p class="mb-0 fs-6">{{ $bank->nomor_rekening }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <strong class="text-muted fs-7">Atas Nama</strong>
                                        <p class="mb-0 fs-6">{{ $bank->nama_rekening }}</p>
                                    </div>
                                    {{-- ✅ Perbaikan: Ambil tagihan pertama untuk data-tagihan-id --}}
                                    <a href="{{ route('wali.pembayaran.create', [
                                            'tagihan_id' => $tagihanList->first()->id,
                                            'bank_id' => $bank->id
                                        ]) }}"
                                    class="btn btn-dark w-100 fs-6">
                                        Konfirmasi Pembayaran
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3">
                        <ul class="mb-2 small">
                            <li>
                                <a href="#" class="text-decoration-none text-primary fs-7" data-bs-toggle="modal" data-bs-target="#modalCaraBayarATM">
                                    Cara Bayar via ATM
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-decoration-none text-primary fs-7">Cara Bayar via Internet Banking</a>
                            </li>
                        </ul>
                        <p class="text-muted small fs-7">
                            Upload bukti pembayaran setelah transfer.
                        </p>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>

@endsection

@push('styles')
<style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f8f9fa;
    }

    .card {
        margin: 0 auto;
    }

    .table thead th {
        font-weight: 500;
        font-size: 0.85rem;
        color: #6c757d;
        white-space: nowrap;
    }

    .table td {
        vertical-align: middle;
    }

    .btn-outline-secondary {
        border-color: #6c757d;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: white;
    }

    .bg-success-light {
        background-color: #e8f5e8 !important;
        border: 1px solid #c8e6c9 !important;
        color: #2e7d32 !important;
        border-radius: 12px;
        transition: all 0.2s ease;
    }

    .btn-dark {
        background-color: #2A363B;
        border-color: #2A363B;
        color: #ffffff;
    }

    .btn-dark:hover {
        background-color: #1E2C2F;
        border-color: #1E2C2F;
    }

    .fs-5 { font-size: 1.1rem; }
    .fs-6 { font-size: 1rem; }
    .fs-7 { font-size: 0.9rem; }

    @media (max-width: 576px) {
        .card { margin: 0 8px; }
        .p-3 { padding: 1rem !important; }
        .mx-3 { margin-left: 1rem !important; margin-right: 1rem !important; }
        .fs-5 { font-size: 1rem; }
        .fs-6 { font-size: 0.95rem; }
        .fs-7 { font-size: 0.875rem; }
        img[style*="width: 100px"] {
            width: 90px !important;
            height: 110px !important;
        }
        .table thead th { font-size: 0.8rem; }
        .badge { font-size: 0.75rem; padding: 0.35em 0.5em; }
    }

    @media print {
        body * { visibility: hidden; }
        #print-area, #print-area * { visibility: visible; }
        #print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .no-print { display: none !important; }
        table { page-break-inside: avoid; }
        tr { page-break-inside: avoid; }
    }
</style>
@endpush

@push('scripts')
<script>
    function cetakInvoice() {
        const namaSiswa = "{{ $siswa->nama }}";
        const nisn = "{{ $siswa->nisn ?? '–' }}";
        const kelas = "{{ $siswa->kelas ?? '–' }}";
        const angkatan = "{{ $siswa->angkatan ?? '–' }}";
        const tanggalCetak = new Date().toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        let detailTagihan = '';
        let grandTotal = {{ $grandTotal }}; // ✅ Ambil dari controller, bukan hitung ulang

        @foreach ($tagihanList as $tagihan)
            @php
                $subtotal = $tagihan->tagihanDetails->sum('jumlah_biaya');
            @endphp
            detailTagihan += `
            <tr>
                <td>{{ \Carbon\Carbon::parse($tagihan->tanggal_tagihan)->translatedFormat('d F Y') }}</td>
                <td>
                    <ul style="margin: 0; padding-left: 20px; font-size: 14px;">
                        @foreach ($tagihan->tagihanDetails as $detail)
                            <li>{{ $detail->nama_biaya ?? '–' }}</li>
                        @endforeach
                    </ul>
                </td>
                <td class="text-end">{{ formatRupiah($subtotal) }}</td>
            </tr>`;
        @endforeach

        const printContent = `
            <div id="print-area">
                <div class="text-center mb-4">
                    <h2 style="font-size: 22px; margin-bottom: 4px;">INVOICE PEMBAYARAN</h2>
                    <p style="margin: 0; font-size: 16px;">Sekolah Menengah Pertama Negeri 1 Contoh</p>
                    <p style="margin: 0; font-size: 14px; color: #666;">Dicetak pada: ${tanggalCetak}</p>
                </div>

                <div class="mb-4">
                    <h5 style="font-size: 18px; margin-bottom: 12px;">Informasi Siswa</h5>
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <tr><td style="width: 30%; padding: 6px 0;"><strong>Nama</strong></td><td style="padding: 6px 0;">: ${namaSiswa}</td></tr>
                        <tr><td style="padding: 6px 0;"><strong>NISN</strong></td><td style="padding: 6px 0;">: ${nisn}</td></tr>
                        <tr><td style="padding: 6px 0;"><strong>Kelas</strong></td><td style="padding: 6px 0;">: ${kelas}</td></tr>
                        <tr><td style="padding: 6px 0;"><strong>Angkatan</strong></td><td style="padding: 6px 0;">: ${angkatan}</td></tr>
                    </table>
                </div>

                <div class="mb-4">
                    <h5 style="font-size: 18px; margin-bottom: 12px;">Detail Tagihan</h5>
                    <table style="width: 100%; border: 1px solid #ddd; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="background-color: #f8f9fa;">
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Tanggal Tagihan</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Rincian Biaya</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${detailTagihan}
                            <tr style="font-weight: bold;">
                                <td colspan="2" style="border: 1px solid #ddd; padding: 8px; text-align: right;">TOTAL PEMBAYARAN</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ formatRupiah($grandTotal) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="text-center mt-5">
                    <p style="margin: 0; font-size: 14px;">Hormat Kami,</p>
                    <p style="margin-top: 40px; margin-bottom: 4px; font-size: 14px;">_______________________</p>
                    <p style="margin: 0; font-weight: 600; font-size: 14px;">Bendahara Sekolah</p>
                </div>
            </div>
        `;

        const originalContent = document.body.innerHTML;
        document.body.innerHTML = printContent;
        window.print();
        document.body.innerHTML = originalContent;
        setTimeout(() => window.location.reload(), 100);
    }

    // Modal handler
    document.addEventListener('DOMContentLoaded', function() {
        const pembayaranModal = document.getElementById('pembayaranModal');

        pembayaranModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const tagihanId = button.getAttribute('data-tagihan-id');
            const bankId = button.getAttribute('data-bank-id');

            pembayaranModal.querySelector('#modal_tagihan_id').value = tagihanId;
            pembayaranModal.querySelector('#modal_bank_id').value = bankId;
        });
    });
</script>
@endpush
