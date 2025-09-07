{{--
|--------------------------------------------------------------------------
| VIEW: Operator - Detail Tagihan Siswa
|--------------------------------------------------------------------------
| Penulis     : Umar Ulkhak
| Tujuan      : Menampilkan detail tagihan per siswa + kartu SPP yang bisa dicetak.
| Fitur       :
|   - Foto & info siswa di sidebar
|   - Filter tagihan per bulan/tahun
|   - Tabel daftar tagihan dengan status & total
|   - Kartu SPP rapi yang bisa dicetak (PDF friendly)
|   - Tombol cetak PDF & kembali
|   - Responsif di desktop & mobile
|   - Clean Code: struktur blade rapi, komentar jelas
|
| Variabel yang diharapkan dari Controller:
|   - $title   → Judul halaman
|   - $siswa   → Instance model Siswa
|   - $tagihan → Collection tagihan yang sudah difilter
|
--}}

@extends('layouts.app_sneat')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa fa-receipt me-2"></i>
                    {{ $title }}
                </h5>
                <a href="{{ route('tagihan.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">

                {{-- Flash Messages --}}
                @include('flash::message')

                <div class="row g-4">

                    {{-- === SECTION: INFO SISWA === --}}
                    <div class="col-12 col-md-4">
                        <div class="bg-light rounded p-3 h-100">
                            <h6 class="fw-bold mb-3">
                                <i class="fa fa-user me-2"></i>
                                Informasi Siswa
                            </h6>

                            {{-- Foto Siswa --}}
                            <div class="text-center mb-3">
                                @php
                                    $fotoPath = ($siswa->foto && \Storage::exists($siswa->foto))
                                        ? \Storage::url($siswa->foto)
                                        : asset('images/no-image.png');
                                @endphp
                                <img
                                    src="{{ $fotoPath }}"
                                    alt="Foto {{ $siswa->nama }}"
                                    class="rounded shadow-sm border"
                                    style="width: 100%; max-width: 200px; height: 260px; object-fit: cover; object-position: center;">
                            </div>

                            {{-- Detail Siswa --}}
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="fw-semibold text-muted">Nama</td>
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

                    {{-- === SECTION: DAFTAR TAGIHAN & KARTU SPP === --}}
                    <div class="col-12 col-md-8">

                        {{-- === FILTER BULAN/TAHUN === --}}
                        <div class="mb-4">
                            <form method="GET" class="row g-2 align-items-end">
                                <div class="col-auto">
                                    <label for="filter_bulan" class="form-label mb-0">Bulan</label>
                                    <select name="bulan" id="filter_bulan" class="form-select form-select-sm">
                                        <option value="">Semua Bulan</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}"
                                                {{ request('bulan') == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <label for="filter_tahun" class="form-label mb-0">Tahun</label>
                                    <select name="tahun" id="filter_tahun" class="form-select form-select-sm">
                                        <option value="">Semua Tahun</option>
                                        @for ($y = date('Y'); $y >= 2022; $y--)
                                            <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>
                                                {{ $y }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-outline-primary btn-sm">
                                        <i class="fa fa-filter me-1"></i> Filter
                                    </button>
                                    @if(request()->filled(['bulan', 'tahun']))
                                        <a href="{{ route('tagihan.show', $siswa->id) }}" class="btn btn-outline-secondary btn-sm ms-2">
                                            <i class="fa fa-undo me-1"></i> Reset
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </div>

                        {{-- === KARTU SPP UNTUK DICETAK === --}}
                        <div class="card border-primary mb-4 print-only">
                            <div class="card-header bg-primary text-light">
                                <h6 class="mb-0" style="color: white !important;">
                                    <i class="fa fa-print me-2"></i>
                                    Kartu SPP - {{ $siswa->nama }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <p class="mb-1"><strong>NISN:</strong> {{ $siswa->nisn ?? '–' }}</p>
                                        <p class="mb-1"><strong>Kelas:</strong> {{ $siswa->kelas ?? '–' }}</p>
                                        <p class="mb-1"><strong>Angkatan:</strong> {{ $siswa->angkatan ?? '–' }}</p>
                                    </div>
                                    <div class="col-6 text-end">
                                        <p class="mb-1"><strong>Bulan:</strong> {{ request('bulan') ? \Carbon\Carbon::create()->month(request('bulan'))->translatedFormat('F') : 'Semua' }}</p>
                                        <p class="mb-1"><strong>Tahun:</strong> {{ request('tahun') ?? 'Semua' }}</p>
                                        <p class="mb-1"><strong>Tanggal Cetak:</strong> {{ now()->translatedFormat('d F Y') }}</p>
                                    </div>
                                </div>
                                <hr>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Biaya</th>
                                                <th class="text-end">Jumlah</th>
                                                <th>Status</th>
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
                                                            <td>{{ $item->tanggal_tagihan->translatedFormat('d M Y') }}</td>
                                                            <td>{{ $detail->nama_biaya ?? '–' }}</td>
                                                            <td class="text-end">{{ 'Rp ' . number_format($detail->jumlah_biaya, 0, ',', '.') }}</td>
                                                            <td>
                                                                @if ($item->status === 'lunas')
                                                                    <span class="badge bg-success">Lunas</span>
                                                                @else
                                                                    <span class="badge bg-warning">Belum Lunas</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">Tidak ada data</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" class="text-end">Total</th>
                                                <th class="text-end fw-bold">{{ 'Rp ' . number_format($totalTagihan, 0, ',', '.') }}</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="mt-3 text-center">
                                    <p class="mb-0">Mengetahui,</p>
                                    <p class="mt-4">_______________________</p>
                                    <p class="mb-0">Bendahara Sekolah</p>
                                </div>
                            </div>
                        </div>

                        {{-- === DAFTAR TAGIHAN === --}}
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="fa fa-list me-2"></i>
                                    Daftar Tagihan
                                </h6>
                                @if($totalTagihan > 0)
                                    <button class="btn btn-outline-info btn-sm" onclick="printKartuSPP()">
                                        <i class="fa fa-print me-1"></i> Cetak Kartu SPP
                                    </button>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" style="width: 8%">No</th>
                                                <th>Tanggal Tagihan</th>
                                                <th>Nama Biaya</th>
                                                <th class="text-end">Jumlah</th>
                                                <th class="text-center" style="width: 15%">Status</th>
                                                <th class="text-center" style="width: 10%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $totalTagihan = 0; @endphp
                                            @forelse ($tagihan as $item)
                                                @if($item->tagihanDetails && $item->tagihanDetails->isNotEmpty())
                                                    @foreach($item->tagihanDetails as $detail)
                                                        @php $totalTagihan += $detail->jumlah_biaya; @endphp
                                                        <tr>
                                                            <td class="text-center">{{ $loop->parent->iteration }}.{{ $loop->iteration }}</td>
                                                            <td>{{ $item->tanggal_tagihan->translatedFormat('d M Y') }}</td>
                                                            <td>{{ $detail->nama_biaya ?? '–' }}</td>
                                                            <td class="text-end">{{ 'Rp ' . number_format($detail->jumlah_biaya, 0, ',', '.') }}</td>
                                                            <td class="text-center">
                                                                @if ($item->status === 'baru')
                                                                    <span class="badge bg-warning"><i class="fa fa-clock me-1"></i> Baru</span>
                                                                @elseif ($item->status === 'lunas')
                                                                    <span class="badge bg-success"><i class="fa fa-check-circle me-1"></i> Lunas</span>
                                                                @else
                                                                    <span class="badge bg-secondary"><i class="fa fa-info-circle me-1"></i> {{ ucfirst($item->status ?? 'tidak diketahui') }}</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                <form action="{{ route('tagihan.destroy', $item->id) }}"
                                                                      method="POST"
                                                                      onsubmit="return confirm('Yakin ingin menghapus tagihan ini beserta semua detailnya? Tindakan tidak bisa dibatalkan.')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center" title="Hapus tagihan ini">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <div class="d-flex flex-column align-items-center">
                                                            <i class="fa fa-receipt fa-2x text-muted mb-2"></i>
                                                            <span class="text-muted">
                                                                @if(request()->filled(['bulan', 'tahun']))
                                                                    Tidak ada tagihan untuk periode
                                                                    <strong>{{ \Carbon\Carbon::create()->month(request('bulan'))->translatedFormat('F') }} {{ request('tahun') }}</strong>.
                                                                @else
                                                                    Siswa ini belum memiliki tagihan.
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        @if ($totalTagihan > 0)
                                            <tfoot class="table-light">
                                                <tr>
                                                    <th colspan="3" class="text-end">Total Tagihan</th>
                                                    <th class="text-end fw-bold">{{ 'Rp ' . number_format($totalTagihan, 0, ',', '.') }}</th>
                                                    <th colspan="2"></th>
                                                </tr>
                                            </tfoot>
                                        @endif
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

@push('styles')
<style>
    .print-only {
        display: none;
    }

    @media print {
        body * {
            visibility: hidden;
        }
        .print-only, .print-only * {
            visibility: visible;
        }
        .print-only {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function printKartuSPP() {
        window.print();
    }
</script>
@endpush
