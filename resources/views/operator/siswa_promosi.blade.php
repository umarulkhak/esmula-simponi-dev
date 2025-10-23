@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header text-center bg-warning bg-opacity-10">
                <h5 class="mb-0 text-warning">
                    <i class="bx bx-up-arrow-circle me-2"></i>
                    PROMOSI & KELULUSAN SISWA
                </h5>
                <small class="text-muted">Tahun Ajaran {{ $tahunSekarang }}/{{ $tahunSekarang + 1 }}</small>
            </div>
            <div class="card-body">

                <div class="alert alert-info">
                    <i class="bx bx-info-circle me-2"></i>
                    Fitur ini akan:
                    <ul class="mb-0 mt-2">
                        <li>Naikkan siswa kelas <strong>VII → VIII</strong></li>
                        <li>Naikkan siswa kelas <strong>VIII → IX</strong></li>
                        <li>Tandai siswa kelas <strong>IX sebagai LULUS</strong> (status diubah, data tetap tersimpan)</li>
                    </ul>
                </div>

                <div class="row text-center g-3 mb-4">
                    <div class="col-4">
                        <div class="bg-label-success rounded p-3">
                            <div class="fs-5 fw-bold">{{ $jumlahVII }}</div>
                            <div class="small">Kelas VII</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="bg-label-warning rounded p-3">
                            <div class="fs-5 fw-bold">{{ $jumlahVIII }}</div>
                            <div class="small">Kelas VIII</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="bg-label-danger rounded p-3">
                            <div class="fs-5 fw-bold">{{ $jumlahIX }}</div>
                            <div class="small">Kelas IX</div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning">
                    <i class="bx bx-error me-2"></i>
                    <strong>Peringatan!</strong> Tindakan ini <strong>tidak bisa dibatalkan</strong>. Pastikan semua data sudah benar.
                </div>

                {!! Form::open(['route' => 'siswa.promosi.proses', 'method' => 'POST']) !!}
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="confirm" id="confirm" required>
                        <label class="form-check-label" for="confirm">
                            Saya yakin ingin memproses promosi & kelulusan tahun {{ $tahunSekarang }}.
                            Data siswa kelas IX akan diubah statusnya menjadi <strong>LULUS</strong>.
                        </label>
                    </div>

                    <div class="d-flex gap-2 justify-content-center">
                        <button type="submit" class="btn btn-danger px-4">
                            <i class="bx bx-check-circle me-1"></i> Jalankan Promosi
                        </button>
                        <a href="{{ route('siswa.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Batal
                        </a>
                    </div>
                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>
@endsection
