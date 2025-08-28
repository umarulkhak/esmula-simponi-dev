{{-- resources/views/operator/biaya_form.blade.php --}}
@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-12">
    <div class="card">

      {{-- Judul Form --}}
      <h5 class="card-header">{{ $title }}</h5>

      <div class="card-body">
        {{-- Form menggunakan Form::model agar bisa dipakai untuk create & edit --}}
        {!! Form::model($model, [
          'route'  => $route,
          'method' => $method,
        ]) !!}

        <div class="row">

          {{-- Input: Nama Biaya --}}
          <div class="col-12 mb-3">
            <label for="nama" class="form-label">Nama Biaya</label>

            {!! Form::text('nama', old('nama', $model->nama ?? null), [
              'class'       => 'form-control',
              'id'          => 'nama',
              'placeholder' => 'Masukkan nama biaya',
              'autofocus'   => true,
            ]) !!}

            {{-- Validasi error --}}
            @error('nama')
              <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>

          {{-- Input: Jumlah / Nominal --}}
          <div class="col-12 mb-3">
            <label for="jumlah" class="form-label">Jumlah / Nominal</label>

            {!! Form::text('jumlah', old('jumlah', $model->jumlah ?? null), [
              'class'       => 'form-control rupiah',
              'id'          => 'jumlah',
              'placeholder' => 'Masukkan jumlah biaya',
            ]) !!}

            {{-- Validasi error --}}
            @error('jumlah')
              <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="form-group mt-4">
          {!! Form::submit($button, ['class' => 'btn btn-primary']) !!}
          <a href="{{ route('biaya.index') }}" class="btn btn-secondary">Batal</a>
        </div>

        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>
@endsection
