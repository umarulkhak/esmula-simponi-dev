{{--
|--------------------------------------------------------------------------
| VIEW: Operator - Beranda Dashboard
|--------------------------------------------------------------------------
| Penulis     : Umar Ulkhak
| Tujuan      : Menampilkan dashboard operator dengan statistik, grafik, dan aktivitas terbaru.
| Fitur       :
|   - Statistik ringkas (total siswa, pembayaran, tingkat pembayaran, tunggakan)
|   - Grafik statistik pembayaran (Chart.js)
|   - Ringkasan per kelas (VII, VIII, IX) dengan status pembayaran
|   - Daftar pembayaran terbaru
|   - Log aktivitas sistem
|   - Tombol aksi: export laporan, buat tagihan baru, tambah siswa
|   - Responsif di desktop & mobile
|   - Clean Code: struktur blade rapi, komentar jelas
|
| Variabel yang diharapkan dari Controller:
|   - $title → Judul halaman
|   - $stats → array statistik (total_siswa, pembayaran_bulan_ini, tingkat_pembayaran, tunggakan)
|   - $kelasStats → array data per kelas
|   - $recentPayments → array pembayaran terbaru
|   - $recentActivities → array aktivitas terbaru
|
--}}

@extends('layouts.app_sneat')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">

                {{-- === SECTION: HEADER === --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold mb-1">Dashboard Operator</h4>
                        <p class="text-muted mb-0">Selamat datang di Simponi SMP Muhammadiyah Larangan</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-outline-secondary btn-sm">
                            <i class="bx bx-file me-1"></i> Export Laporan
                        </a>
                        <a href="{{ route('tagihan.create') }}" class="btn btn-primary btn-sm">
                            <i class="bx bx-calendar-plus me-1"></i> Buat Tagihan Baru
                        </a>
                    </div>
                </div>

                {{-- === SECTION: STATS CARDS === --}}
                <div class="row g-4 mb-4">
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted small mb-1">Total Siswa</p>
                                        <h4 class="mb-0 fw-bold">{{ number_format($stats['total_siswa']) }}</h4>
                                        <small class="text-success">+{{ $stats['siswa_baru'] }} siswa baru</small>
                                    </div>
                                    <div class="bg-label-primary rounded p-3">
                                        <i class="bx bx-user fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted small mb-1">Pembayaran Bulan Ini</p>
                                        <h4 class="mb-0 fw-bold">Rp {{ number_format($stats['pembayaran_bulan_ini'], 0, ',', '.') }}</h4>
                                        <small class="text-success">+{{ $stats['pertumbuhan_bulan_lalu'] }}% dari bulan lalu</small>
                                    </div>
                                    <div class="bg-label-success rounded p-3">
                                        <i class="bx bx-credit-card fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted small mb-1">Tingkat Pembayaran</p>
                                        <h4 class="mb-0 fw-bold">{{ $stats['tingkat_pembayaran'] }}%</h4>
                                        <small class="text-success">+{{ $stats['peningkatan_bulan_lalu'] }}% dari bulan lalu</small>
                                    </div>
                                    <div class="bg-label-info rounded p-3">
                                        <i class="bx bx-trending-up fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted small mb-1">Tunggakan</p>
                                        <h4 class="mb-0 fw-bold">{{ $stats['tunggakan'] }}</h4>
                                        <small class="text-danger">{{ $stats['tagihan_belum_bayar'] }} tagihan belum dibayar</small>
                                    </div>
                                    <div class="bg-label-danger rounded p-3">
                                        <i class="bx bx-error fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- === SECTION: CHARTS & OVERVIEW === --}}
                <div class="row g-4 mb-4">
                    {{-- Grafik Statistik Pembayaran --}}
                    <div class="col-12 col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header border-bottom">
                                <h5 class="card-title mb-0">Statistik Pembayaran</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="paymentChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Ringkasan Per Kelas --}}
                    <div class="col-12 col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Ringkasan Per Kelas</h5>
                                <a href="{{ route('siswa.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bx bx-user-plus me-1"></i> Tambah Siswa
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    @foreach($kelasStats as $kelas)
                                        <div class="col-12">
                                            <div class="border rounded p-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="fw-semibold mb-0">{{ $kelas['nama'] }}</h6>
                                                        <small class="text-muted">{{ $kelas['jumlah_siswa'] }} siswa</small>
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        @if($kelas['lunas'] > 0)
                                                            <span class="badge bg-label-success rounded-pill px-2 py-1">
                                                                {{ $kelas['lunas'] }} Lunas
                                                            </span>
                                                        @endif
                                                        @if($kelas['pending'] > 0)
                                                            <span class="badge bg-label-warning rounded-pill px-2 py-1">
                                                                {{ $kelas['pending'] }} Pending
                                                            </span>
                                                        @endif
                                                        @if($kelas['belum_bayar'] > 0)
                                                            <span class="badge bg-label-danger rounded-pill px-2 py-1">
                                                                {{ $kelas['belum_bayar'] }} Belum
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row mt-4 pt-3 border-top">
                                    <div class="col-4 text-center">
                                        <h5 class="fw-bold text-success">{{ $stats['total_lunas'] }}</h5>
                                        <small class="text-muted">Total Lunas</small>
                                    </div>
                                    <div class="col-4 text-center">
                                        <h5 class="fw-bold text-warning">{{ $stats['total_pending'] }}</h5>
                                        <small class="text-muted">Pending</small>
                                    </div>
                                    <div class="col-4 text-center">
                                        <h5 class="fw-bold text-danger">{{ $stats['total_belum_bayar'] }}</h5>
                                        <small class="text-muted">Belum Bayar</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- === SECTION: RECENT ACTIVITIES === --}}
                <div class="row g-4">
                    {{-- Pembayaran Terbaru --}}
                    <div class="col-12 col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header border-bottom">
                                <h5 class="card-title mb-0">Pembayaran Terbaru</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Siswa</th>
                                                <th>Kelas</th>
                                                <th>Bulan</th>
                                                <th>Jumlah</th>
                                                <th>Tanggal</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentPayments as $payment)
                                                <tr>
                                                    <td class="fw-semibold">{{ $payment->siswa->nama }}</td>
                                                    <td>{{ $payment->siswa->kelas }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($payment->tanggal_tagihan)->translatedFormat('F Y') }}</td>
                                                    <td class="fw-bold">Rp {{ number_format($payment->total_biaya, 0, ',', '.') }}</td>
                                                    <td>{{ $payment->created_at->format('d-m-Y') }}</td>
                                                    <td>
                                                        @if($payment->status === 'lunas')
                                                            <span class="badge bg-label-success">Lunas</span>
                                                        @elseif($payment->status === 'baru')
                                                            <span class="badge bg-label-warning">Pending</span>
                                                        @else
                                                            <span class="badge bg-label-danger">Belum Bayar</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('tagihan.show', $payment->siswa_id) }}" class="btn btn-outline-secondary btn-sm">
                                                            <i class="bx bx-show"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center py-4">
                                                        <i class="bx bx-receipt fs-1 text-muted mb-2"></i>
                                                        <p class="text-muted mb-0">Belum ada pembayaran terbaru</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-center mt-3">
                                    <a href="{{ route('tagihan.index') }}" class="btn btn-outline-secondary btn-sm">
                                        Lihat Semua Pembayaran
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Aktivitas Terbaru --}}
                    <div class="col-12 col-lg-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header border-bottom">
                                <h5 class="card-title mb-0">Aktivitas Terbaru</h5>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    @forelse($recentActivities as $activity)
                                        <div class="timeline-item mb-3">
                                            {{-- ✅ PERBAIKAN: Ganti $activity->type → $activity['type'] --}}
                                            <div class="timeline-point bg-{{ $activity['type'] === 'payment' ? 'success' : ($activity['type'] === 'student' ? 'primary' : ($activity['type'] === 'reminder' ? 'warning' : 'danger')) }}"></div>
                                            <div class="timeline-content">
                                                <h6 class="mb-1">{{ $activity['title'] }}</h6>
                                                <p class="text-muted small mb-1">{{ $activity['description'] }}</p>
                                                <small class="text-muted">{{ $activity['created_at']->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4">
                                            <i class="bx bx-history fs-1 text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Belum ada aktivitas terbaru</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #e9ecef;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-point {
        position: absolute;
        left: -30px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 3px solid #fff;
    }

    .timeline-content {
        padding: 12px 16px;
        background-color: #f8f9fa;
        border-radius: 8px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Chart
        const ctx = document.getElementById('paymentChart').getContext('2d');
        const paymentChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [{
                    label: 'Lunas (%)',
                    data: [85, 90, 88, 92, 95, 87],
                    backgroundColor: '#696cff',
                    borderRadius: 4,
                    barPercentage: 0.8,
                }, {
                    label: 'Belum Bayar (%)',
                    data: [15, 10, 12, 8, 5, 13],
                    backgroundColor: '#e9ecef',
                    borderRadius: 4,
                    barPercentage: 0.8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Persentase (%)'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
