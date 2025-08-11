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
                                    <th style="width: 30%;">ID</th>
                                    <td>{{ $model->id }}</td>
                                </tr>
                                <tr>
                                    <th>Nama</th>
                                    <td>{{ $model->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $model->email }}</td>
                                </tr>
                                <tr>
                                    <th>No HP</th>
                                    <td>{{ $model->nohp }}</td>
                                </tr>
                                <tr>
                                    <th>Dibuat Pada</th>
                                    <td>{{ $model->created_at ? $model->created_at->format('d-m-Y H:i') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Diubah Terakhir</th>
                                    <td>{{ $model->updated_at ? $model->updated_at->format('d-m-Y H:i') : '-' }}</td>
                                </tr>
                            </tbody>
                        </table>

                        {{-- Data Anak --}}
                        <h5 class="mt-4">Data Anak</h5>
                        <table class="table table-bordered">
                            <thead class="table-secondary">
                                <tr>
                                    <th style="width: 10%">No</th>
                                    <th style="width: 30%">NISN</th>
                                    <th>Nama</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($model->siswa as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->nisn }}</td>
                                        <td>{{ $item->nama }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Belum ada data anak</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Tombol Kembali --}}
                        <a href="{{ url()->previous() }}" class="btn btn-dark mt-3">
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