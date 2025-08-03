@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <h5 class="card-header">{{ $title }}</h5>

            <div class="card-body">
                {!! Form::model($model, [
                    'route'  => $route,
                    'method' => $method,
                    'files'  => true,
                ]) !!}

                {{-- Wali Murid --}}
                <div class="form-group mb-3">
                    <label for="wali_id">Wali Murid</label>
                    {!! Form::select('wali_id', $wali, null, ['class' => 'form-control']) !!}
                    <span class="text-danger">{{ $errors->first('wali_id') }}</span>
                </div>

                {{-- Nama Siswa --}}
                <div class="form-group mb-3">
                    <label for="nama">Nama</label>
                    {!! Form::text('nama', null, ['class' => 'form-control', 'autofocus' => true]) !!}
                    <span class="text-danger">{{ $errors->first('nama') }}</span>
                </div>

                {{-- NISN --}}
                <div class="form-group mb-3">
                    <label for="nisn">NISN</label>
                    {!! Form::text('nisn', null, ['class' => 'form-control']) !!}
                    <span class="text-danger">{{ $errors->first('nisn') }}</span>
                </div>

                {{-- Kelas --}}
                <div class="form-group mb-3">
                    <label for="kelas">Kelas</label>
                    {!! Form::text('kelas', null, ['class' => 'form-control']) !!}
                    <span class="text-danger">{{ $errors->first('kelas') }}</span>
                </div>

                {{-- Tahun Angkatan --}}
                <div class="form-group mb-3">
                    <label for="angkatan">Tahun Angkatan</label>
                    {!! Form::selectRange('angkatan', 2023, date('Y') + 1, null, ['class' => 'form-control']) !!}
                    <span class="text-danger">{{ $errors->first('angkatan') }}</span>
                </div>

                {{-- Upload Foto --}}
                <div class="form-group mb-3">
                    <label for="foto">Foto <small class="text-muted">(Format: jpg, png &max; 5MB)</small></label>
                    {!! Form::file('foto', ['class' => 'form-control', 'accept' => 'image/*']) !!}
                    <span class="text-danger">{{ $errors->first('foto') }}</span>
                </div>

                {{-- Tombol Submit --}}
                <div class="form-group mt-4">
                    {!! Form::submit($button, ['class' => 'btn btn-primary']) !!}
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection