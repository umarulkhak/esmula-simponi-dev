@extends('layouts.app_sneat_wali')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Data Anak</h5>
                <a href="{{ route($routePrefix . '.create') }}" class="btn btn-primary btn-sm">
                    <i class="bx bx-plus me-1"></i> Tambah Data
                </a>
            </div>
        </div>

        @if($models->isEmpty())
            <div class="text-center py-5">
                <i class="bx bx-empty fs-1 text-muted mb-2 d-block"></i>
                <p class="text-muted mb-0">Data tidak ditemukan.</p>
                @if(request('q'))
                    <small class="d-block mt-1">
                        <a href="{{ route($routePrefix . '.index') }}" class="text-primary">
                            Lihat semua data
                        </a>
                    </small>
                @endif
            </div>
        @else
            <div class="row g-3">
                @foreach($models as $item)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm border">
                            <div class="card-body d-flex flex-column">
                                <!-- Nama Siswa -->
                                <h6 class="fw-bold mb-2">{{ $item->nama }}</h6>

                                <!-- Info Tambahan -->
                                <div class="mb-2">
                                    <small class="text-muted">Wali Murid:</small>
                                    <div>
                                        <span class="badge bg-label-primary rounded-pill px-2 py-1">
                                            {{ $item->wali->name ?? '–' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">NISN:</small>
                                    <p class="mb-0">{{ $item->nisn ?? '–' }}</p>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">Kelas:</small>
                                    <span class="badge bg-label-{{ $item->kelas == 'VII' ? 'success' : ($item->kelas == 'VIII' ? 'warning' : 'danger') }} rounded-pill">
                                        {{ $item->kelas }}
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted">Angkatan:</small>
                                    <p class="mb-0">{{ $item->angkatan }}</p>
                                </div>

                                <!-- Aksi -->
                                <div class="mt-auto d-flex gap-1">
                                    <!-- Detail -->
                                    <a href="{{ route($routePrefix . '.show', $item->id) }}"
                                       class="btn btn-outline-info btn-sm flex-grow-1"
                                       title="Lihat Detail Siswa: {{ $item->nama }}">
                                        <i class="fa fa-info me-1"></i> Detail
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route($routePrefix . '.edit', $item->id) }}"
                                       class="btn btn-outline-warning btn-sm flex-grow-1"
                                       title="Edit Data Siswa: {{ $item->nama }}">
                                        <i class="fa fa-edit me-1"></i> Edit
                                    </a>

                                    <!-- Hapus -->
                                    <form action="{{ route($routePrefix . '.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('⚠️ Yakin ingin menghapus data siswa: {{ $item->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm flex-grow-1" title="Hapus Data Siswa: {{ $item->nama }}">
                                            <i class="fa fa-trash me-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
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
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Optional: spacing card di mobile */
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
</style>
@endpush
