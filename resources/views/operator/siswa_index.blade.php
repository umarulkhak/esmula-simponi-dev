{{--
|--------------------------------------------------------------------------
| VIEW: Operator - Daftar Siswa
|--------------------------------------------------------------------------
| Penulis     : Umar Ulkhak
| Tujuan      : Menampilkan daftar siswa dengan fitur pencarian, aksi, dan pagination.
| Fitur       :
|   - Tombol tambah data
|   - Pencarian real-time (berdasarkan nama siswa)
|   - Tabel responsif dengan aksi: Edit, Detail, Hapus (icon-only style)
|   - Pagination Bootstrap
|   - Responsif di mobile
|   - UX Friendly: konfirmasi hapus, ikon, spacing konsisten
|   - Clean Code: struktur blade rapi, komentar jelas
|
| Variabel yang diharapkan dari Controller:
|   - $title     → Judul halaman
|   - $routePrefix → Prefix route (misal: 'operator.siswa')
|   - $models    → Collection paginate siswa
|
--}}

@extends('layouts.app_sneat')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $title }}</h5>
                {{-- Tombol Tambah — selalu muncul di kanan atas --}}
                <a href="{{ route($routePrefix . '.create') }}" class="btn btn-primary btn-sm">
                    <i class="bx bx-plus me-1"></i> Tambah Data
                </a>
            </div>
            <div class="card-body">

                {{-- === SECTION: PENCARIAN === --}}
                <div class="mb-4">
                    {!! Form::open([
                        'route' => $routePrefix . '.index',
                        'method' => 'GET',
                        'class' => 'd-flex gap-2 flex-wrap align-items-end'
                    ]) !!}
                        <div style="flex: 1; min-width: 200px;">
                            <label for="q" class="form-label visually-hidden">Cari Siswa</label>
                            <input
                                type="text"
                                name="q"
                                id="q"
                                class="form-control"
                                placeholder="🔍 Cari Nama Siswa..."
                                value="{{ request('q') }}"
                                autocomplete="off"
                            >
                        </div>
                        <button class="btn btn-outline-primary px-4" type="submit">
                            <i class="bx bx-search"></i> Cari
                        </button>
                        @if(request('q'))
                            <a href="{{ route($routePrefix . '.index') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-x"></i> Reset
                            </a>
                        @endif
                    {!! Form::close() !!}
                </div>

                {{-- === SECTION: TABEL DATA === --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="table-siswa">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 5%">#</th>
                                <th>Wali Murid</th>
                                <th>Nama Siswa</th>
                                <th>NISN</th>
                                <th>Kelas</th>
                                <th>Angkatan</th>
                                <th>Dibuat Oleh</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($models as $item)
                                <tr>
                                    <td class="text-center fw-medium">
                                        {{ $loop->iteration + ($models->firstItem() - 1) }}
                                    </td>
                                    <td>
                                        <span class="badge bg-label-primary rounded-pill px-3 py-1">
                                            {{ $item->wali->name ?? '–' }}
                                        </span>
                                    </td>
                                    <td class="fw-semibold">{{ $item->nama }}</td>
                                    <td>{{ $item->nisn ?? '–' }}</td>
                                    <td>
                                        <span class="badge bg-label-{{ $item->kelas == 'VII' ? 'success' : ($item->kelas == 'VIII' ? 'warning' : 'danger') }} rounded-pill">
                                            {{ $item->kelas }}
                                        </span>
                                    </td>
                                    <td>{{ $item->angkatan }}</td>
                                    <td>{{ $item->user->name ?? '–' }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">

                                            {{-- Tombol Detail --}}
                                            <a href="{{ route($routePrefix . '.show', $item->id) }}"
                                               class="btn btn-outline-info btn-sm d-inline-flex align-items-center justify-content-center"
                                               title="Lihat Detail Siswa: {{ $item->nama }}">
                                                <i class="fa fa-info"></i>
                                            </a>

                                            {{-- Tombol Edit --}}
                                            <a href="{{ route($routePrefix . '.edit', $item->id) }}"
                                               class="btn btn-outline-warning btn-sm d-inline-flex align-items-center justify-content-center"
                                               title="Edit Data Siswa: {{ $item->nama }}">
                                                <i class="fa fa-edit"></i>
                                            </a>

                                            {{-- Tombol Hapus --}}
                                            <form action="{{ route($routePrefix . '.destroy', $item->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('⚠️ Yakin ingin menghapus data siswa: {{ $item->nama }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center"
                                                        title="Hapus Data Siswa: {{ $item->nama }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="bx bx-empty fs-1 text-muted mb-2 d-block"></i>
                                        <p class="text-muted mb-0">Data tidak ditemukan.</p>
                                        @if(request('q'))
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

                {{-- === SECTION: PAGINATION === --}}
                @if($models->hasPages())
                    <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <small class="text-muted">
                            Menampilkan {{ $models->firstItem() }} - {{ $models->lastItem() }} dari {{ $models->total() }} data
                        </small>
                        <div>
                            {!! $models->links() !!}
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Optional: hover effect lebih smooth */
    #table-siswa tbody tr:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s ease;
    }

    /* Optional: tombol aksi lebih kotak dan proporsional */
    .btn-sm.d-inline-flex {
        width: 36px;
        height: 36px;
    }
</style>
@endpush

@push('scripts')
<script>
    // Optional: Fokus ke input pencarian saat halaman load
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('q');
        if (searchInput && !searchInput.value) {
            searchInput.focus();
        }
    });
</script>
@endpush
