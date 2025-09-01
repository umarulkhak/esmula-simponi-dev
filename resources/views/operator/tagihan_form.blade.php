{{-- resources/views/operator/tagihan_form.blade.php --}}
@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-12">
    <div class="card">

      {{-- Judul Form --}}
      <h5 class="card-header">{{ $title }}</h5>

      <div class="card-body">
        {{-- Form create & edit --}}
        {!! Form::model($model, ['route' => $route, 'method' => $method]) !!}

        <div class="row">

          {{-- Biaya --}}
          <div class="col-12 mb-3">
            <label class="form-label fw-bold">Biaya Yang Ditagihkan</label>
            <div class="row">
              @foreach($biaya as $id => $nama)
                <div class="col-md-4 mb-3">
                  <label for="biaya_{{ $id }}"
                         class="card h-100 shadow-sm biaya-card cursor-pointer">
                    <div class="card-body d-flex align-items-center">
                      <div class="form-check m-0">
                        {!! Form::checkbox(
                            'biaya_id[]',
                            $id,
                            in_array($id, old('biaya_id', $model->biaya_id ?? [])),
                            ['class' => 'form-check-input me-2', 'id' => 'biaya_'.$id]
                        ) !!}
                        <span class="form-check-label">{{ $nama }}</span>
                      </div>
                    </div>
                  </label>
                </div>
              @endforeach
            </div>

            @error('biaya_id')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          {{-- Angkatan --}}
          <div class="col-md-6 mb-3">
            {!! Form::label('angkatan', 'Tagihan Untuk Angkatan', ['class' => 'form-label']) !!}
            {!! Form::select(
                'angkatan',
                $angkatan,
                old('angkatan', $model->angkatan ?? null),
                [
                    'class'       => 'form-select' . ($errors->has('angkatan') ? ' is-invalid' : ''),
                    'id'          => 'angkatan',
                    'placeholder' => '-- Pilih Tahun Angkatan --',
                    'autofocus'   => true,
                ]
            ) !!}
            @error('angkatan')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Kelas --}}
          <div class="col-md-6 mb-3">
            {!! Form::label('kelas', 'Kelas', ['class' => 'form-label']) !!}
            {!! Form::select(
                'kelas',
                $kelas,
                old('kelas', $model->kelas ?? null),
                [
                    'class'       => 'form-select' . ($errors->has('kelas') ? ' is-invalid' : ''),
                    'id'          => 'kelas',
                    'placeholder' => '-- Pilih Kelas --',
                ]
            ) !!}
            @error('kelas')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Tanggal Tagihan --}}
          <div class="col-md-6 mb-3">
            {!! Form::label('tanggal_tagihan', 'Tanggal Tagihan', ['class' => 'form-label']) !!}
            {!! Form::date(
                'tanggal_tagihan',
                old('tanggal_tagihan', $model->tanggal_tagihan ?? date('Y-m-d')),
                [
                    'class' => 'form-control' . ($errors->has('tanggal_tagihan') ? ' is-invalid' : ''),
                    'id'    => 'tanggal_tagihan',
                ]
            ) !!}
            @error('tanggal_tagihan')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Tanggal Jatuh Tempo --}}
          <div class="col-md-6 mb-3">
            {!! Form::label('tanggal_jatuh_tempo', 'Tanggal Jatuh Tempo', ['class' => 'form-label']) !!}
            {!! Form::date(
                'tanggal_jatuh_tempo',
                old('tanggal_jatuh_tempo', $model->tanggal_jatuh_tempo ?? date('Y-m-d')),
                [
                    'class' => 'form-control' . ($errors->has('tanggal_jatuh_tempo') ? ' is-invalid' : ''),
                    'id'    => 'tanggal_jatuh_tempo',
                ]
            ) !!}
            @error('tanggal_jatuh_tempo')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Keterangan --}}
          <div class="col-12 mb-3">
            {!! Form::label('keterangan', 'Keterangan', ['class' => 'form-label']) !!}
            {!! Form::textarea(
                'keterangan',
                old('keterangan', $model->keterangan ?? null),
                [
                    'class' => 'form-control' . ($errors->has('keterangan') ? ' is-invalid' : ''),
                    'id'    => 'keterangan',
                    'rows'  => 3,
                ]
            ) !!}
            @error('keterangan')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="mt-4 d-flex gap-2">
          {!! Form::submit($button, ['class' => 'btn btn-primary']) !!}
          <a href="{{ route('tagihan.index') }}" class="btn btn-secondary">Batal</a>
        </div>

        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .biaya-card {
    transition: all 0.25s ease-in-out;
    cursor: pointer;
    border: 2px solid #dee2e6;
    border-radius: 0.5rem;
    background-color: #fff;
  }
  .biaya-card:hover {
    border-color: #0d6efd;
    background-color: #f0f6ff;
    box-shadow: 0 0.5rem 1rem rgba(13, 110, 253, 0.15);
  }
  .biaya-card .form-check-input {
    display: none;
  }
  .biaya-card input[type="checkbox"]:checked ~ .form-check-label {
    font-weight: 600;
    color: #fff;
    background-color: #0d6efd;
    padding: 0.4rem 0.75rem;
    border-radius: 0.4rem;
    display: inline-block;
  }
  .biaya-card input[type="checkbox"]:checked ~ .form-check-label::before {
    content: "✓ ";
  }
</style>
@endpush
