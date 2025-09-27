@extends('layouts.app_sneat_wali')

{{--
Form Konfirmasi Pembayaran SPP oleh Wali Murid

Fitur:
- Sembunyikan "Bank Tersimpan" jika user belum pernah menyimpan rekening
- Toggle rekening baru pakai checkbox (dengan animasi)
- Redirect otomatis jika bank tujuan dipilih
- Preview file upload (gambar / PDF)
- Format Rupiah otomatis
- Loading state saat submit

@author Umar
@version 3.1
--}}

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-0 pb-0">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 fw-bold">Konfirmasi Pembayaran</h5>
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm px-3">
                        <i class="bx bx-arrow-back"></i>
                    </a>
                </div>
            </div>

            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {!! Form::model($model, ['route'=>$route, 'method'=>$method, 'files'=>true, 'id'=>'formPembayaran']) !!}

                <!-- REKENING PENGIRIM -->
                <section class="mb-4">
                    <div class="divider divider-dark">
                        <div class="divider-text">
                            <i class="fa-solid fa-circle-info me-1"></i> Rekening Pengirim
                        </div>
                    </div>

                    {{-- Tampilkan dropdown hanya jika user sudah punya rekening tersimpan --}}
                    @if($listWaliBank && count($listWaliBank) > 0)
                        <div class="form-group" id="formGroupBankTersimpan">
                            <label class="form-label" for="bank_id_pengirim">Bank Tersimpan</label>
                            {!! Form::select('bank_id_pengirim', $listWaliBank, null, [
                                'class'=>'form-control select2',
                                'placeholder'=>'Pilih Bank Pengirim',
                                'id'=>'bank_id_pengirim'
                            ]) !!}
                            <span class="text-danger">{{ $errors->first('bank_id_pengirim') }}</span>
                        </div>

                        <div class="form-check mt-2" id="checkboxRekeningBaruWrapper">
                            <input type="checkbox" class="form-check-input" id="chkGunakanBankBaru">
                            <label for="chkGunakanBankBaru" class="form-check-label">
                                Saya punya rekening baru
                            </label>
                        </div>
                    @endif

                    <!-- Info -->
                    <div id="infoRekeningBaru" class="alert alert-info mt-2 {{ ($listWaliBank && count($listWaliBank) > 0) ? 'd-none' : '' }}">
                        Isi data rekening baru agar operator dapat verifikasi transfer Anda.
                    </div>

                    <!-- Form Input Manual -->
                    <div id="formBankBaru" class="mt-3" style="{{ ($listWaliBank && count($listWaliBank) > 0) ? 'display:none; overflow:hidden;' : '' }}">
                        <div class="form-group mb-3">
                            <label for="bank_pengirim_id" class="form-label">Nama Bank Pengirim</label>
                            {!! Form::select('bank_pengirim_id', $listBank ?? [], null, [
                                'class'=>'form-control select2',
                                'placeholder'=>'Pilih bank pengirim...',
                                'id'=>'bank_pengirim_id'
                            ]) !!}
                            <span class="text-danger">{{ $errors->first('bank_pengirim_id') }}</span>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="nama_pengirim">Nama Pemilik Rekening</label>
                            {!! Form::text('nama_pengirim', null, [
                                'class'=>'form-control',
                                'placeholder'=>'Nama sesuai buku tabungan',
                                'id'=>'nama_pengirim'
                            ]) !!}
                            <span class="text-danger">{{ $errors->first('nama_pengirim') }}</span>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="no_rekening_pengirim">Nomor Rekening</label>
                            {!! Form::text('no_rekening_pengirim', null, [
                                'class'=>'form-control',
                                'placeholder'=>'Contoh: 1234567890',
                                'id'=>'no_rekening_pengirim'
                            ]) !!}
                            <span class="text-danger">{{ $errors->first('no_rekening_pengirim') }}</span>
                        </div>

                        <div class="form-check">
                            {!! Form::checkbox('simpan_data_rekening', 1, true, ['class'=>'form-check-input','id'=>'defaultCheck3']) !!}
                            <label for="defaultCheck3" class="form-check-label">
                                <small>Simpan data ini untuk pembayaran berikutnya</small>
                            </label>
                        </div>
                    </div>
                </section>

                <!-- REKENING TUJUAN -->
                <section class="mb-4">
                    <div class="divider divider-dark">
                        <div class="divider-text"><i class="fa-solid fa-circle-info me-1"></i> Rekening Tujuan</div>
                    </div>

                    <div class="form-group">
                        <label for="bank_sekolah_id">Bank Tujuan Pembayaran</label>
                        {!! Form::select('bank_sekolah_id', $listBankSekolah, request('bank_sekolah_id'), [
                            'class'=>'form-control',
                            'placeholder'=>'Pilih Bank Tujuan Transfer',
                            'id'=>'bank_sekolah_id'
                        ]) !!}
                        <span class="text-danger">{{ $errors->first('bank_sekolah_id') }}</span>
                    </div>

                    @if (request('bank_sekolah_id'))
                        <div class="alert alert-primary mt-2">
                            <table class="table table-sm mb-0">
                                <tbody>
                                    <tr><td>Bank</td><td>{{ $bankYangDipilih->nama_bank }}</td></tr>
                                    <tr><td>No. Rekening</td><td>{{ $bankYangDipilih->nomor_rekening }}</td></tr>
                                    <tr><td>Atas Nama</td><td>{{ $bankYangDipilih->nama_rekening }}</td></tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>

                <input type="hidden" name="tagihan_id" value="{{ $tagihan->id }}">

                <!-- INFO PEMBAYARAN -->
                <section class="mb-4">
                    <div class="divider divider-dark">
                        <div class="divider-text"><i class="fa-solid fa-circle-info me-1"></i> Info Pembayaran</div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="tanggal_bayar" class="form-label">Tanggal Bayar</label>
                        {!! Form::date('tanggal_bayar', $model->tanggal_bayar ?? date('Y-m-d'), [
                            'class'=>'form-control','required'=>true
                        ]) !!}
                    </div>

                    <div class="form-group mb-3">
                        <label for="jumlah_dibayar" class="form-label">Jumlah Dibayar</label>
                        {!! Form::text('jumlah_dibayar', null, [
                            'class'=>'form-control rupiah',
                            'placeholder'=>'Contoh: 500.000',
                            'id'=>'jumlah_dibayar'
                        ]) !!}
                    </div>

                    <div class="mb-3">
                        <label for="bukti_bayar" class="form-label fw-medium">
                            <i class="bi bi-upload me-1"></i> Upload Bukti (JPG/PNG/PDF) <span class="text-danger">*</span>
                        </label>
                        {!! Form::file('bukti_bayar', ['class'=>'form-control','accept'=>'image/*,.pdf','id'=>'bukti_bayar']) !!}
                        <div class="form-text small">Maks. 2MB</div>
                        <div id="previewContainer" class="mt-2 d-none"></div>
                    </div>
                </section>

                <div class="d-flex justify-content-end">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-dark" id="btnSimpan">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const isFirstPayment = {{ ($listWaliBank && count($listWaliBank) > 0) ? 'false' : 'true' }};

    // ==== SLIDE UTILITY ====
    const slideToggle = (el, show) => {
        if (show) {
            el.style.display = 'block';
            el.style.height = '0px';
            let fullHeight = el.scrollHeight + 'px';
            requestAnimationFrame(() => {
                el.style.transition = 'height 0.3s ease';
                el.style.height = fullHeight;
            });
            el.addEventListener('transitionend', () => {
                el.style.height = 'auto';
                el.style.transition = '';
            }, {once:true});
        } else {
            el.style.height = el.scrollHeight + 'px';
            requestAnimationFrame(() => {
                el.style.transition = 'height 0.3s ease';
                el.style.height = '0px';
            });
            el.addEventListener('transitionend', () => {
                el.style.display = 'none';
                el.style.transition = '';
            }, {once:true});
        }
    };

    const formBankBaru = document.getElementById('formBankBaru');
    const chkGunakanBankBaru = document.getElementById('chkGunakanBankBaru');
    const infoRekeningBaru = document.getElementById('infoRekeningBaru');

    function toggleFormBankBaru() {
        slideToggle(formBankBaru, chkGunakanBankBaru.checked);
        infoRekeningBaru.classList.toggle('d-none', !chkGunakanBankBaru.checked);
        if (!chkGunakanBankBaru.checked) {
            formBankBaru.querySelectorAll('input, select').forEach(el => el.value = '');
        }
    }

    if (!isFirstPayment && chkGunakanBankBaru) {
        chkGunakanBankBaru.addEventListener('change', toggleFormBankBaru);
    }

    // ==== REDIRECT BANK TUJUAN ====
    const bankSekolahSelect = document.getElementById('bank_sekolah_id');
    bankSekolahSelect?.addEventListener('change', function () {
        const bankId = this.value;
        if (bankId) {
            const url = new URL(window.location.href);
            url.searchParams.set('bank_sekolah_id', bankId);
            window.location.href = url.toString();
        }
    });

    // ==== FILE PREVIEW ====
    const buktiBayarEl = document.getElementById('bukti_bayar');
    const previewContainer = document.getElementById('previewContainer');

    function createCloseButton() {
        const btn = document.createElement('button');
        btn.className = 'btn-close';
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

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '120px';
                img.style.height = '120px';
                img.style.objectFit = 'cover';
                previewContainer.appendChild(img);
                previewContainer.appendChild(createCloseButton());
                previewContainer.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        } else if (file.type === 'application/pdf') {
            const div = document.createElement('div');
            div.innerHTML = `<i class="bi bi-file-pdf me-1"></i> ${file.name}`;
            previewContainer.appendChild(div);
            previewContainer.appendChild(createCloseButton());
            previewContainer.classList.remove('d-none');
        }
    });

    // ==== FORMAT RUPIAH ====
    document.querySelectorAll('.rupiah').forEach(input => {
        input.addEventListener('keyup', () => {
            let value = input.value.replace(/\D/g, '');
            if (value) input.value = new Intl.NumberFormat('id-ID').format(value);
        });
        input.form?.addEventListener('submit', () => {
            input.value = input.value.replace(/\D/g, '');
        });
    });

    // ==== LOADING STATE ====
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
