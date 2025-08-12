@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">

        <div class="card">
            <h5 class="card-header">{{ $title ?? 'Detail Wali Murid' }}</h5>

            <div class="card-body">
                <div class="row">

                    {{-- Detail Data Wali --}}
                    <div class="col-md-8">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th class="table-light w-30">ID</th>
                                    <td>{{ $model->id }}</td>
                                </tr>
                                <tr>
                                    <th class="table-light">Nama</th>
                                    <td>{{ $model->name }}</td>
                                </tr>
                                <tr>
                                    <th class="table-light">Email</th>
                                    <td>{{ $model->email }}</td>
                                </tr>
                                <tr>
                                    <th class="table-light">No HP</th>
                                    <td>{{ $model->nohp }}</td>
                                </tr>
                                <tr>
                                    <th class="table-light">Dibuat Pada</th>
                                    <td>{{ optional($model->created_at)->format('d-m-Y H:i') ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="table-light">Diubah Terakhir</th>
                                    <td>{{ optional($model->updated_at)->format('d-m-Y H:i') ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>

                        {{-- Tambah Data Anak --}}
                        <h5 class="mt-4">Tambah Data Anak</h5>
                        {!! Form::open(['route' => 'walisiswa.store', 'method' => 'POST']) !!}
                            {!! Form::hidden('wali_id', $model->id) !!}

                            <div class="form-group">
                                {!! Form::label('siswa_id', 'Pilih Data Siswa') !!}
                                {!! Form::select('siswa_id', $siswa, null, [
                                    'class' => 'form-control select2',
                                    'id' => 'siswa_id',
                                    'placeholder' => '-- Pilih Siswa --'
                                ]) !!}
                                <span class="text-danger">{{ $errors->first('siswa_id') }}</span>
                            </div>

                            {!! Form::button('<i class="fa-regular fa-floppy-disk"></i> Simpan', [
                                'type' => 'submit',
                                'class' => 'btn btn-primary my-2'
                            ]) !!}
                        {!! Form::close() !!}

                        {{-- Data Anak --}}
                        <h5 class="mt-4">Data Anak</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-secondary">
                                    <tr>
                                        <th class="w-10">No</th>
                                        <th class="w-15">NISN</th>
                                        <th>Nama</th>
                                        <th class="w-20">Kelas</th>
                                        <th class="w-20">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($model->siswa as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->nisn }}</td>
                                            <td>{{ $item->nama }}</td>
                                            <td>{{ $item->kelas }}</td>
                                            <td>
                                                {!! Form::open([
                                                    'route' => ['walisiswa.update', $item->id],
                                                    'method' => 'PUT',
                                                    'onsubmit' => 'return confirm("Yakin ingin menghapus data ini?")',
                                                    'class' => 'd-inline'
                                                ]) !!}
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fa fa-trash"></i> Hapus
                                                    </button>
                                                {!! Form::close() !!}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Belum ada data anak</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Tombol Kembali --}}
                        <a href="{{ url()->previous() }}" class="btn btn-dark btn-sm mt-3">
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
