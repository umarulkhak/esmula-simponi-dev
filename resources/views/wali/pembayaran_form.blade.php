@extends('layouts.app_sneat_wali')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-0 py-3 px-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bx bx-credit-card me-2"></i>Konfirmasi Pembayaran
                    </h5>
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm px-2 rounded-3" title="Kembali">
                        <i class="bx bx-arrow-back"></i>
                    </a>
                </div>
            </div>

            <div class="card-body p-3 p-md-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                        <i class="bx bx-check-circle fs-5 me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                        <i class="bx bx-error-circle fs-5 me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {!! Form::model($model, ['route' => $route, 'method' => $method, 'files' => true, 'id' => 'formPembayaran']) !!}

                <!-- REKENING PENGIRIM -->
                <section class="mb-3 mt-0"> {{-- mt-0 mengurangi jarak dari atas --}}
                    <div class="divider divider-dark my-2">
                        <div class="divider-text">
                            <h6 class="mb-0"><i class="fa-solid fa-circle-info text-dark me-1"></i>Rekening Pengirim</h6>
                        </div>
                    </div>

                    @if($listWaliBank && count($listWaliBank) > 0)
                        <div class="form-group mb-3" id="formGroupBankTersimpan">
                            <label class="form-label fw-medium" for="wali_bank_id">Bank Tersimpan</label>
                            {!! Form::select('wali_bank_id', $listWaliBank, null, [
                                'class' => 'form-select select2',
                                'placeholder' => 'Pilih Bank Pengirim',
                                'id' => 'wali_bank_id'
                            ]) !!}
                            @error('wali_bank_id')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            {!! Form::checkbox('pilihan_bank', 1, false, [
                                'class' => 'form-check-input',
                                'id' => 'checkboxtoggle'
                            ]) !!}
                            <label class="form-check-label" for="checkboxtoggle">
                                Saya punya rekening baru
                            </label>
                        </div>
                    @endif

                    <div id="infoRekeningBaru" class="alert alert-info d-flex align-items-center {{ ($listWaliBank && count($listWaliBank) > 0) ? 'd-none' : '' }} p-2">
                        <i class="bx bx-info-circle me-2"></i>
                        <small>Isi data rekening baru agar operator dapat verifikasi transfer Anda.</small>
                    </div>

                    <div id="formBankBaru" class="mt-3 {{ ($listWaliBank && count($listWaliBank) > 0) ? 'd-none' : '' }}">
                        <div class="row g-2">
                            <div class="col-12">
                                <label for="bank_id" class="form-label fw-medium">Nama Bank Pengirim</label>
                                {!! Form::select('bank_id', $listBank ?? [], null, [
                                    'class' => 'form-select select2',
                                    'placeholder' => 'Pilih bank pengirim...',
                                    'id' => 'bank_id'
                                ]) !!}
                                @error('bank_id')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium" for="nama_rekening">Nama Pemilik Rekening</label>
                                {!! Form::text('nama_rekening', null, [
                                    'class' => 'form-control',
                                    'placeholder' => 'Nama sesuai buku tabungan',
                                    'id' => 'nama_rekening'
                                ]) !!}
                                @error('nama_rekening')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium" for="nomor_rekening">Nomor Rekening</label>
                                {!! Form::text('nomor_rekening', null, [
                                    'class' => 'form-control',
                                    'placeholder' => 'Contoh: 1234567890',
                                    'id' => 'nomor_rekening'
                                ]) !!}
                                @error('nomor_rekening')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    {!! Form::checkbox('simpan_data_rekening', 1, true, [
                                        'class' => 'form-check-input',
                                        'id' => 'defaultCheck3'
                                    ]) !!}
                                    <label for="defaultCheck3" class="form-check-label">
                                        <small>Simpan data ini untuk pembayaran berikutnya</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- REKENING TUJUAN -->
                <section class="mb-3">
                    <div class="divider divider-dark my-2">
                        <div class="divider-text">
                            <h6 class="mb-0"><i class="fa-solid fa-circle-info text-dark me-1"></i>Rekening Tujuan</h6>
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <label for="bank_sekolah_id" class="form-label fw-medium">Bank Tujuan Pembayaran</label>
                        {!! Form::select('bank_sekolah_id', $listBankSekolah, request('bank_sekolah_id'), [
                            'class' => 'form-select select2',
                            'placeholder' => 'Pilih Bank Tujuan Transfer',
                            'id' => 'bank_sekolah_id'
                        ]) !!}
                        @error('bank_sekolah_id')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    @if(request('bank_sekolah_id') && $bankYangDipilih)
                        <div class="alert alert-light border rounded-2 p-2 mt-2 small">
                            <ul class="list-unstyled mb-0">
                                <li><strong>Bank:</strong> {{ $bankYangDipilih->nama_bank }}</li>
                                <li><strong>No. Rekening:</strong> {{ $bankYangDipilih->nomor_rekening }}</li>
                                <li><strong>Atas Nama:</strong> {{ $bankYangDipilih->nama_rekening }}</li>
                            </ul>
                        </div>
                    @endif
                </section>

                <!-- INFO PEMBAYARAN -->
                <section class="mb-3">
                    <div class="divider divider-dark my-2">
                        <div class="divider-text">
                            <h6 class="mb-0"><i class="fa-solid fa-circle-info text-dark me-1"></i>Info Pembayaran</h6>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-12">
                            <label for="tanggal_bayar" class="form-label fw-medium">Tanggal Bayar</label>
                            {!! Form::date('tanggal_bayar', $model->tanggal_bayar ?? date('Y-m-d'), [
                                'class' => 'form-control'
                            ]) !!}
                        </div>

                        <div class="col-12">
                            <label for="jumlah_dibayar" class="form-label fw-medium">Jumlah Dibayar</label>
                            {!! Form::text('jumlah_dibayar', $tagihan->tagihanDetails->sum('jumlah_biaya'), [
                                'class' => 'form-control rupiah',
                                'placeholder' => 'Contoh: 500.000',
                                'id' => 'jumlah_dibayar'
                            ]) !!}
                        </div>

                        <div class="col-12">
                            <label for="bukti_bayar" class="form-label fw-medium">
                                <i class="bi bi-upload me-1"></i> Upload Bukti (JPG/PNG/PDF) <span class="text-danger">*</span>
                            </label>
                            {!! Form::file('bukti_bayar', [
                                'class' => 'form-control',
                                'accept' => 'image/*,.pdf',
                                'id' => 'bukti_bayar'
                            ]) !!}
                            <div class="form-text small text-muted">Maks. 2MB</div>
                            <div id="previewContainer" class="mt-2 d-none"></div>
                        </div>
                    </div>
                </section>

                <input type="hidden" name="tagihan_id" value="{{ $tagihan->id }}">

                <div class="d-grid gap-2 d-sm-flex justify-content-end mt-3">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary px-3 rounded-3">Batal</a>
                    <button type="submit" class="btn btn-dark px-4 rounded-3" id="btnSimpan">
                        <span class="spinner-border spinner-border-sm d-none me-2" id="loadingSpinner"></span>
                        <span id="btnText">Simpan</span>
                    </button>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Opsional: sesuaikan tinggi Select2 di mobile */
    .select2-container .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px) !important;
        padding: 0.375rem 0.75rem !important;
        font-size: 0.95rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5 !important;
        padding-left: 0 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + 0.75rem + 2px) !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Inisialisasi Select2
    $('.select2').select2({
        width: '100%',
        dropdownAutoWidth: true,
        minimumResultsForSearch: 5 // sembunyikan pencarian jika pilihan < 5
    });

    const hasSavedBank = {{ ($listWaliBank && count($listWaliBank) > 0) ? 'true' : 'false' }};
    const formBankBaru = document.getElementById('formBankBaru');
    const infoRekeningBaru = document.getElementById('infoRekeningBaru');
    const checkboxToggle = document.getElementById('checkboxtoggle');

    if (!hasSavedBank) {
        formBankBaru.classList.remove('d-none');
        infoRekeningBaru.classList.remove('d-none');
    } else if (checkboxToggle) {
        checkboxToggle.addEventListener('change', () => {
            const isChecked = checkboxToggle.checked;
            formBankBaru.classList.toggle('d-none', !isChecked);
            infoRekeningBaru.classList.toggle('d-none', !isChecked);
        });
    }

    // Redirect saat pilih bank tujuan
    const bankSekolahSelect = document.getElementById('bank_sekolah_id');
    bankSekolahSelect?.addEventListener('change', function () {
        const url = new URL(window.location.href);
        if (this.value) url.searchParams.set('bank_sekolah_id', this.value);
        else url.searchParams.delete('bank_sekolah_id');
        window.location.href = url.toString();
    });

    // Preview bukti bayar
    const buktiBayarEl = document.getElementById('bukti_bayar');
    const previewContainer = document.getElementById('previewContainer');

    function createCloseButton() {
        const btn = document.createElement('button');
        btn.className = 'btn-close position-absolute top-0 end-0';
        btn.style.transform = 'translate(50%, -50%)';
        btn.onclick = () => {
            buktiBayarEl.value = '';
            previewContainer.classList.add('d-none');
            previewContainer.innerHTML = '';
        };
        return btn;
    }

    buktiBayarEl?.addEventListener('change', function () {
        const file = this.files[0];
        previewContainer.innerHTML = '';
        if (!file) return previewContainer.classList.add('d-none');

        const wrapper = document.createElement('div');
        wrapper.className = 'position-relative d-inline-block me-2';

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'border rounded-2';
                img.style.width = '100px';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                wrapper.appendChild(img);
                wrapper.appendChild(createCloseButton());
                previewContainer.appendChild(wrapper);
                previewContainer.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        } else if (file.type === 'application/pdf') {
            const div = document.createElement('div');
            div.className = 'bg-light border rounded-2 d-flex align-items-center justify-content-center p-2';
            div.style.width = '100px';
            div.style.height = '100px';
            div.innerHTML = `<i class="bi bi-file-pdf fs-1 text-danger"></i>`;
            wrapper.appendChild(div);
            wrapper.appendChild(createCloseButton());
            previewContainer.appendChild(wrapper);
            previewContainer.classList.remove('d-none');
        }
    });

    // Format rupiah
    document.querySelectorAll('.rupiah').forEach(input => {
        input.addEventListener('keyup', () => {
            let value = input.value.replace(/\D/g, '');
            if (value) input.value = new Intl.NumberFormat('id-ID').format(value);
        });
        input.form?.addEventListener('submit', () => {
            input.value = input.value.replace(/\D/g, '');
        });
    });

    // Loading state
    const form = document.getElementById('formPembayaran');
    const btnSimpan = document.getElementById('btnSimpan');
    const btnText = document.getElementById('btnText');
    const spinner = document.getElementById('loadingSpinner');
    form?.addEventListener('submit', () => {
        btnSimpan.disabled = true;
        spinner.classList.remove('d-none');
        btnText.textContent = 'Menyimpan...';
    });
});
</script>
@endpush
