{{-- resources/views/operator/pembayaran_show.blade.php --}}
@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bx bx-credit-card me-2"></i>Detail Pembayaran
                </h5>
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bx bx-arrow-back me-1"></i>Kembali
                </a>
            </div>

            <div class="card-body">
                <!-- === 1. RINGKASAN PEMBAYARAN === -->
                <div class="row g-4 mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header fw-semibold">
                                <i class="bx bx-receipt me-1"></i>Ringkasan Pembayaran
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2">
                                                <span class="fw-medium text-muted">Metode</span><br>
                                                <span>{{ $model->metode_pembayaran ?? '–' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="fw-medium text-muted">Tanggal Bayar</span><br>
                                                <span>
                                                    {{ $model->tanggal_bayar ? \Carbon\Carbon::parse($model->tanggal_bayar)->locale('id')->isoFormat('D MMMM Y') : '–' }}
                                                </span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="fw-medium text-muted">Jumlah Dibayar</span><br>
                                                <span class="text-success fw-bold">{{ formatRupiah($model->jumlah_dibayar ?? 0) }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="fw-medium text-muted">Total Tagihan</span><br>
                                                <span class="text-danger fw-bold">
                                                    {{ formatRupiah($model->tagihan?->tagihanDetails?->sum('jumlah_biaya') ?? 0) }}
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2">
                                                <span class="fw-medium text-muted">Status Konfirmasi</span><br>
                                                @if($model->status_konfirmasi === 'sudah')
                                                    <span class="badge bg-success">Sudah</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Belum</span>
                                                @endif
                                            </li>
                                            <li class="mb-2">
                                                <span class="fw-medium text-muted">Status Pembayaran</span><br>
                                                @php
                                                    $statusPembayaran = $model->tagihan?->getStatusTagihanWaliAttribute();
                                                @endphp
                                                @if($statusPembayaran === 'Sudah Dibayar')
                                                    <span class="badge bg-success">Sudah Dibayar</span>
                                                @elseif($statusPembayaran === 'Angsur')
                                                    <span class="badge bg-info text-dark">Angsur</span>
                                                @else
                                                    <span class="badge bg-danger">Belum Dibayar</span>
                                                @endif
                                            </li>
                                            <li class="mb-2">
                                                <span class="fw-medium text-muted">Bukti Pembayaran</span><br>
                                                @if(!empty($model->bukti_bayar))
                                                    <a
                                                        href="javascript:void(0)"
                                                        onclick="popupCenter({
                                                            url: '{{ \Storage::url($model->bukti_bayar) }}',
                                                            title: 'Bukti Pembayaran',
                                                            w: 900,
                                                            h: 700
                                                        })"
                                                    >
                                                        Lihat Bukti Bayar
                                                    </a>
                                                @else
                                                    <span class="text-muted">Tidak tersedia</span>
                                                @endif
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- === 2. DETAIL REKENING (GABUNG JADI SATU CARD) === -->
                <div class="row g-4 mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header fw-semibold">
                                <i class="bx bx-detail me-1"></i>Detail Rekening
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <h6 class="fw-semibold mb-2">
                                            <i class="bx bx-send me-1"></i>Rekening Pengirim
                                        </h6>
                                        <p class="mb-1"><span class="fw-medium">Bank:</span> {{ $model->waliBank?->nama_bank ?? '–' }}</p>
                                        <p class="mb-1"><span class="fw-medium">No. Rek:</span> {{ $model->waliBank?->nomor_rekening ?? '–' }}</p>
                                        <p class="mb-0"><span class="fw-medium">Atas Nama:</span> {{ $model->waliBank?->nama_rekening ?? '–' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-semibold mb-2">
                                            <i class="bx bx-wallet me-1"></i>Rekening Sekolah (Penerima)
                                        </h6>
                                        <p class="mb-1"><span class="fw-medium">Bank:</span> {{ $model->bankSekolah?->nama_bank ?? '–' }}</p>
                                        <p class="mb-1"><span class="fw-medium">No. Rek:</span> {{ $model->bankSekolah?->nomor_rekening ?? '–' }}</p>
                                        <p class="mb-0"><span class="fw-medium">Atas Nama:</span> {{ $model->bankSekolah?->nama_rekening ?? '–' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- === 3. TAGIHAN & SISWA === -->
                <div class="row g-4">
                    <!-- Informasi Tagihan -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header fw-semibold">
                                <i class="bx bx-file me-1"></i>Rincian Tagihan
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><span class="fw-medium">ID Pembayaran:</span> {{ $model->id }}</p>
                                <p class="mb-3"><span class="fw-medium">ID Tagihan:</span> {{ $model->tagihan_id }}</p>

                                <h6 class="fw-semibold mb-2"><i class="bx bx-list-ul me-1"></i>Item Biaya</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Biaya</th>
                                                <th class="text-end">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($model->tagihan?->tagihanDetails ?? [] as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item->nama_biaya }}</td>
                                                    <td class="text-end">{{ formatRupiah($item->jumlah_biaya) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted fst-italic">Tidak ada item</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Siswa -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header fw-semibold">
                                <i class="bx bx-user me-1"></i>Data Siswa
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><span class="fw-medium">Nama:</span> {{ $model->tagihan?->siswa?->nama ?? '–' }}</p>
                                <p class="mb-2"><span class="fw-medium">NIS/NISN:</span> {{ $model->tagihan?->siswa?->nisn ?? '–' }}</p>
                                <p class="mb-2"><span class="fw-medium">Kelas:</span> {{ $model->tagihan?->siswa?->kelas ?? '–' }}</p>
                                <p class="mb-3"><span class="fw-medium">Wali Murid:</span> {{ $model->tagihan?->siswa?->wali?->name ?? '–' }}</p>
                                <!-- TOMBOL DIHAPUS DARI SINI -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- === TOMBOL KONFIRMASI DI LUAR CARD === -->
                {!! Form::open([
                    'route' => $route,
                    'method' => 'PUT',
                    'onsubmit' => 'return confirm("Apakah anda yakin?")',
                    ]) !!}

                    {!! Form::hidden('pembayaran_id', $model->id, []) !!}

                    {!! Form::submit('Konfirmasi Pembayaran', ['class' => 'btn btn-primary']) !!}

                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>
@endsection
