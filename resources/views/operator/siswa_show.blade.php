@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-12">

    <div class="card">
      <h5 class="card-header">{{ $title ?? 'Detail Siswa' }}</h5>

      <div class="card-body">
        <div class="row">

          {{-- Foto Siswa --}}
        <div class="col-md-4 text-center mb-3">
        @php
            $fotoPath = $model->foto && Storage::exists($model->foto)
                ? Storage::url($model->foto)
                : asset('images/no-image.png');
        @endphp
        <img 
            src="{{ $fotoPath }}" 
            alt="Foto Siswa"
            class="img-thumbnail"
            style="max-width: 100%; max-height: 250px;">
        </div>

          {{-- Detail Data Siswa --}}
          <div class="col-md-8">
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th style="width: 30%;">ID</th>
                  <td>{{ $model->id }}</td>
                </tr>
                <tr>
                  <th>Wali Murid</th>
                  <td>{{ $model->wali->name ?? '-' }}</td>
                </tr>
                <tr>
                  <th>Status Wali</th>
                  <td>
                    @if ($model->wali_status === 'ok')
                      <span class="badge bg-label-success">Aktif</span>
                    @else
                      <span class="badge bg-label-secondary">Tidak Ada</span>
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>Nama Siswa</th>
                  <td>{{ $model->nama }}</td>
                </tr>
                <tr>
                  <th>NISN</th>
                  <td>{{ $model->nisn }}</td>
                </tr>
                <tr>
                  <th>Kelas</th>
                  <td>{{ $model->kelas }}</td>
                </tr>
                <tr>
                  <th>Angkatan</th>
                  <td>{{ $model->angkatan }}</td>
                </tr>
                <tr>
                  <th>Dibuat Pada</th>
                  <td>{{ $model->created_at ? $model->created_at->format('d-m-Y H:i') : '-' }}</td>
                </tr>
                <tr>
                  <th>Diubah Terakhir</th>
                  <td>{{ $model->updated_at ? $model->updated_at->format('d-m-Y H:i') : '-' }}</td>
                </tr>
                <tr>
                  <th>Dibuat Oleh</th>
                  <td>{{ $model->user->name ?? '-' }}</td>
                </tr>
              </tbody>
            </table>

            <a href="{{ route($routePrefix . '.index') }}" class="btn btn-dark mt-3">
              <i class="fa fa-arrow-left me-1"></i> Kembali
            </a>
          </div>

        </div>
      </div>
    </div>

  </div>
</div>
@endsection

{{-- Dibuat oleh Umar Ulkhak --}}