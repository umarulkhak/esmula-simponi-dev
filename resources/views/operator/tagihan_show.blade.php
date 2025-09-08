{{--
|--------------------------------------------------------------------------
| VIEW: Operator - Detail Tagihan Siswa
|--------------------------------------------------------------------------
| Penulis     : Umar Ulkhak
| Tujuan      : Menampilkan detail tagihan per siswa dengan layout dua kolom.
| Fitur       :
|   - Foto & info siswa di atas (foto kiri, info kanan)
|   - Tombol kembali di card info siswa
|   - Tombol cetak kartu SPP di card Kartu SPP
|   - Data tagihan & form pembayaran di kolom kiri
|   - Kartu SPP di kolom kanan
|   - Responsive di desktop & mobile
|   - Clean Code: struktur blade rapi, komentar jelas
|
| Variabel yang diharapkan dari Controller:
|   - $title         → Judul halaman
|   - $siswa         → Instance model Siswa
|   - $tagihan       → Collection tagihan yang sudah difilter
|   - $pembayaran    → Collection pembayaran terkait
|   - $tagihanDefault → Tagihan default (untuk hidden field)
|
--}}

@extends('layouts.app_sneat')

@section('content')
<div class="row">
    <div class="col-12">

        {{-- === SECTION: INFO SISWA === --}}
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex flex-column flex-md-row align-items-start gap-4 position-relative">

                    {{-- Tombol Kembali --}}
                    <div class="position-absolute top-0 end-0 me-3 mt-3">
                        <a href="{{ route('tagihan.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fa fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>

                    {{-- Foto Siswa (Kiri) --}}
                    <div class="flex-shrink-0 text-center">
                        @php
                            $fotoPath = ($siswa->foto && \Storage::exists($siswa->foto))
                                ? \Storage::url($siswa->foto)
                                : asset('images/no-image.png');
                        @endphp
                        <img
                            src="{{ $fotoPath }}"
                            alt="Foto {{ $siswa->nama }}"
                            class="rounded shadow-sm border"
                            style="width: 150px; height: 180px; object-fit: cover; object-position: center;">
                    </div>

                    {{-- Detail Siswa (Kanan) --}}
                    <div class="flex-grow-1 w-100">
                        <h6 class="fw-bold mb-3">
                            <i class="fa fa-user me-2"></i>
                            Informasi Siswa
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold text-muted" style="width: 30%;">Nama</td>
                                        <td>{{ $siswa->nama ?? '–' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">NISN</td>
                                        <td>{{ $siswa->nisn ?? '–' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Kelas</td>
                                        <td>
                                            <span class="badge bg-label-{{ $siswa->kelas == 'VII' ? 'success' : ($siswa->kelas == 'VIII' ? 'warning' : 'danger') }} rounded-pill px-2 py-1">
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
        </div>

        {{-- === SECTION: BAWAH (DUA KOLOM 50:50) === --}}
        <div class="row">

            {{-- === KOLOM KIRI: DATA TAGIHAN & PEMBAYARAN === --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Data Tagihan</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Tagihan</th>
                                        <th>Jumlah Tagihan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalTagihan = 0;
                                        $jumlahDibayar = 0;
                                    @endphp
                                    @forelse ($tagihan as $item)
                                        @if($item->tagihanDetails && $item->tagihanDetails->isNotEmpty())
                                            @foreach($item->tagihanDetails as $detail)
                                                @php
                                                    $totalTagihan += $detail->jumlah_biaya;
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->parent->iteration }}.{{ $loop->iteration }}</td>
                                                    <td>{{ $detail->nama_biaya ?? '–' }}</td>
                                                    <td>{{ 'Rp ' . number_format($detail->jumlah_biaya, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Tidak ada data tagihan</td>
                                        </tr>
                                    @endforelse
                                    <tr class="fw-bold">
                                        <td colspan="2">Total Tagihan</td>
                                        <td>{{ 'Rp ' . number_format($totalTagihan, 0, ',', '.') }}</td>
                                    </tr>

                                    {{-- ✅ HITUNG TOTAL PEMBAYARAN --}}
                                    @php
                                        $jumlahDibayar = $pembayaran->sum('jumlah_dibayar');
                                        $sisaTagihan = max(0, $totalTagihan - $jumlahDibayar);
                                    @endphp

                                    <tr class="fw-bold text-danger">
                                        <td colspan="2">Sisa Tagihan</td>
                                        <td>{{ 'Rp ' . number_format($sisaTagihan, 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- === DATA PEMBAYARAN === --}}
                        <div class="mt-4">
                            <h6 class="fw-bold mb-3">Riwayat Pembayaran</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Tanggal</th>
                                            <th>Nama Tagihan</th>
                                            <th>Jumlah</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($pembayaran ?? [] as $p)
                                            @php
                                                $tagihan = $p->tagihan;
                                                $details = $tagihan->tagihanDetails;
                                                $totalTagihanItem = $details->sum('jumlah_biaya');
                                                $sisa = $totalTagihanItem - $p->jumlah_dibayar;
                                                $status = $sisa <= 0 ? 'Lunas' : 'Belum Lunas';
                                            @endphp
                                            @foreach($details as $detail)
                                                <tr>
                                                    <td><i class="fa fa-file-alt text-primary"></i></td>
                                                    <td>{{ $p->tanggal_bayar ? $p->tanggal_bayar->format('d/m/Y') : '–' }}</td>
                                                    <td>{{ $detail->nama_biaya ?? '–' }}</td>
                                                    <td>{{ 'Rp ' . number_format($p->jumlah_dibayar, 0, ',', '.') }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $status == 'Lunas' ? 'success' : 'warning' }} text-dark">
                                                            {{ $status }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Belum ada pembayaran</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- === FORM PEMBAYARAN (SIMPLE) === --}}
                        <div class="mt-4">
                            <h6 class="fw-bold mb-3">Form Pembayaran Baru</h6>
                            {!! Form::open(['route' => 'pembayaran.store', 'method' => 'POST', 'class' => 'row g-3']) !!}
                                {!! Form::hidden('siswa_id', $siswa->id) !!}
                                {!! Form::hidden('wali_id', $siswa->wali_id) !!}

                                {{-- ✅ Kirim tagihan_id pertama sebagai acuan — DENGAN NULL-SAFE --}}
                                @if($tagihanDefault && $tagihanDefault->id)
                                    {!! Form::hidden('tagihan_id', $tagihanDefault->id) !!}
                                @else
                                    <div class="col-12">
                                        <div class="alert alert-warning">
                                            <i class="fa fa-exclamation-triangle me-1"></i>
                                            Siswa ini belum memiliki tagihan. Tidak bisa melakukan pembayaran.
                                        </div>
                                    </div>
                                @endif

                                <div class="col-12 col-md-6">
                                    <label for="tanggal_bayar" class="form-label fw-semibold">Tanggal Pembayaran <span class="text-danger">*</span></label>
                                    {!! Form::date('tanggal_bayar', old('tanggal_bayar') ?? now()->toDateString(), [
                                        'class' => 'form-control' . ($errors->has('tanggal_bayar') ? ' is-invalid' : ''),
                                        'id' => 'tanggal_bayar',
                                        'required'
                                    ]) !!}
                                    @error('tanggal_bayar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="jumlah_dibayar" class="form-label fw-semibold">Jumlah Dibayar <span class="text-danger">*</span></label>
                                    {!! Form::text('jumlah_dibayar', old('jumlah_dibayar'), [
                                        'class' => 'form-control rupiah' . ($errors->has('jumlah_dibayar') ? ' is-invalid' : ''),
                                        'id' => 'jumlah_dibayar',
                                        'placeholder' => 'Contoh: 50000',
                                        'required'
                                    ]) !!}
                                    @error('jumlah_dibayar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if($tagihanDefault && $tagihanDefault->id)
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save me-1"></i> Simpan Pembayaran
                                        </button>
                                    </div>
                                @else
                                    <div class="col-12">
                                        <button type="button" class="btn btn-secondary" disabled>
                                            <i class="fa fa-ban me-1"></i> Tidak Ada Tagihan
                                        </button>
                                    </div>
                                @endif
                            {!! Form::close() !!}
                        </div> {{-- .mt-4 --}}
                    </div> {{-- .card-body --}}
                </div> {{-- .card --}}
            </div> {{-- .col-md-6 (KOLOM KIRI SELESAI) --}}

            {{-- === KOLOM KANAN: KARTU SPP + BUTTON CETAK === --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Kartu SPP</h5>
                        <button
                            type="button"
                            class="btn btn-outline-primary btn-sm"
                            onclick="cetakKartuSPP()"
                        >
                            <i class="fa fa-print me-1"></i> Cetak Kartu SPP
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="kartu-spp-content" class="bg-light p-4 rounded">
                            <div class="text-center mb-4">
                                <i class="fa fa-credit-card fa-2x text-primary"></i>
                                <h6 class="mt-2 fw-bold">KARTU PEMBAYARAN SPP</h6>
                            </div>

                            <div class="text-start mb-4">
                                <p class="mb-1"><strong>Nama:</strong> {{ $siswa->nama ?? '–' }}</p>
                                <p class="mb-1"><strong>NISN:</strong> {{ $siswa->nisn ?? '–' }}</p>
                                <p class="mb-1"><strong>Kelas:</strong> {{ $siswa->kelas ?? '–' }}</p>
                                <p class="mb-1"><strong>Angkatan:</strong> {{ $siswa->angkatan ?? '–' }}</p>
                            </div>

                            <hr>

                            <h6 class="fw-bold mb-3 text-center">Riwayat Pembayaran</h6>

                            @if(isset($pembayaran) && $pembayaran->isNotEmpty())
                                <div class="table-responsive mb-3">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Nama Tagihan</th>
                                                <th>Jumlah</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pembayaran as $p)
                                                @php
                                                    $tagihan = $p->tagihan;
                                                    $details = $tagihan->tagihanDetails;
                                                    $totalTagihanItem = $details->sum('jumlah_biaya');
                                                    $sisa = $totalTagihanItem - $p->jumlah_dibayar;
                                                    $status = $sisa <= 0 ? 'Lunas' : 'Belum Lunas';
                                                @endphp
                                                @foreach($details as $detail)
                                                    <tr>
                                                        <td>{{ $p->tanggal_bayar ? $p->tanggal_bayar->format('d M Y') : '–' }}</td>
                                                        <td>{{ $detail->nama_biaya ?? '–' }}</td>
                                                        <td class="text-end">{{ 'Rp ' . number_format($p->jumlah_dibayar, 0, ',', '.') }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $status == 'Lunas' ? 'success' : 'warning' }} text-dark">
                                                                {{ $status }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <i class="fa fa-exclamation-circle text-muted fa-lg mb-2"></i>
                                    <p class="mb-0 text-muted">Belum ada riwayat pembayaran.</p>
                                </div>
                            @endif

                            <hr class="my-3">

                            <div class="text-center mt-3">
                                <p class="mb-0">Mengetahui,</p>
                                <p class="mt-4">_______________________</p>
                                <p class="mb-0 fw-semibold">Bendahara Sekolah</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div> {{-- .col-md-6 (KOLOM KANAN SELESAI) --}}

        </div> {{-- END ROW BAWAH --}}

    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        margin-bottom: 1rem;
    }
    .card-header {
        background-color: #f8f9fa;
        padding: 0.75rem 1.25rem;
        border-bottom: 1px solid rgba(0,0,0,.05);
    }
    .card-body {
        padding: 1.25rem;
    }
    .rupiah {
        direction: rtl;
        text-align: right;
    }
    .table-bordered th,
    .table-bordered td {
        border: 1px solid #dee2e6;
    }

    /* Gaya khusus saat print */
    @media print {
        body * {
            visibility: hidden;
        }
        #kartu-spp-content, #kartu-spp-content * {
            visibility: visible;
        }
        #kartu-spp-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 20px;
            font-size: 14px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Rupiah Masking
        $('.rupiah').mask("#.##0", {
            reverse: true
        });
    });

    function cetakKartuSPP() {
        const printContent = document.getElementById('kartu-spp-content').innerHTML;
        const originalContent = document.body.innerHTML;

        document.body.innerHTML = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Cetak Kartu SPP</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    table { width: 100%; border-collapse: collapse; margin: 10px 0; }
                    td, th { border: 1px solid #ddd; padding: 8px; }
                    .text-end { text-align: right; }
                </style>
            </head>
            <body>
                ${printContent}
            </body>
            </html>
        `;

        window.print();
        document.body.innerHTML = originalContent;
        location.reload(); // Reload agar event listener kembali aktif
    }
</script>
@endpush
