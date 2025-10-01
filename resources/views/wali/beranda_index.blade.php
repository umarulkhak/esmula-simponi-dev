@extends('layouts.app_sneat_wali')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        {{-- === HEADER === --}}
        <div class="d-flex justify-content-between align-items-center mb-4 p-2">
            <div>
                <h5 class="fw-bold mb-0">Halo, Bapak/Ibu 👋</h5>
                <small class="text-muted">Dashboard Wali Murid</small>
            </div>
            <div class="avatar avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
            </div>
        </div>

        {{-- === QUICK STATS === --}}
        <div class="row g-3 mb-4">
            <div class="col-6">
                <div class="card shadow-sm border-0 rounded-3 bg-light-primary">
                    <div class="card-body p-3 text-center">
                        <div class="fs-5 fw-bold text-primary">2</div>
                        <small class="text-muted">Anak</small>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card shadow-sm border-0 rounded-3 bg-light-warning">
                    <div class="card-body p-3 text-center">
                        <div class="fs-5 fw-bold text-warning">3</div>
                        <small class="text-muted">Tagihan Belum Lunas</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- === ACTION BUTTONS === --}}
        <div class="d-grid gap-2 mb-4">
            <a href="{{ route('wali.tagihan.index') }}" class="btn btn-primary rounded-3 py-3">
                <i class="bx bx-credit-card me-2"></i>Bayar Tagihan Sekarang
            </a>
            <a href="{{ route('wali.siswa.index') }}" class="btn btn-outline-secondary rounded-3 py-3">
                <i class="bx bx-user me-2"></i>Lihat Data Anak
            </a>
        </div>

        {{-- === INFO PENTING === --}}
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-white border-0 py-2">
                <h6 class="mb-0 fw-bold">
                    <i class="bx bx-bell text-info me-1"></i> Info Penting
                </h6>
            </div>
            <div class="card-body p-3">
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2">🗓️ Pembayaran SPP Oktober dibuka 1–10 Oktober 2025.</li>
                    <li class="mb-2">📱 Konfirmasi pembayaran bisa lewat menu ini.</li>
                    <li>📞 Butuh bantuan? Hubungi operator via WhatsApp.</li>
                </ul>
            </div>
        </div>

        {{-- === RIWAYAT PEMBAYARAN TERBARU === --}}
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white border-0 py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Riwayat Terbaru</h6>
                <a href="{{ route('wali.pembayaran.index') }}" class="small text-decoration-none">Lihat semua</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item border-0 px-3 py-2">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="fw-medium">SPP September</div>
                                <small class="text-muted">15 Sep 2025</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">Rp 350.000</div>
                                <small class="badge bg-warning">Menunggu</small>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item border-0 px-3 py-2">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="fw-medium">Uang Gedung</div>
                                <small class="text-muted">10 Sep 2025</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">Rp 1.500.000</div>
                                <small class="badge bg-success">Sudah</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
