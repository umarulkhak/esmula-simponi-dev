{{-- resources/views/operator/pembayaran_index.blade.php --}}
@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">

            {{-- Header Card --}}
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold">Data Pembayaran</h5>
            </div>

            <div class="card-body">

                {{-- Flash Message --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                        <i class="fa fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @elseif(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Form Filter Data --}}
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
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                                    <select name="bulan" id="bulan" class="form-select form-select-sm">
                                        <option value="">Semua Bulan</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}"
                                                {{ request('bulan') == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <label for="tahun" class="form-label small">Tahun</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                    <select name="tahun" id="tahun" class="form-select form-select-sm">
                                        <option value="">Semua Tahun</option>
                                        @for ($y = date('Y'); $y >= 2022; $y--)
                                            <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>
                                                {{ $y }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="q" class="form-label small">Cari Siswa</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-search"></i></span>
                                    <input type="text" name="q" id="q" class="form-control form-control-sm"
                                           placeholder="Nama/NISN..." value="{{ request('q') }}">

                                    {{-- Tombol Filter --}}
                                    <button type="submit" class="btn btn-outline-primary btn-sm">
                                        <i class="fa fa-filter me-1"></i> Filter
                                    </button>

                                    {{-- Tombol Reset --}}
                                    <a href="{{ route('pembayaran' . '.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fa fa-undo me-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Tabel Data --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="table-tagihan">
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
                            @forelse ($models as $item)
                                    <tr>
                                        <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                        <td><span class="badge bg-light text-dark">{{ $item->tagihan->siswa->nisn }}</span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <strong>{{ $item->tagihan->siswa->nama }}</strong>
                                                    <div class="text-muted small">Angkatan: {{ $item->tagihan->siswa->angkatan }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-light text-dark">{{ $item->wali->name }}</span></td>
                                        <td class="text-center">
                                            @php
                                                $kelas = $item->tagihan->siswa->kelas ?? 'Tanpa Kelas';
                                                $colorMap = [
                                                    'VII' => 'primary',
                                                    'VIII' => 'success',
                                                    'IX' => 'danger',
                                                ];
                                                $color = $colorMap[$kelas] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }} text-white px-3 py-2 rounded-pill">
                                                {{ $kelas }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark">
                                                {{ optional($item->tanggal_bayar)->translatedFormat('d M Y') }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($item->status_konfirmasi == 'belum')
                                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                                    <i class="fa fa-clock me-1"></i> BELUM
                                                </span>
                                            @elseif($item->status_konfirmasi == 'sudah')
                                                <span class="badge bg-success text-white px-3 py-2 rounded-pill">
                                                    <i class="fa fa-check-circle me-1"></i> SUDAH
                                                </span>
                                            @else
                                                <span class="badge bg-secondary text-white px-3 py-2 rounded-pill">
                                                    <i class="fa fa-info-circle me-1"></i> {{ ucfirst($item->status_konfirmasi) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('pembayaran' . '.show', $item->id) }}"
                                                   class="btn btn-outline-primary btn-sm px-3"
                                                   title="Lihat detail">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <form action="{{ route('pembayaran' . '.destroy', $item->tagihan->siswa->id) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('⚠️ PERHATIAN!\n\nYakin ingin menghapus SEMUA tagihan siswa ini?\n\nNama: {{ $item->tagihan->siswa->nama }}\nNISN: {{ $item->tagihan->siswa->nisn }}\n\nTindakan ini tidak bisa dibatalkan!')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm px-3" title="Hapus semua">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-center">
                                            <div class="mb-3">
                                                <i class="fa fa-database fa-3x text-muted"></i>
                                            </div>
                                            <h5 class="text-muted fw-normal mb-2">
                                                Belum ada data tagihan
                                            </h5>
                                            <p class="text-muted small">
                                                Silakan tambah data tagihan baru.
                                            </p>
                                            <a href="{{ route('pembayaran' . '.create') }}" class="btn btn-primary btn-sm mt-3">
                                                <i class="fa fa-plus me-1"></i> Tambah Tagihan
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($models->isNotEmpty())
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <small class="text-muted">
                                Menampilkan {{ $models->firstItem() }} - {{ $models->lastItem() }} dari {{ $models->total() }} data
                            </small>
                            {!! $models->links() !!}
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
