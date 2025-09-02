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

        <div class="row">
          {{-- Wali Murid --}}
          <div class="col-12 mb-3">
            <label for="wali_id">Wali Murid</label>
            {!! Form::select('wali_id', $wali, null, [
              'class' => 'form-control select2' . ($errors->has('wali_id') ? ' is-invalid' : ''),
              'placeholder' => '-- Pilih Wali Murid --',
              'id' => 'wali_id',
            ]) !!}
            @error('wali_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Nama Siswa --}}
          <div class="col-12 mb-3">
            <label for="nama">Nama</label>
            {!! Form::text('nama', null, [
              'class' => 'form-control' . ($errors->has('nama') ? ' is-invalid' : ''),
              'id' => 'nama',
              'autofocus' => true,
            ]) !!}
            @error('nama')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- NISN --}}
          <div class="col-12 mb-3">
            <label for="nisn">NISN</label>
            {!! Form::text('nisn', null, [
              'class' => 'form-control' . ($errors->has('nisn') ? ' is-invalid' : ''),
              'id' => 'nisn',
            ]) !!}
            @error('nisn')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Kelas --}}
          <div class="col-12 mb-3">
            <label for="kelas">Kelas</label>
            {!! Form::text('kelas', null, [
              'class' => 'form-control' . ($errors->has('kelas') ? ' is-invalid' : ''),
              'id' => 'kelas',
            ]) !!}
            @error('kelas')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Tahun Angkatan --}}
          <div class="col-12 mb-3">
            <label for="angkatan">Tahun Angkatan</label>
            {!! Form::selectRange('angkatan', 2023, date('Y') + 1, null, [
              'class' => 'form-control' . ($errors->has('angkatan') ? ' is-invalid' : ''),
              'id' => 'angkatan',
            ]) !!}
            @error('angkatan')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Preview Foto --}}
          <div class="col-12 mb-3">
            <p class="mb-1">Preview Foto</p>
            <div class="border rounded p-2" style="max-width: 220px;">
              @php
                $fotoPath = $model->foto && \Storage::exists($model->foto)
                            ? \Storage::url($model->foto)
                            : asset('images/no-image.png');
              @endphp
              <img id="preview-foto"
                   src="{{ $fotoPath }}"
                   alt="Foto Siswa"
                   class="img-thumbnail w-100">
            </div>
          </div>

          {{-- Upload Foto --}}
          <div class="col-12 mb-3">
            <label for="foto">
              Foto <small class="text-muted">(jpg/png max 5MB)</small>
            </label>
            {!! Form::file('foto', [
              'class' => 'form-control' . ($errors->has('foto') ? ' is-invalid' : ''),
              'accept' => 'image/*',
              'id' => 'foto',
            ]) !!}
            @error('foto')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
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

@push('scripts')
<script>
  document.getElementById('foto').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file && file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = function (evt) {
        document.getElementById('preview-foto').src = evt.target.result;
      }
      reader.readAsDataURL(file);
    }
  });
</script>
@endpush
