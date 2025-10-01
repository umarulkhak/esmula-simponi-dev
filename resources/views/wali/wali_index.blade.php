{{--
|--------------------------------------------------------------------------
| VIEW: Wali - Lihat Profil
|--------------------------------------------------------------------------
| Penulis     : Umar Ulkhak
| Tujuan      : Menampilkan profil wali murid dalam mode read-only.
| Fitur       :
|   - Tampilan profil sederhana & rapi
|   - Data user ditampilkan di card
|   - Tombol Edit untuk menuju ke form
|   - Responsif di desktop & mobile
|   - Clean Code
--}}

@extends('layouts.app_sneat_wali')

@section('content')
<div class="row">
    <div class="col-12 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bx bx-user me-2"></i>Profil Saya</h5>
                <a href="{{ route('wali.profile.edit', auth()->id()) }}" class="btn btn-warning btn-sm">
                    <i class="bx bx-edit me-1"></i>Edit Profil
                </a>
            </div>
            <div class="card-body">

                <!-- Opsional: Avatar (bisa dikembangkan nanti) -->
                <!--
                <div class="text-center mb-4">
                    <div class="avatar avatar-xl mb-3">
                        <span class="avatar-initial rounded-circle bg-label-primary fs-3">{{ strtoupper(substr($model->name, 0, 1)) }}</span>
                    </div>
                    <h6 class="mb-1">{{ $model->name }}</h6>
                    <small class="text-muted">{{ ucfirst($model->akses) }}</small>
                </div>
                -->

                <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bx bx-user-circle text-muted me-2"></i>
                        <span class="fw-medium text-body">Nama Lengkap</span>
                    </div>
                    <p class="mb-0 ms-4">{{ $model->name }}</p>
                </div>

                <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bx bx-envelope text-muted me-2"></i>
                        <span class="fw-medium text-body">Email</span>
                    </div>
                    <p class="mb-0 ms-4">{{ $model->email }}</p>
                </div>

                <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bx bx-phone text-muted me-2"></i>
                        <span class="fw-medium text-body">No. HP</span>
                    </div>
                    <p class="mb-0 ms-4">{{ $model->nohp ?? '–' }}</p>
                </div>

                <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bx bx-shield text-muted me-2"></i>
                        <span class="fw-medium text-body">Akses</span>
                    </div>
                    <p class="mb-0 ms-4">
                        <span class="badge bg-label-info">{{ ucfirst($model->akses) }}</span>
                    </p>
                </div>

                <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bx bx-calendar-plus text-muted me-2"></i>
                        <span class="fw-medium text-body">Terdaftar Sejak</span>
                    </div>
                    <p class="mb-0 ms-4">{{ $model->created_at->translatedFormat('d F Y') }}</p>
                </div>

                <div class="mb-0">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bx bx-time-five text-muted me-2"></i>
                        <span class="fw-medium text-body">Terakhir Diperbarui</span>
                    </div>
                    <p class="mb-0 ms-4">{{ $model->updated_at->diffForHumans() }}</p>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
