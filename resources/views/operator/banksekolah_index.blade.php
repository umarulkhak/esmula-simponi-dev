{{--
|--------------------------------------------------------------------------
| VIEW: Operator - Daftar Bank Sekolah
|--------------------------------------------------------------------------
| Penulis     : Umar Ulkhak (Modifikasi oleh AI Assistant)
| Tujuan      : Menampilkan daftar rekening bank sekolah dengan fitur pencarian, aksi, dan pagination.
| Fitur       :
|   - Tombol tambah data
|   - Pencarian real-time (berdasarkan nama bank atau nomor rekening)
|   - Tabel responsif dengan aksi: Edit, Hapus (icon-only style)
|   - Format data bank: nama, nomor, atas nama, kode
|   - Pagination Bootstrap
|   - Responsif di mobile
|   - UX Friendly: konfirmasi hapus, ikon, spacing konsisten
|   - Clean Code: struktur blade rapi, komentar jelas
|
| Variabel yang diharapkan dari Controller:
|   - $title       → Judul halaman
|   - $routePrefix → Prefix route (misal: 'bank-sekolah')
|   - $models     → Collection paginate bank sekolah
|
--}}

@extends('layouts.app_sneat')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $title }}</h5>
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
                                <label for="q" class="form-label visually-hidden">Cari Bank</label>
                                <input
                                    type="text"
                                    name="q"
                                    id="q"
                                    class="form-control"
                                    placeholder="🔍 Cari Nama Bank / Nomor Rekening..."
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
                        <table class="table table-hover align-middle" id="table-bank">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 5%">#</th>
                                    <th>Kode</th>
                                    <th>Nama Bank</th>
                                    <th>Atas Nama</th>
                                    <th>Nomor Rekening</th>
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
                                            <span class="badge bg-label-secondary rounded-pill px-2 py-1">
                                                {{ $item->kode ?? '–' }}
                                            </span>
                                        </td>
                                        <td class="fw-semibold">{{ $item->nama_bank }}</td>
                                        <td>{{ $item->nama_rekening }}</td>
                                        <td class="font-monospace">{{ $item->nomor_rekening }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                {{-- Tombol Edit --}}
                                                <a href="{{ route($routePrefix . '.edit', $item->id) }}"
                                                   class="btn btn-outline-warning btn-sm d-inline-flex align-items-center justify-content-center"
                                                   title="Edit Bank: {{ $item->nama_bank }}">
                                                    <i class="fa fa-edit"></i>
                                                </a>

                                                {{-- Tombol Hapus --}}
                                                <form action="{{ route($routePrefix . '.destroy', $item->id) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('⚠️ Yakin ingin menghapus bank: {{ $item->nama_bank }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center"
                                                            title="Hapus Bank: {{ $item->nama_bank }}">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
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
                                Menampilkan {{ $models->firstItem() }}–{{ $models->lastItem() }} dari {{ $models->total() }} data
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
        /* Hover effect lebih smooth */
        #table-bank tbody tr:hover {
            background-color: #f8f9fa;
            transition: background-color 0.2s ease;
        }

        /* Tombol aksi lebih kotak dan proporsional */
        .btn-sm.d-inline-flex {
            width: 36px;
            height: 36px;
        }

        /* Nomor rekening tampil monospace agar mudah dibaca */
        .font-monospace {
            font-family: var(--bs-font-monospace);
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('q');
            if (searchInput && !searchInput.value) {
                searchInput.focus();
            }
        });
    </script>
@endpush
