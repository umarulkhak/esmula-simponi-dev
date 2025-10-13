@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            {{-- HEADER SISWA --}}
            <div class="card-header bg-gradient-primary text-white">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
                    <div>
                        @if($siswa)
                            <h5 class="mb-1 fw-bold fs-5">
                                <i class="bx bx-user-circle me-2"></i>{{ $siswa->nama }}
                            </h5>
                            <div class="d-flex flex-wrap gap-3 mt-1">
                                <span class="badge bg-light text-dark"><i class="bx bx-id-card me-1"></i> {{ $siswa->nisn }}</span>
                                <span class="badge bg-light text-dark"><i class="bx bx-school me-1"></i> {{ $siswa->kelas }}</span>
                                <span class="badge bg-light text-dark"><i class="bx bx-user me-1"></i> {{ $siswa->wali?->name ?? '–' }}</span>
                            </div>
                        @else
                            <h5 class="mb-1">Siswa Tidak Ditemukan</h5>
                            <div class="text-muted">Data tidak tersedia.</div>
                        @endif
                    </div>
                    <a href="{{ route('pembayaran.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="card-body">

                {{-- STATUS PEMBAYARAN --}}
                <div class="row g-4 mb-4">
                    <div class="col-12">
                        <div class="card border rounded-3">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between flex-wrap gap-3">
                                    <div class="text-center flex-fill">
                                        <small class="text-muted d-block">Total Tagihan</small>
                                        <strong class="text-danger fs-5">{{ formatRupiah($totalTagihan ?? 0) }}</strong>
                                    </div>
                                    <div class="vr d-none d-md-block"></div>
                                    <div class="text-center flex-fill">
                                        <small class="text-muted d-block">Sudah Dibayar</small>
                                        <strong class="text-success fs-5">{{ formatRupiah($totalDibayar ?? 0) }}</strong>
                                    </div>
                                    <div class="vr d-none d-md-block"></div>
                                    <div class="text-center flex-fill">
                                        <small class="text-muted d-block">Status</small>
                                            @if($statusPembayaran === 'Lunas')
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

                {{-- DAFTAR PEMBAYARAN --}}
                <div class="row g-4">
                    <div class="col-12">
                        <div class="card border rounded-3">
                            <div class="card-header d-flex justify-content-between align-items-center py-3">
                                <h6 class="mb-0 fw-semibold"><i class="bx bx-receipt me-1"></i> Riwayat Pembayaran</h6>
                                @if ($pembayaranGroup->contains(fn($p) => $p->status_konfirmasi == 'belum'))
                                    <small class="text-muted">Centang untuk konfirmasi</small>
                                @endif
                            </div>
                            <div class="card-body p-0">
                                @if ($pembayaranGroup->isEmpty())
                                    <div class="text-center py-5 text-muted">
                                        <i class="bx bx-receipt fs-1 mb-2"></i><br>
                                        Belum ada pembayaran.
                                    </div>
                                @else
                                    @if ($pembayaranGroup->contains(fn($p) => $p->status_konfirmasi == 'belum'))
                                        {!! Form::open(['route' => 'pembayaran.update.multiple', 'method' => 'PUT', 'class' => 'table-form']) !!}
                                        @csrf
                                    @endif

                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 40px;" class="text-center">#</th>
                                                    <th>Tagihan</th>
                                                    <th>Tanggal</th>
                                                    <th>Metode</th>
                                                    <th>Biaya</th>
                                                    <th class="text-end">Jumlah</th>
                                                    <th class="text-center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $totalJumlah = 0; @endphp
                                                @foreach($pembayaranGroup as $pembayaran)
                                                    @php
                                                        $totalJumlah += $pembayaran->jumlah_dibayar;
                                                        $biayas = $pembayaran->tagihan?->tagihanDetails->pluck('nama_biaya') ?? collect([]);
                                                        $biayaPreview = $biayas->take(2)->implode(', ');
                                                        $biayaLain = $biayas->count() > 2 ? ' +'.($biayas->count() - 2).' lainnya' : '';
                                                        $biayaFull = $biayas->implode(', ');
                                                    @endphp
                                                    <tr class="border-bottom">
                                                        <td class="text-center align-middle">
                                                            @if ($pembayaran->status_konfirmasi == 'belum')
                                                                <input type="checkbox" name="pembayaran_ids[]" value="{{ $pembayaran->id }}" class="form-check-input pembayaran-checkbox">
                                                            @else
                                                                <i class="bx bx-check-circle text-success fs-5"></i>
                                                            @endif
                                                        </td>
                                                        <td class="align-middle fw-medium">{{ $pembayaran->tagihan_id }}</td>
                                                        <td class="align-middle">
                                                            {{ $pembayaran->tanggal_bayar ? \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->translatedFormat('d M Y') : '–' }}
                                                        </td>
                                                        <td class="align-middle">
                                                            @php $metode = strtolower($pembayaran->metode_pembayaran); @endphp
                                                            @if($metode === 'transfer')
                                                                <a href="javascript:void(0)"
                                                                   class="text-primary text-decoration-none d-flex align-items-center"
                                                                   data-bs-toggle="modal"
                                                                   data-bs-target="#transferModal"
                                                                   data-pengirim-nama="{{ $pembayaran->waliBank?->nama_rekening ?? $pembayaran->nama_rekening_pengirim }}"
                                                                   data-pengirim-no="{{ $pembayaran->waliBank?->nomor_rekening ?? $pembayaran->nomor_rekening_pengirim }}"
                                                                   data-pengirim-bank="{{ $pembayaran->waliBank?->nama_bank ?? $pembayaran->nama_bank_pengirim }}"
                                                                   data-sekolah-nama="{{ $pembayaran->bankSekolah?->nama_rekening }}"
                                                                   data-sekolah-no="{{ $pembayaran->bankSekolah?->nomor_rekening }}"
                                                                   data-sekolah-bank="{{ $pembayaran->bankSekolah?->nama_bank }}"
                                                                   data-bukti="{{ $pembayaran->bukti_bayar ? \Storage::url($pembayaran->bukti_bayar) : '' }}">
                                                                    <i class="bx bx-transfer-alt me-1"></i> Transfer
                                                                </a>
                                                            @elseif($metode === 'manual')
                                                                <span class="text-muted"><i class="bx bx-hand me-1"></i> Manual</span>
                                                            @else
                                                                {{ $pembayaran->metode_pembayaran }}
                                                            @endif
                                                        </td>
                                                        <td class="align-middle">
                                                            @if($biayas->isNotEmpty())
                                                                <span data-bs-toggle="tooltip" title="{{ $biayaFull }}">
                                                                    {{ $biayaPreview }}{{ $biayaLain }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">–</span>
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-end fw-semibold">{{ formatRupiah($pembayaran->jumlah_dibayar) }}</td>
                                                        <td class="align-middle text-center">
                                                            @if($pembayaran->status_konfirmasi == 'sudah')
                                                                <span class="badge bg-success rounded-pill" data-bs-toggle="tooltip" title="Sudah dikonfirmasi">
                                                                    <i class="bx bx-check"></i>
                                                                </span>
                                                            @else
                                                                <span class="badge bg-warning text-dark rounded-pill" data-bs-toggle="tooltip" title="Menunggu konfirmasi">
                                                                    <i class="bx bx-time"></i>
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="fw-bold">
                                                <tr class="bg-light">
                                                    <td colspan="5" class="text-end py-3">Total Pembayaran:</td>
                                                    <td class="text-end py-3">{{ formatRupiah($totalJumlah) }}</td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    @if ($pembayaranGroup->contains(fn($p) => $p->status_konfirmasi == 'belum'))
                                        <div class="card-footer bg-transparent border-0 p-3">
                                            <button type="submit" class="btn btn-success" id="btn-konfirmasi" disabled>
                                                <i class="bx bx-check-circle me-1"></i> Konfirmasi Terpilih
                                            </button>
                                            <small class="text-muted ms-2">Pilih minimal 1 pembayaran untuk dikonfirmasi.</small>
                                        </div>
                                        {!! Form::close() !!}
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DETAIL TRANSFER (MODERN) --}}
<div class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Detail Transfer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="row g-3">
                    <div class="col-12">
                        <h6 class="fw-bold text-primary mb-2">Rekening Pengirim</h6>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-1"><strong>Nama:</strong> <span id="pengirim-nama">–</span></p>
                            <p class="mb-1"><strong>No Rek:</strong> <span id="pengirim-no">–</span></p>
                            <p class="mb-0"><strong>Bank:</strong> <span id="pengirim-bank">–</span></p>
                        </div>
                    </div>
                    <div class="col-12">
                        <h6 class="fw-bold text-primary mb-2">Rekening Sekolah</h6>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-1"><strong>Nama:</strong> <span id="sekolah-nama">–</span></p>
                            <p class="mb-1"><strong>No Rek:</strong> <span id="sekolah-no">–</span></p>
                            <p class="mb-0"><strong>Bank:</strong> <span id="sekolah-bank">–</span></p>
                        </div>
                    </div>
                    <div class="col-12">
                        <h6 class="fw-bold text-primary mb-2">Bukti Pembayaran</h6>
                        <div id="modal-bukti" class="text-center py-2">
                            –
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Tooltip
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Checkbox handler
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

    // Modal handler
    const transferModal = document.getElementById('transferModal');
    if (transferModal) {
        transferModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const pengirimNama = button.getAttribute('data-pengirim-nama') || '–';
            const pengirimNo = button.getAttribute('data-pengirim-no') || '–';
            const pengirimBank = button.getAttribute('data-pengirim-bank') || '–';
            const sekolahNama = button.getAttribute('data-sekolah-nama') || '–';
            const sekolahNo = button.getAttribute('data-sekolah-no') || '–';
            const sekolahBank = button.getAttribute('data-sekolah-bank') || '–';
            const buktiUrl = button.getAttribute('data-bukti');

            document.getElementById('pengirim-nama').textContent = pengirimNama;
            document.getElementById('pengirim-no').textContent = pengirimNo;
            document.getElementById('pengirim-bank').textContent = pengirimBank;

            document.getElementById('sekolah-nama').textContent = sekolahNama;
            document.getElementById('sekolah-no').textContent = sekolahNo;
            document.getElementById('sekolah-bank').textContent = sekolahBank;

            const buktiDiv = document.getElementById('modal-bukti');
            if (buktiUrl) {
                buktiDiv.innerHTML = `
                    <img src="${buktiUrl}" alt="Bukti Bayar" class="img-fluid rounded border" style="max-height: 200px; object-fit: cover;">
                    <div class="mt-2">
                        <a href="${buktiUrl}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bx bx-download me-1"></i> Unduh / Lihat Penuh
                        </a>
                    </div>
                `;
            } else {
                buktiDiv.innerHTML = '<span class="text-muted">Tidak ada bukti</span>';
            }
        });
    }
});
</script>
@endpush
