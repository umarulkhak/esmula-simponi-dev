{{--
|--------------------------------------------------------------------------
| VIEW: Operator - Daftar Wali Murid
|--------------------------------------------------------------------------
| Penulis     : Umar Ulkhak
| Tujuan      : Menampilkan daftar wali murid dengan fitur pencarian, aksi, dan pagination.
| Fitur       :
|   - Tombol tambah data
|   - Pencarian real-time (berdasarkan nama/email)
|   - Tabel responsif dengan aksi: Detail, Edit, Hapus (icon-only style)
|   - Badge warna untuk level akses (wali)
|   - Pagination Bootstrap
|   - Responsif di mobile
|   - UX Friendly: konfirmasi hapus, tooltip, spacing konsisten
|   - Gunakan Font Awesome (fa) — tidak pakai Boxicons (bx)
|   - Header tanpa ikon — hanya teks
|
| Variabel yang diharapkan dari Controller:
|   - $title       → Judul halaman
|   - $routePrefix → Prefix route (misal: 'wali')
|   - $models     → Collection paginate wali
|
--}}

@extends('layouts.app_sneat')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    {{ $title }} {{-- ✅ Header tanpa ikon — sesuai permintaan --}}
                </h5>
                <a href="{{ route($routePrefix . '.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus me-1"></i> Tambah Data {{-- ✅ Tetap pakai fa --}}
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
                            <label for="q" class="form-label visually-hidden">Cari Wali</label>
                            <input
                                type="text"
                                name="q"
                                id="q"
                                class="form-control"
                                placeholder="🔍 Cari Nama atau Email Wali..."
                                value="{{ request('q') }}"
                                autocomplete="off"
                            >
                        </div>
                        <button class="btn btn-outline-primary px-4" type="submit">
                            <i class="fa fa-search"></i> Cari
                        </button>
                        @if(request('q'))
                            <a href="{{ route($routePrefix . '.index') }}" class="btn btn-outline-secondary">
                                <i class="fa fa-times"></i> Reset
                            </a>
                        @endif
                    {!! Form::close() !!}
                </div>

                {{-- === SECTION: TABEL DATA === --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="table-wali">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 5%">#</th>
                                <th>Nama</th>
                                <th>No. HP</th>
                                <th>Email</th>
                                <th>Akses</th>
                                <th class="text-center" style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($models as $item)
                                <tr>
                                    <td class="text-center fw-medium">
                                        {{ $loop->iteration + ($models->firstItem() - 1) }}
                                    </td>
                                    <td class="fw-semibold">{{ $item->name }}</td>
                                    <td>{{ $item->nohp ?? '–' }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>
                                        <span class="badge bg-label-success rounded-pill px-3 py-1">
                                            {{ ucfirst($item->akses) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">

                                            {{-- Tombol Detail --}}
                                            <a href="{{ route($routePrefix . '.show', $item->id) }}"
                                               class="btn btn-outline-info btn-sm d-inline-flex align-items-center justify-content-center"
                                               title="Detail Wali: {{ $item->name }}">
                                                <i class="fa fa-eye"></i> {{-- ✅ Tetap pakai fa --}}
                                            </a>

                                            {{-- Tombol Edit --}}
                                            <a href="{{ route($routePrefix . '.edit', $item->id) }}"
                                               class="btn btn-outline-warning btn-sm d-inline-flex align-items-center justify-content-center"
                                               title="Edit Wali: {{ $item->name }}">
                                                <i class="fa fa-edit"></i> {{-- ✅ Tetap pakai fa --}}
                                            </a>

                                            {{-- Tombol Hapus --}}
                                            <form action="{{ route($routePrefix . '.destroy', $item->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('⚠️ Yakin ingin menghapus wali: {{ $item->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center"
                                                        title="Hapus Wali: {{ $item->name }}">
                                                    <i class="fa fa-trash"></i> {{-- ✅ Tetap pakai fa --}}
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fa fa-exclamation-circle fs-1 text-muted mb-2 d-block"></i>
                                        <p class="text-muted mb-0">Data wali tidak ditemukan.</p>
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
    #table-wali tbody tr:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s ease;
    }

    .btn-sm.d-inline-flex {
        width: 36px;
        height: 36px;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('q');
        if (searchInput && !searchInput.value) {
            searchInput.focus(); // Fokus ke input pencarian saat halaman load
        }
    });
</script>
@endpush
