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
|   - $title   → Judul halaman
|   - $siswa   → Instance model Siswa
|   - $tagihan → Collection tagihan yang sudah difilter
|   - $pembayaran → Collection pembayaran terkait (opsional)
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
                                    @php $totalTagihan = 0; @endphp
                                    @forelse ($tagihan as $item)
                                        @if($item->tagihanDetails && $item->tagihanDetails->isNotEmpty())
                                            @foreach($item->tagihanDetails as $detail)
                                                @php $totalTagihan += $detail->jumlah_biaya; @endphp
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
                                            <th>Jumlah</th>
                                            <th>Metode</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($pembayaran ?? [] as $p)
                                            <tr>
                                                <td><i class="fa fa-file-alt text-primary"></i></td>
                                                <td>{{ $p->tanggal_pembayaran->format('d/m/Y') }}</td>
                                                <td>{{ 'Rp ' . number_format($p->jumlah_dibayar, 0, ',', '.') }}</td>
                                                <td>{{ ucfirst($p->metode_pembayaran) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Belum ada pembayaran</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- === FORM PEMBAYARAN === --}}
                        <div class="mt-4">
                            <h6 class="fw-bold mb-3">Form Pembayaran Baru</h6>
                            {!! Form::open(['route' => 'pembayaran.store', 'method' => 'POST', 'class' => 'row g-3']) !!}
                                {!! Form::hidden('siswa_id', $siswa->id) !!}
                                {!! Form::hidden('wali_id', $siswa->wali_id) !!}

                                <div class="col-12 col-md-6">
                                    <label for="tanggal_pembayaran" class="form-label fw-semibold">Tanggal Pembayaran <span class="text-danger">*</span></label>
                                    {!! Form::date('tanggal_pembayaran', null, [
                                        'class' => 'form-control' . ($errors->has('tanggal_pembayaran') ? ' is-invalid' : ''),
                                        'id' => 'tanggal_pembayaran',
                                        'required'
                                    ]) !!}
                                    @error('tanggal_pembayaran')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="jumlah_dibayar" class="form-label fw-semibold">Jumlah Dibayar <span class="text-danger">*</span></label>
                                    {!! Form::text('jumlah_dibayar', null, [
                                        'class' => 'form-control rupiah' . ($errors->has('jumlah_dibayar') ? ' is-invalid' : ''),
                                        'id' => 'jumlah_dibayar',
                                        'placeholder' => 'Contoh: 225000',
                                        'required'
                                    ]) !!}
                                    @error('jumlah_dibayar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="metode_pembayaran" class="form-label fw-semibold">Metode Pembayaran <span class="text-danger">*</span></label>
                                    {!! Form::select('metode_pembayaran', [
                                        '' => '-- Pilih Metode --',
                                        'transfer' => 'Transfer Bank',
                                        'cash' => 'Tunai',
                                        'qris' => 'QRIS',
                                        'lainnya' => 'Lainnya'
                                    ], null, [
                                        'class' => 'form-select' . ($errors->has('metode_pembayaran') ? ' is-invalid' : ''),
                                        'id' => 'metode_pembayaran',
                                        'required'
                                    ]) !!}
                                    @error('metode_pembayaran')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save me-1"></i> Simpan Pembayaran
                                    </button>
                                </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>

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
                                                <th>Jumlah</th>
                                                <th>Metode</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pembayaran as $p)
                                                <tr>
                                                    <td>{{ $p->tanggal_pembayaran->format('d M Y') }}</td>
                                                    <td class="text-end">{{ 'Rp ' . number_format($p->jumlah_dibayar, 0, ',', '.') }}</td>
                                                    <td>{{ ucfirst($p->metode_pembayaran) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- === STATUS PER BULAN === --}}
                                @php
                                    $bulanDibayar = [];
                                    $nominalSPPPerBulan = 250000; // Sesuaikan dengan kebijakan sekolah
                                    foreach($pembayaran as $p) {
                                        if ($p->jumlah_dibayar >= $nominalSPPPerBulan) {
                                            $bulanDibayar[] = $p->tanggal_pembayaran->format('F Y');
                                        }
                                    }
                                    $bulanDibayar = array_unique($bulanDibayar);
                                @endphp

                                <div class="mb-3">
                                    <h6 class="fw-semibold">Status Bulanan:</h6>
                                    @if(count($bulanDibayar) > 0)
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($bulanDibayar as $bulan)
                                                <span class="badge bg-success rounded-pill px-3 py-2">
                                                    {{ $bulan }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="badge bg-warning text-dark">Belum ada pembayaran SPP</span>
                                    @endif
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
            </div>

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
        .no-print {
            display: none !important;
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
        // Tambahkan gaya khusus sebelum cetak
        const printContent = document.getElementById('kartu-spp-content').innerHTML;
        const originalContent = document.body.innerHTML;

        document.body.innerHTML = `
            <div style="padding: 20px; font-family: Arial, sans-serif;">
                ${printContent}
            </div>
        `;

        window.print();
        document.body.innerHTML = originalContent;
        location.reload(); // reload agar event listener & script kembali aktif
    }
</script>
@endpush
