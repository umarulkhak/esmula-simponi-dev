@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold">Data Pembayaran</h5>
            </div>

            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                        <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @elseif(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                        <i class="fa fa-exclamation-triangle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card mb-4 border">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa fa-filter text-muted"></i>
                            <h6 class="mb-0 fw-bold">Filter Data</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3 align-items-end">
                            <div class="col-12 col-md-3">
                                <label for="bulan" class="form-label small">Bulan</label>
                                <select name="bulan" id="bulan" class="form-select form-select-sm">
                                    <option value="">Semua Bulan</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ request('bulan') == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-12 col-md-3">
                                <label for="tahun" class="form-label small">Tahun</label>
                                <select name="tahun" id="tahun" class="form-select form-select-sm">
                                    <option value="">Semua Tahun</option>
                                    @for ($y = date('Y'); $y >= 2022; $y--)
                                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="q" class="form-label small">Cari Siswa</label>
                                <div class="input-group">
                                    <input type="text" name="q" id="q" class="form-control form-control-sm" placeholder="Nama/NISN..." value="{{ request('q') }}">
                                    <button type="submit" class="btn btn-outline-primary btn-sm">
                                        <i class="fa fa-filter me-1"></i> Filter
                                    </button>
                                    <a href="{{ route('pembayaran.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fa fa-undo me-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 5%">#</th>
                                <th>NISN</th>
                                <th>Nama Siswa</th>
                                <th>Wali</th>
                                <th class="text-center">Kelas</th>
                                <th class="text-center">Tanggal Konfirmasi</th>
                                <th class="text-center">Status Konfirmasi</th>
                                <th class="text-center" style="width: 10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($results as $item)
                                <tr>
                                    <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                    <td><span class="badge bg-light text-dark">{{ $item->nisn }}</span></td>
                                    <td>
                                        <strong>{{ $item->nama_siswa }}</strong>
                                        <div class="text-muted small">Angkatan: {{ $item->angkatan }}</div>
                                    </td>
                                    <td><span class="badge bg-light text-dark">{{ $item->nama_wali }}</span></td>
                                    <td class="text-center">
                                        @php
                                            $kelas = $item->kelas ?? 'Tanpa Kelas';
                                            $colorMap = ['VII' => 'primary', 'VIII' => 'success', 'IX' => 'danger'];
                                            $color = $colorMap[$kelas] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $color }} text-white px-3 py-2 rounded-pill">{{ $kelas }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark">
                                            {{ \Carbon\Carbon::parse($item->tanggal_konfirmasi)->translatedFormat('d M Y') }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($item->status_konfirmasi == 'belum')
                                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                                <i class="fa fa-clock me-1"></i> BELUM
                                            </span>
                                        @else
                                            <span class="badge bg-success text-white px-3 py-2 rounded-pill">
                                                <i class="fa fa-check-circle me-1"></i> SUDAH
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('pembayaran.show', $item->id) }}" class="btn btn-outline-primary btn-sm px-3" title="Lihat detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-center">
                                            <i class="fa fa-database fa-3x text-muted"></i>
                                            <h5 class="text-muted fw-normal mb-2">Belum ada data pembayaran</h5>
                                            <p class="text-muted small">Silakan tunggu pembayaran dari wali murid.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($results->isNotEmpty())
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <small class="text-muted">
                                Menampilkan {{ $results->firstItem() }} - {{ $results->lastItem() }} dari {{ $results->total() }} data
                            </small>
                            {!! $results->links() !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
