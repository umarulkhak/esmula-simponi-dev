@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            {{-- 1. HEADER: INFORMASI SISWA --}}
            <div class="card-header bg-primary text-white">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
                    <div>
                        @if($siswa)
                            <h5 class="mb-1 fw-bold">
                                {{ $siswa->nama }}
                            </h5>
                            <div class="d-flex flex-wrap gap-3">
                                <span><i class="bx bx-id-card me-1"></i> NISN: {{ $siswa->nisn }}</span>
                                <span><i class="bx bx-school me-1"></i> Kelas: {{ $siswa->kelas }}</span>
                                <span><i class="bx bx-user-circle me-1"></i> Wali: {{ $siswa->wali?->name ?? '–' }}</span>
                            </div>
                        @else
                            <h5 class="mb-1 fw-bold">Siswa Tidak Ditemukan</h5>
                            <div class="text-muted">Data siswa tidak tersedia.</div>
                        @endif
                    </div>
                    <a href="{{ route('pembayaran.index') }}" class="btn btn-light btn-sm">
                        <i class="bx bx-arrow-back me-1"></i>Kembali
                    </a>
                </div>
            </div>

            <div class="card-body">

                {{-- 2. STATUS PEMBAYARAN SISWA --}}
                <div class="row g-4 mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header fw-semibold">
                                <i class="bx bx-bar-chart-alt-2 me-1"></i>Status Pembayaran Siswa
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <div class="bg-light rounded p-3">
                                            <div class="text-muted">Total Tagihan</div>
                                            <div class="fw-bold text-danger">{{ formatRupiah($totalTagihan ?? 0) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <div class="bg-light rounded p-3">
                                            <div class="text-muted">Sudah Dibayar</div>
                                            <div class="fw-bold text-success">{{ formatRupiah($totalDibayar ?? 0) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="bg-light rounded p-3">
                                            <div class="text-muted">Status</div>
                                            @php
                                                $statusPembayaran = 'Belum Dibayar';
                                                if ($siswa) {
                                                    $belumLunas = $siswa->tagihan()->where('status', '!=', 'lunas')->count();
                                                    $totalTagihanSiswa = $siswa->tagihan()->count();
                                                    if ($totalTagihanSiswa > 0) {
                                                        $statusPembayaran = $belumLunas == 0 ? 'Sudah Dibayar' : 'Angsur';
                                                    }
                                                }
                                            @endphp
                                            @if($statusPembayaran === 'Sudah Dibayar')
                                                <span class="badge bg-success px-3 py-2">Lunas</span>
                                            @elseif($statusPembayaran === 'Angsur')
                                                <span class="badge bg-warning text-dark px-3 py-2">Angsur</span>
                                            @else
                                                <span class="badge bg-danger px-3 py-2">Belum Bayar</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. DAFTAR PEMBAYARAN --}}
                <div class="row g-4 mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                                <span><i class="bx bx-list-ul me-1"></i>Daftar Pembayaran</span>
                            </div>
                            <div class="card-body">
                                @if ($pembayaranGroup->contains(fn($p) => $p->status_konfirmasi == 'belum'))
                                    {!! Form::open(['route' => 'pembayaran.update.multiple', 'method' => 'PUT']) !!}
                                    @csrf
                                @endif

                                <div class="table-responsive">
                                    <table class="table table-striped table-hover align-middle">
                                        <thead class="table-light">
                                                <tr>
                                                    <th style="width: 5%; text-align: center;">#</th>
                                                    <th style="width: 10%;">Tagihan ID</th>
                                                    <th style="width: 12%;">Tanggal Bayar</th>
                                                    <th style="width: 10%;">Metode</th>
                                                    <th style="width: 10%;" class="text-end">Jumlah</th>
                                                    <th style="width: 20%;">Rekening Pengirim</th>
                                                    <th style="width: 20%;">Rekening Sekolah</th>
                                                    <th style="width: 8%;">Bukti Bayar</th>
                                                    <th style="width: 8%;">Status</th>
                                                </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pembayaranGroup as $pembayaran)
                                                <tr>
                                                    <!-- Kolom # (Checkbox / Icon) -->
                                                    <td style="text-align: center; width: 5%;">
                                                        @if ($pembayaran->status_konfirmasi == 'belum')
                                                            <input type="checkbox" name="pembayaran_ids[]" value="{{ $pembayaran->id }}" class="pembayaran-checkbox">
                                                        @else
                                                            <i class="bx bx-check-circle text-success"></i>
                                                        @endif
                                                    </td>

                                                    <!-- Kolom Tagihan ID -->
                                                    <td style="width: 10%;">{{ $pembayaran->tagihan_id }}</td>

                                                    <!-- Kolom Tanggal Bayar -->
                                                    <td style="width: 12%;">{{ $pembayaran->tanggal_bayar ? \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->translatedFormat('d M Y') : '–' }}</td>

                                                    <!-- Kolom Metode -->
                                                    <td style="width: 10%;">{{ $pembayaran->metode_pembayaran }}</td>

                                                    <!-- Kolom Jumlah -->
                                                    <td style="width: 10%;" class="text-end">{{ formatRupiah($pembayaran->jumlah_dibayar) }}</td>

                                                    <!-- Kolom Rekening Pengirim -->
                                                    <td style="width: 20%;">
                                                        @if($pembayaran->waliBank)
                                                            <small>{{ $pembayaran->waliBank->nama_bank }}</small><br>
                                                            <small>{{ $pembayaran->waliBank->nomor_rekening }}</small><br>
                                                            <small>{{ $pembayaran->waliBank->nama_rekening }}</small>
                                                        @else
                                                            <small>{{ $pembayaran->nama_bank_pengirim }}</small><br>
                                                            <small>{{ $pembayaran->nomor_rekening_pengirim }}</small><br>
                                                            <small>{{ $pembayaran->nama_rekening_pengirim }}</small>
                                                        @endif
                                                    </td>

                                                    <!-- Kolom Rekening Sekolah -->
                                                    <td style="width: 20%;">
                                                        <small>{{ $pembayaran->bankSekolah?->nama_bank }}</small><br>
                                                        <small>{{ $pembayaran->bankSekolah?->nomor_rekening }}</small><br>
                                                        <small>{{ $pembayaran->bankSekolah?->nama_rekening }}</small>
                                                    </td>

                                                    <!-- Kolom Bukti Bayar -->
                                                    <td style="width: 8%;">
                                                        @if($pembayaran->bukti_bayar)
                                                            <a href="javascript:void(0)" onclick="popupCenter({ url: '{{ \Storage::url($pembayaran->bukti_bayar) }}', title: 'Bukti', w: 800, h: 600 })" class="text-primary">
                                                                Lihat
                                                            </a>
                                                        @else
                                                            –
                                                        @endif
                                                    </td>

                                                    <!-- Kolom Status -->
                                                    <td style="width: 8%;">
                                                        @if($pembayaran->status_konfirmasi == 'sudah')
                                                            <span class="badge bg-success rounded-pill px-2 py-1">Sudah</span>
                                                        @else
                                                            <span class="badge bg-warning text-dark rounded-pill px-2 py-1">Belum</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @if ($pembayaranGroup->contains(fn($p) => $p->status_konfirmasi == 'belum'))
                                    <button type="submit" class="btn btn-success mt-3" id="btn-konfirmasi" disabled>
                                        <i class="bx bx-check-circle me-1"></i>Konfirmasi Terpilih
                                    </button>
                                    {!! Form::close() !!}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. RINCIAN TAGIHAN YANG DIBAYAR --}}
                <div class="row g-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header fw-semibold">
                                <i class="bx bx-file me-1"></i>Rincian Tagihan yang Dibayar
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">No</th>
                                                <th style="width: 60%;">Biaya</th>
                                                <th style="width: 20%;" class="text-end">Jumlah</th>
                                                <th style="width: 15%;">Tagihan ID</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $no = 1; @endphp
                                            @forelse($pembayaranGroup as $pembayaran)
                                                @if($pembayaran->tagihan)
                                                    @foreach($pembayaran->tagihan->tagihanDetails as $item)
                                                        <tr>
                                                            <td>{{ $no++ }}</td>
                                                            <td>{{ $item->nama_biaya }}</td>
                                                            <td class="text-end">{{ formatRupiah($item->jumlah_biaya) }}</td>
                                                            <td>{{ $pembayaran->tagihan_id }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">Tidak ada data</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const checkboxes = document.querySelectorAll('.pembayaran-checkbox');
    const btn = document.querySelector('#btn-konfirmasi');

    function updateButton() {
        if (btn) {
            btn.disabled = !Array.from(checkboxes).some(cb => cb.checked);
        }
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateButton);
    });

    updateButton();
});

function popupCenter({ url, title, w, h }) {
    const left = (screen.width / 2) - (w / 2);
    const top = (screen.height / 2) - (h / 2);
    const win = window.open(url, title, `toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width=${w}, height=${h}, top=${top}, left=${left}`);
    win.focus();
}
</script>
@endpush
