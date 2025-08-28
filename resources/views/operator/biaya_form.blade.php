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
            {!! Form::label('nama', 'Nama Biaya', ['class' => 'form-label']) !!}
            {!! Form::text('nama', old('nama', $model->nama ?? null), [
              'class'       => 'form-control' . ($errors->has('nama') ? ' is-invalid' : ''),
              'id'          => 'nama',
              'placeholder' => 'Masukkan nama biaya',
              'autofocus'   => true,
            ]) !!}
            @error('nama')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Input: Jumlah / Nominal --}}
          <div class="col-12 mb-3">
            {!! Form::label('jumlah', 'Jumlah / Nominal', ['class' => 'form-label']) !!}
            {!! Form::text('jumlah', old('jumlah', $model->jumlah ?? null), [
              'class'       => 'form-control rupiah' . ($errors->has('jumlah') ? ' is-invalid' : ''),
              'id'          => 'jumlah',
              'placeholder' => 'Masukkan jumlah biaya',
            ]) !!}
            @error('jumlah')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

        </div>

        {{-- Tombol Aksi --}}
        <div class="form-group mt-4 d-flex gap-2">
          {!! Form::submit($button, ['class' => 'btn btn-primary']) !!}
          <a href="{{ route('biaya.index') }}" class="btn btn-secondary">Batal</a>
        </div>

        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>
@endsection
