@extends('layouts.app_sneat_wali')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bx bx-user-circle me-2"></i>
                    {{ $title ?? 'Detail Profil Siswa' }}
                </h5>
            </div>
            <div class="card-body">

                {{-- === FOTO PROFIL (Bukan Lingkaran) === --}}
                <div class="text-center mb-4">
                    @php
                        $fotoPath = $model->foto && \Storage::exists($model->foto)
                            ? \Storage::url($model->foto)
                            : asset('images/no-image.png');
                    @endphp

                    <div class="position-relative d-inline-block mb-3">
                        <img
                            src="{{ $fotoPath }}"
                            alt="Foto {{ $model->nama }}"
                            class="img-fluid rounded shadow-sm"
                            style="max-width: 180px; height: auto; border: 2px solid #f8f9fa;">
                        <div class="position-absolute bottom-0 end-0 bg-white rounded-circle p-1 shadow-sm">
                            <i class="bx bx-image text-muted fs-6"></i>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-1">{{ $model->nama }}</h5>
                    <p class="text-muted mb-0">
                        <span class="badge bg-label-{{ $model->kelas == 'VII' ? 'success' : ($model->kelas == 'VIII' ? 'warning' : 'danger') }} rounded-pill px-2 py-1">
                            Kelas {{ $model->kelas }}
                        </span>
                    </p>
                </div>

                {{-- === DETAIL DATA (TETAP SAMA) === --}}
                <div class="row g-3">

                    {{-- NISN --}}
                    <div class="col-12">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3 mt-1">
                                <div class="bg-label-info rounded p-2">
                                    <i class="bx bx-id-card fs-5"></i>
                                </div>
                            </div>
                            <div>
                                <small class="text-muted">NISN</small>
                                <div class="fw-medium">{{ $model->nisn ?? '–' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Angkatan --}}
                    <div class="col-12">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3 mt-1">
                                <div class="bg-label-secondary rounded p-2">
                                    <i class="bx bx-calendar fs-5"></i>
                                </div>
                            </div>
                            <div>
                                <small class="text-muted">Angkatan</small>
                                <div class="fw-medium">{{ $model->angkatan }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Wali Murid --}}
                    <div class="col-12">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3 mt-1">
                                <div class="bg-label-primary rounded p-2">
                                    <i class="bx bx-group fs-5"></i>
                                </div>
                            </div>
                            <div>
                                <small class="text-muted">Wali Murid</small>
                                <div class="fw-medium">{{ $model->wali->name ?? '–' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Status Wali --}}
                    <div class="col-12">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3 mt-1">
                                <div class="bg-label-{{ $model->wali_status === 'ok' ? 'success' : 'secondary' }} rounded p-2">
                                    <i class="bx bx-check-circle fs-5"></i>
                                </div>
                            </div>
                            <div>
                                <small class="text-muted">Status Wali</small>
                                <div class="fw-medium">
                                    @if($model->wali_status === 'ok')
                                        <span class="badge bg-label-success">Aktif</span>
                                    @else
                                        <span class="badge bg-label-secondary">Tidak Ada</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Dibuat Oleh --}}
                    <div class="col-12">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3 mt-1">
                                <div class="bg-label-dark rounded p-2">
                                    <i class="bx bx-user-plus fs-5"></i>
                                </div>
                            </div>
                            <div>
                                <small class="text-muted">Dibuat Oleh</small>
                                <div class="fw-medium">{{ $model->user->name ?? '–' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Waktu Dibuat --}}
                    <div class="col-12">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3 mt-1">
                                <div class="bg-label-primary rounded p-2">
                                    <i class="bx bx-time fs-5"></i>
                                </div>
                            </div>
                            <div>
                                <small class="text-muted">Dibuat Pada</small>
                                <div class="fw-medium">{{ $model->created_at?->format('d M Y H:i') ?? '–' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Waktu Diubah --}}
                    <div class="col-12">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3 mt-1">
                                <div class="bg-label-info rounded p-2">
                                    <i class="bx bx-edit fs-5"></i>
                                </div>
                            </div>
                            <div>
                                <small class="text-muted">Diubah Terakhir</small>
                                <div class="fw-medium">{{ $model->updated_at?->format('d M Y H:i') ?? '–' }}</div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- === TOMBOL AKSI === --}}
                <div class="mt-4 pt-3 border-top">
                    <div class="d-grid gap-2">
                        <a href="{{ route('wali.siswa.edit', $model->id) }}" class="btn btn-warning">
                            <i class="bx bx-edit me-1"></i> Edit Data Siswa
                        </a>
                        <a href="{{ route('wali.siswa.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Kembali
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        transition: box-shadow 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
    }
</style>
@endpush
