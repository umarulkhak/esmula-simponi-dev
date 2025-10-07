@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bx bx-credit-card me-2"></i>Detail Pembayaran
                </h5>
                <a href="{{ route('pembayaran.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bx bx-arrow-back me-1"></i>Kembali
                </a>
            </div>
            <div class="card-body">

                {{-- 1. RINGKASAN PEMBAYARAN --}}
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
                                                <span class="fw-medium text-muted">Jumlah Dibayar</span><br>
                                                <span class="text-success fw-bold">{{ formatRupiah($totalDibayar ?? 0) }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="fw-medium text-muted">Total Tagihan</span><br>
                                                <span class="text-danger fw-bold">{{ formatRupiah($totalTagihan ?? 0) }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="fw-medium text-muted">Jumlah Item</span><br>
                                                <span>{{ $pembayaranGroup->count() }} pembayaran</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2">
                                                <span class="fw-medium text-muted">Status Konfirmasi</span><br>
                                                @php
                                                    $statusGroup = $pembayaranGroup->every(fn($p) => $p->status_konfirmasi == 'sudah') ? 'sudah' : 'belum';
                                                @endphp
                                                @if($statusGroup === 'sudah')
                                                    <span class="badge bg-success">Sudah</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Belum</span>
                                                @endif
                                            </li>
                                            <li class="mb-2">
                                                <span class="fw-medium text-muted">Tanggal Konfirmasi</span><br>
                                                <span>
                                                    {{ $pembayaranGroup->where('status_konfirmasi', 'sudah')->max('tanggal_konfirmasi')
                                                        ? \Carbon\Carbon::parse($pembayaranGroup->where('status_konfirmasi', 'sudah')->max('tanggal_konfirmasi'))->locale('id')->isoFormat('D MMMM Y')
                                                        : '–' }}
                                                </span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="fw-medium text-muted">Status Pembayaran</span><br>
                                                @php
                                                    $statusPembayaran = 'Belum Dibayar';

                                                    if ($siswa) {
                                                        $jumlahTagihanBelumLunas = $siswa->tagihan()->where('status', '!=', 'lunas')->count();
                                                        $jumlahTagihanTotal = $siswa->tagihan()->count();

                                                        if ($jumlahTagihanBelumLunas == 0 && $jumlahTagihanTotal > 0) {
                                                            $statusPembayaran = 'Sudah Dibayar';
                                                        } elseif ($jumlahTagihanBelumLunas > 0) {
                                                            $statusPembayaran = 'Angsur';
                                                        }
                                                    }
                                                @endphp
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. DAFTAR ITEM PEMBAYARAN --}}
                <div class="row g-4 mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header fw-semibold">
                                <i class="bx bx-list-ul me-1"></i>Daftar Pembayaran
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                {{-- <th>ID Pembayaran</th> --}}
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
                                                    <td>{{ $loop->iteration }}</td>
                                                    {{-- <td>{{ $pembayaran->id }}</td> --}}
                                                    <td>
                                                        {{ $pembayaran->tanggal_bayar
                                                            ? \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->translatedFormat('d M Y')
                                                            : '–' }}
                                                    </td>
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
                                                            <a href="javascript:void(0)"
                                                               onclick="popupCenter({
                                                                   url: '{{ \Storage::url($pembayaran->bukti_bayar) }}',
                                                                   title: 'Bukti Pembayaran #{{ $pembayaran->id }}',
                                                                   w: 900,
                                                                   h: 700
                                                               })">
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
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. TAGIHAN & SISWA --}}
                <div class="row g-4">
                    {{-- Informasi Tagihan --}}
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header fw-semibold">
                                <i class="bx bx-file me-1"></i>Rincian Tagihan
                            </div>
                            <div class="card-body">
                                <h6 class="fw-semibold mb-2">
                                    <i class="bx bx-list-ul me-1"></i>Item Biaya
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Biaya</th>
                                                <th class="text-end">Jumlah</th>
                                                {{-- <th>Tagihan ID</th> --}}
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
                                                            {{-- <td>{{ $pembayaran->tagihan_id }}</td> --}}
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="4" class="text-danger">
                                                            Tagihan ID {{ $pembayaran->tagihan_id }} tidak ditemukan.
                                                        </td>
                                                    </tr>
                                                @endif
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted fst-italic">
                                                        Tidak ada item
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Informasi Siswa --}}
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header fw-semibold">
                                <i class="bx bx-user me-1"></i>Data Siswa
                            </div>
                            <div class="card-body">
                                @if($siswa)
                                    <p class="mb-2"><span class="fw-medium">Nama:</span> {{ $siswa->nama ?? '–' }}</p>
                                    <p class="mb-2"><span class="fw-medium">NIS/NISN:</span> {{ $siswa->nisn ?? '–' }}</p>
                                    <p class="mb-2"><span class="fw-medium">Kelas:</span> {{ $siswa->kelas ?? '–' }}</p>
                                    <p class="mb-3"><span class="fw-medium">Wali Murid:</span> {{ $siswa->wali?->name ?? '–' }}</p>
                                @else
                                    <p class="text-muted">Data siswa tidak ditemukan.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. TOMBOL KONFIRMASI DENGAN CHECKBOX --}}
                @if ($pembayaranGroup->contains(fn($p) => $p->status_konfirmasi == 'belum'))
                    {!! Form::open([
                        'route' => 'pembayaran.update.multiple',
                        'method' => 'PUT',
                        'onsubmit' => 'return confirm("Apakah anda yakin ingin mengonfirmasi pembayaran yang dipilih?")',
                    ]) !!}
                    <div class="table-responsive mb-3">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%">
                                        <input type="checkbox" id="select-all" onchange="toggleAll(this)">
                                    </th>
                                    <th>ID</th>
                                    <th>Tanggal Bayar</th>
                                    <th class="text-end">Jumlah</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pembayaranGroup as $pembayaran)
                                    @if($pembayaran->status_konfirmasi == 'belum')
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="pembayaran_ids[]" value="{{ $pembayaran->id }}" class="pembayaran-checkbox">
                                            </td>
                                            <td>{{ $pembayaran->id }}</td>
                                            <td>
                                                {{ $pembayaran->tanggal_bayar
                                                    ? \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->translatedFormat('d M Y')
                                                    : '–' }}
                                            </td>
                                            <td class="text-end">{{ formatRupiah($pembayaran->jumlah_dibayar) }}</td>
                                            <td>
                                                <span class="badge bg-warning text-dark">Belum</span>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-primary" id="btn-konfirmasi" disabled>
                        Konfirmasi Pembayaran Terpilih
                    </button>
                    {!! Form::close() !!}
                @else
                    <div class="alert alert-primary" role="alert">
                        <h3>Semua pembayaran sudah dikonfirmasi.</h3>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleAll(source) {
    const checkboxes = document.querySelectorAll('.pembayaran-checkbox');
    checkboxes.forEach(cb => cb.checked = source.checked);
    updateSubmitButton();
}
function updateSubmitButton() {
    const anyChecked = document.querySelector('.pembayaran-checkbox:checked');
    const btn = document.getElementById('btn-konfirmasi');
    if (btn) btn.disabled = !anyChecked;
}
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.pembayaran-checkbox').forEach(cb => {
        cb.addEventListener('change', updateSubmitButton);
    });
    updateSubmitButton();
});
</script>
@endpush
