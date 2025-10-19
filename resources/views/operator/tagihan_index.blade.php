{{--
|--------------------------------------------------------------------------
| VIEW: Operator - Daftar Tagihan
|--------------------------------------------------------------------------
| Penulis     : Umar Ulkhak
| Tujuan      : Menampilkan daftar tagihan dengan dashboard ringkasan, filter lengkap, dan aksi massal.
| Fitur       :
|   - Dashboard ringkasan (lebih compact)
|   - Filter: bulan, tahun, status, kelas, pencarian
|   - Hapus massal (checkbox + tombol)
|   - Tabel responsif dengan aksi ikon-only
|   - Pagination & flash message
|   - UX konsisten dengan view siswa
|
| Variabel dari Controller:
|   - $models, $routePrefix, $title
|   - Statistik: $totalSiswa, $totalLunas, $totalBelum, $persentase
|   - Perbedaan vs periode lalu: $diffSiswa, $diffLunas, $diffBelum, $diffPersen
|
--}}

@extends('layouts.app_sneat')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">Data Tagihan</h5>
                <a href="{{ route($routePrefix . '.create') }}" class="btn btn-primary btn-sm px-3">
                    <i class="fa fa-plus me-1"></i> Tambah Tagihan
                </a>
            </div>
            <div class="card-body">

                {{-- === FLASH MESSAGE === --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                        <i class="fa fa-check-circle me-2"></i> {!! session('success') !!}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @elseif(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                        <i class="fa fa-exclamation-triangle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- === DASHBOARD RINGKASAN (COMPACT) === --}}
                <div class="row mb-4 g-2">
                    <div class="col-6 col-md-3">
                        <div class="card border rounded-2 p-2 h-100">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted small mb-1">Total Siswa</h6>
                                    <h5 class="fw-bold mb-0">{{ $totalSiswa }}</h5>
                                    <p class="{{ $diffSiswa >= 0 ? 'text-success' : 'text-danger' }} small mb-0">
                                        {{ $diffSiswa >= 0 ? '+' : '' }}{{ $diffSiswa }}
                                    </p>
                                </div>
                                <div class="bg-light rounded p-1">
                                    <i class="fa fa-users text-primary fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border rounded-2 p-2 h-100">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted small mb-1">Lunas</h6>
                                    <h5 class="fw-bold mb-0">{{ $totalLunas }}</h5>
                                    <p class="{{ $diffLunas >= 0 ? 'text-success' : 'text-danger' }} small mb-0">
                                        {{ $diffLunas >= 0 ? '+' : '' }}{{ $diffLunas }}
                                    </p>
                                </div>
                                <div class="bg-light rounded p-1">
                                    <i class="fa fa-check-circle text-success fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border rounded-2 p-2 h-100">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted small mb-1">Belum Bayar</h6>
                                    <h5 class="fw-bold mb-0">{{ $totalBelum }}</h5>
                                    <p class="{{ $diffBelum <= 0 ? 'text-success' : 'text-danger' }} small mb-0">
                                        {{ $diffBelum >= 0 ? '+' : '' }}{{ $diffBelum }}
                                    </p>
                                </div>
                                <div class="bg-light rounded p-1">
                                    <i class="fa fa-clock text-warning fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border rounded-2 p-2 h-100">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted small mb-1">Pembayaran</h6>
                                    <h5 class="fw-bold mb-0">{{ $persentase }}%</h5>
                                    <p class="{{ $diffPersen >= 0 ? 'text-success' : 'text-danger' }} small mb-0">
                                        {{ $diffPersen >= 0 ? '+' : '' }}{{ number_format($diffPersen, 1) }}%
                                    </p>
                                </div>
                                <div class="bg-light rounded p-1">
                                    <i class="fa fa-chart-line text-purple fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- === FILTER === --}}
                <div class="card mb-4 border">
                    <div class="card-header bg-white d-flex align-items-center gap-2">
                        <i class="fa fa-filter text-muted"></i>
                        <h6 class="mb-0 fw-bold">Filter Data</h6>
                    </div>
                    <div class="card-body">
                        {!! Form::open([
                            'route' => $routePrefix . '.index',
                            'method' => 'GET',
                            'class' => 'row g-2 align-items-end'
                        ]) !!}
                            <div class="col-12 col-md-2">
                                <label class="form-label small">Bulan</label>
                                <select name="bulan" class="form-select form-select-sm">
                                    <option value="">Semua</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}"
                                            {{ request('bulan') == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-12 col-md-2">
                                <label class="form-label small">Tahun</label>
                                <select name="tahun" class="form-select form-select-sm">
                                    <option value="">Semua</option>
                                    @for ($y = date('Y'); $y >= 2022; $y--)
                                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-12 col-md-2">
                                <label class="form-label small">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">Semua</option>
                                    <option value="baru" {{ request('status') == 'baru' ? 'selected' : '' }}>Baru</option>
                                    <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-2">
                                <label class="form-label small">Kelas</label>
                                <select name="kelas" class="form-select form-select-sm">
                                    <option value="">Semua</option>
                                    <option value="VII" {{ request('kelas') == 'VII' ? 'selected' : '' }}>VII</option>
                                    <option value="VIII" {{ request('kelas') == 'VIII' ? 'selected' : '' }}>VIII</option>
                                    <option value="IX" {{ request('kelas') == 'IX' ? 'selected' : '' }}>IX</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label small">Cari Siswa</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" name="q" class="form-control" placeholder="Nama/NISN..."
                                           value="{{ request('q') }}">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fa fa-filter me-1"></i> Filter
                                    </button>
                                    @if(request()->anyFilled(['q', 'bulan', 'tahun', 'status', 'kelas']))
                                        <a href="{{ route($routePrefix . '.index') }}" class="btn btn-outline-secondary">
                                            <i class="fa fa-times me-1"></i> Reset
                                        </a>
                                    @endif
                                </div>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>

                {{-- === AKSI MASSAL === --}}
                <div class="mb-3 d-flex gap-2 flex-wrap">
                    <form id="form-mass-delete" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                        {{-- Input hidden akan diisi ulang oleh JS --}}
                    </form>

                    <button type="button" class="btn btn-outline-danger btn-sm" id="btn-delete-selected" disabled>
                        <i class="fa fa-trash me-1"></i> Hapus Terpilih
                    </button>
                </div>

                {{-- === TABEL DATA === --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="table-tagihan">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 5%">
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th class="text-center" style="width: 5%">#</th>
                                <th>NISN</th>
                                <th>Nama Siswa</th>
                                <th class="text-center">Kelas</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($models as $item)
                                @if($item->siswa)
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" name="tagihan_ids[]" value="{{ $item->id }}" class="tagihan-checkbox">
                                        </td>
                                        <td class="text-center fw-medium">
                                            {{ $loop->iteration + ($models->firstItem() - 1) }}
                                        </td>
                                        <td><span class="badge bg-label-dark rounded-pill px-2 py-1">{{ $item->siswa->nisn }}</span></td>
                                        <td>
                                            <strong>{{ $item->siswa->nama }}</strong>
                                            <div class="text-muted small">Angkatan: {{ $item->siswa->angkatan }}</div>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $kelas = $item->siswa->kelas ?? '–';
                                                $colorMap = ['VII' => 'primary', 'VIII' => 'success', 'IX' => 'danger'];
                                                $color = $colorMap[$kelas] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-label-{{ $color }} rounded-pill px-2 py-1">{{ $kelas }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-dark rounded-pill px-2 py-1">
                                                {{ $item->tanggal_tagihan->translatedFormat('d M Y') }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($item->status == 'baru')
                                                <span class="badge bg-label-warning rounded-pill px-2 py-1">
                                                    <i class="fa fa-clock me-1"></i> BARU
                                                </span>
                                            @elseif($item->status == 'lunas')
                                                <span class="badge bg-label-success rounded-pill px-2 py-1">
                                                    <i class="fa fa-check-circle me-1"></i> LUNAS
                                                </span>
                                            @else
                                                <span class="badge bg-label-secondary rounded-pill px-2 py-1">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route($routePrefix . '.show', $item->siswa->id) }}"
                                                   class="btn btn-icon btn-outline-primary btn-sm"
                                                   title="Lihat semua tagihan">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <form action="{{ route($routePrefix . '.destroy', $item->id) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('⚠️ Yakin hapus tagihan ini?\n{{ $item->siswa->nama }} - {{ $item->tanggal_tagihan->translatedFormat('M Y') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-icon btn-outline-danger btn-sm"
                                                            title="Hapus tagihan">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fa fa-database fa-2x text-muted mb-2 d-block"></i>
                                        <p class="text-muted mb-0">Tidak ada data tagihan.</p>
                                        @if(request()->anyFilled(['q', 'bulan', 'tahun', 'status', 'kelas']))
                                            <small class="d-block mt-1">
                                                <a href="{{ route($routePrefix . '.index') }}" class="text-primary">
                                                    Lihat semua data
                                                </a>
                                            </small>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- === PAGINATION === --}}
                @if($models->hasPages())
                    <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <small class="text-muted">
                            Menampilkan {{ $models->firstItem() }} - {{ $models->lastItem() }} dari {{ $models->total() }} data
                        </small>
                        <div>{!! $models->links() !!}</div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table-hover tbody tr:hover {
        background-color: #f9fafb !important;
    }
    .btn-icon {
        width: 34px;
        height: 34px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
    }
    .btn-icon:hover {
        transform: scale(1.05);
        transition: transform 0.1s ease;
    }
    .text-purple { color: #6f42c1 !important; }

    /* Style khusus untuk pesan flash */
    .flash-message-content {
        font-size: 0.875rem;
        line-height: 1.5;
    }

    .flash-message-content p {
        margin: 0.5rem 0;
    }

    .flash-list {
        padding-left: 1.5rem;
        margin: 0.5rem 0;
        max-height: 150px;
        overflow-y: auto;
        border: 1px solid #d1e7dd;
        border-radius: 0.375rem;
        background-color: #f8fff8;
        padding: 0.75rem;
        font-size: 0.85rem;
    }

    .flash-list li {
        margin: 0.25rem 0;
        word-wrap: break-word;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.tagihan-checkbox');
    const deleteSelectedBtn = document.getElementById('btn-delete-selected');
    const formMassDelete = document.getElementById('form-mass-delete');

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateDeleteButton();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateDeleteButton);
    });

    function updateDeleteButton() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        deleteSelectedBtn.disabled = !anyChecked;
    }

    deleteSelectedBtn.addEventListener('click', function () {
        const selected = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        if (selected.length === 0) return;

        if (!confirm(`⚠️ Yakin ingin menghapus ${selected.length} tagihan yang dipilih?`)) {
            return;
        }

        // 🔥 HAPUS INPUT LAMA
        formMassDelete.innerHTML = `
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="DELETE">
        `;

        // 🔥 TAMBAHKAN INPUT BARU UNTUK SETIAP ID
        selected.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            formMassDelete.appendChild(input);
        });

        formMassDelete.action = "{{ route($routePrefix . '.massDestroy') }}";
        formMassDelete.submit();
    });
});
</script>
@endpush
