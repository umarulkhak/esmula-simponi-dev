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
                                    <table class="table table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                @if ($pembayaranGroup->contains(fn($p) => $p->status_konfirmasi == 'belum'))
                                                    <th style="width: 5%">#</th>
                                                @endif
                                                <th>Tagihan ID</th>
                                                <th>Tanggal Bayar</th>
                                                <th>Metode</th>
                                                <th class="text-end">Jumlah</th>
                                                <th>Rekening Pengirim</th>
                                                <th>Rekening Sekolah</th>
                                                <th>Bukti Bayar</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pembayaranGroup as $pembayaran)
                                                <tr>
                                                    @if($pembayaran->status_konfirmasi == 'belum')
                                                        <td>
                                                            <input type="checkbox" name="pembayaran_ids[]" value="{{ $pembayaran->id }}" class="pembayaran-checkbox">
                                                        </td>
                                                    @endif
                                                    <td>{{ $pembayaran->tagihan_id }}</td>
                                                    <td>{{ $pembayaran->tanggal_bayar ? \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->translatedFormat('d M Y') : '–' }}</td>
                                                    <td>{{ $pembayaran->metode_pembayaran }}</td>
                                                    <td class="text-end">{{ formatRupiah($pembayaran->jumlah_dibayar) }}</td>
                                                    <td>
                                                        @if($pembayaran->waliBank)
                                                            {{ $pembayaran->waliBank->nama_bank }}<br>
                                                            {{ $pembayaran->waliBank->nomor_rekening }}<br>
                                                            {{ $pembayaran->waliBank->nama_rekening }}
                                                        @else
                                                            {{ $pembayaran->nama_bank_pengirim }}<br>
                                                            {{ $pembayaran->nomor_rekening_pengirim }}<br>
                                                            {{ $pembayaran->nama_rekening_pengirim }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $pembayaran->bankSekolah?->nama_bank }}<br>
                                                        {{ $pembayaran->bankSekolah?->nomor_rekening }}<br>
                                                        {{ $pembayaran->bankSekolah?->nama_rekening }}
                                                    </td>
                                                    <td>
                                                        @if($pembayaran->bukti_bayar)
                                                            <a href="javascript:void(0)" onclick="popupCenter({ url: '{{ \Storage::url($pembayaran->bukti_bayar) }}', title: 'Bukti', w: 800, h: 600 })">
                                                                Lihat
                                                            </a>
                                                        @else
                                                            –
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($pembayaran->status_konfirmasi == 'sudah')
                                                            <span class="badge bg-success">Sudah</span>
                                                        @else
                                                            <span class="badge bg-warning text-dark">Belum</span>
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
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Biaya</th>
                                                <th class="text-end">Jumlah</th>
                                                <th>Tagihan ID</th>
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
            btn.disabled = !document.querySelector('.pembayaran-checkbox:checked');
        }
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateButton);
    });

    updateButton();
});
</script>
@endpush
