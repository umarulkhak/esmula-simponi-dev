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
    <div class="col-12 col-lg-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Profil Saya</h5>
                <a href="{{ route('wali.profile.edit', auth()->id()) }}" class="btn btn-warning">
                    Edit Profil
                </a>
            </div>
            <div class="card-body">

                <dl class="row mb-0">
                    <dt class="col-sm-4">Nama Lengkap</dt>
                    <dd class="col-sm-8">{{ $model->name }}</dd>

                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">{{ $model->email }}</dd>

                    <dt class="col-sm-4">No. HP</dt>
                    <dd class="col-sm-8">{{ $model->nohp ?? '-' }}</dd>

                    <dt class="col-sm-4">Akses</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-info text-dark">{{ ucfirst($model->akses) }}</span>
                    </dd>

                    <dt class="col-sm-4">Terdaftar Sejak</dt>
                    <dd class="col-sm-8">{{ $model->created_at->translatedFormat('d F Y') }}</dd>

                    <dt class="col-sm-4">Terakhir Diperbarui</dt>
                    <dd class="col-sm-8">{{ $model->updated_at->diffForHumans() }}</dd>
                </dl>

            </div>
        </div>
    </div>
</div>
@endsection
