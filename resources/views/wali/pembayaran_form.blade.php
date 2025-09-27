@extends('layouts.app_sneat_wali')

{{--
Form Konfirmasi Pembayaran SPP oleh Wali Murid (Halaman Penuh)

Fitur Utama:
- Preview file (gambar/PDF) sebelum upload
- Pilihan antara data bank tersimpan atau input manual
- Format otomatis untuk jumlah pembayaran (Rupiah)
- Validasi Laravel + UX loading state saat submit
- Responsif & kompatibel Bootstrap 5

Dependencies:
- Bootstrap 5 + Bootstrap Icons
- Select2 (untuk dropdown)
- jQuery
- Laravel Collective Form

@author Umar Ulkhak
@version 2.1 — Full Page (Non-Modal)
@updated {{ now()->format('d F Y') }}
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
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {!! Form::model($model, [
                    'route' => $route,
                    'method' => $method,
                    'files' => true,
                    'id' => 'formPembayaran'
                ]) !!}

                <!-- Rekening Tujuan -->
                <section class="mb-4">
                    <div class="divider divider-dark">
                        <div class="divider-text">
                            <i class="fa-solid fa-circle-info me-1"></i> Rekening Tujuan
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="bank_id">Bank Tujuan Pembayaran</label>
                        {!! Form::select('bank_sekolah_id', $listBank, request('bank_sekolah_id'), [
                            'class' => 'form-control',
                            'placeholder' => 'Pilih Bank Tujuan Transfer',
                        ]) !!}
                        <span class="text-danger">{{ $errors->first('bank_sekolah_id') }}</span>
                    </div>

                    @if (request('bank_sekolah_id') != "")
                        <div class="alert alert-primary mt-2" role="alert">
                            <table width="100%">
                                <tbody>
                                    <tr>
                                        <td width="10%">Bank Tujuan</td>
                                        <td>: {{ $bankYangDipilih->nama_bank }}</td>
                                    </tr>
                                    <tr>
                                        <td>Nomor Rekening</td>
                                        <td>: {{ $bankYangDipilih->nomor_rekening }}</td>
                                    </tr>
                                    <tr>
                                        <td>Atas Nama</td>
                                        <td>: {{ $bankYangDipilih->nama_rekening }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>

                <!-- Hidden Inputs -->
                <input type="hidden" name="tagihan_id" value="{{ $tagihan->id }}">
                <input type="hidden" name="bank_id" value="{{ $bankSekolah->id }}">

                {{-- <!-- Rekening Pengirim -->
                <section class="mb-4">
                    <div class="divider divider-dark">
                        <div class="divider-text">
                            <i class="fa-solid fa-circle-info me-1"></i> Rekening Pengirim
                        </div>
                    </div>

                    @if(isset($waliBanks) && $waliBanks->isNotEmpty())
                        <div class="form-group mb-3">
                            <label for="bank_tersimpan_id" class="form-label fw-medium">
                                <i class="bi bi-collection me-1"></i> Gunakan Data Bank Tersimpan
                            </label>
                            <select name="bank_tersimpan_id" id="bank_tersimpan_id" class="form-control select2" data-placeholder="Pilih data bank tersimpan...">
                                <option value=""></option>
                                @foreach($waliBanks as $bank)
                                    <option value="{{ $bank->id }}"
                                        data-nama-bank="{{ $bank->nama_bank_full }}"
                                        data-nama-pemilik="{{ $bank->nama_pemilik }}"
                                        data-no-rekening="{{ $bank->no_rekening }}">
                                        {{ $bank->nama_bank_full }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted small">
                                <i class="bi bi-info-circle me-1"></i>
                                Pilih untuk mengisi otomatis form di bawah.
                            </div>
                        </div>

                        <div class="form-check mb-3" id="chkGunakanBankBaru" style="display: none;">
                            <input class="form-check-input" type="checkbox" id="chkBaru" name="gunakan_bank_baru">
                            <label class="form-check-label" for="chkBaru">
                                Gunakan bank baru? (abaikan data tersimpan)
                            </label>
                        </div>
                    @endif --}}

                    <div id="formBankBaru">
                        @include('wali.partials.form_field', [
                            'label' => 'Nama Bank Pengirim',
                            'name' => 'bank_pengirim_id',
                            'type' => 'select',
                            'options' => $listbank ?? [],
                            'icon' => 'bi-bank2',
                            'placeholder' => 'Pilih bank pengirim...',
                            'required' => true,
                            'class' => 'select2 col-md-12'
                        ])

                        @include('wali.partials.form_field', [
                            'label' => 'Nama Pemilik Rekening',
                            'name' => 'nama_pengirim',
                            'type' => 'text',
                            'icon' => 'bi-person',
                            'placeholder' => 'Nama sesuai buku tabungan',
                            'required' => true,
                            'class' => 'col-md-12'
                        ])

                        @include('wali.partials.form_field', [
                            'label' => 'Nomor Rekening',
                            'name' => 'no_rekening_pengirim',
                            'type' => 'text',
                            'icon' => 'bi-credit-card',
                            'placeholder' => 'Contoh: 1234567890',
                            'required' => true,
                            'class' => 'col-md-12'
                        ])
                    </div>

                    <div class="form-check mt-3">
                        {!! Form::checkbox('simpan_data_rekening', 1, true, ['class' => 'form-check-input', 'id' => 'defaultCheck3']) !!}
                        <label class="form-check-label" for="defaultCheck3">
                            <small>Simpan data ini untuk pembayaran selanjutnya.</small>
                        </label>
                    </div>
                </section>

                <!-- Info Pembayaran -->
                <section class="mb-4">
                    <div class="divider divider-dark">
                        <div class="divider-text">
                            <i class="fa-solid fa-circle-info me-1"></i> Info Pembayaran
                        </div>
                    </div>

                    @include('wali.partials.form_field', [
                        'label' => 'Tanggal Bayar',
                        'name' => 'tanggal_bayar',
                        'type' => 'date',
                        'icon' => 'bi-calendar',
                        'value' => $model->tanggal_bayar ?? date('Y-m-d'),
                        'required' => true
                    ])

                    @include('wali.partials.form_field', [
                        'label' => 'Jumlah Dibayar',
                        'name' => 'jumlah_dibayar',
                        'type' => 'text',
                        'icon' => 'bi-cash',
                        'placeholder' => 'Contoh: 500.000',
                        'required' => true,
                        'class' => 'rupiah'
                    ])

                    <div class="mb-3">
                        <label for="bukti_bayar" class="form-label fw-medium">
                            <i class="bi bi-upload me-1"></i> Upload Bukti (JPG/PNG/PDF) <span class="text-danger">*</span>
                        </label>
                        {!! Form::file('bukti_bayar', [
                            'class' => 'form-control' . ($errors->has('bukti_bayar') ? ' is-invalid' : ''),
                            'accept' => 'image/*,.pdf',
                            'required',
                            'id' => 'bukti_bayar'
                        ]) !!}
                        @error('bukti_bayar')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted small">Maks. 2MB</div>
                        <div id="previewContainer" class="mt-2 d-none"></div>
                    </div>
                </section>

                <!-- Action Buttons -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary px-4 rounded-2">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-dark px-4 rounded-2" id="btnSimpan">
                        <span class="spinner-border spinner-border-sm d-none me-2" role="status" id="loadingSpinner"></span>
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
    .divider {
        position: relative;
        height: 1px;
        background-color: #adb5bd;
        margin: 1.5rem 0;
    }
    .divider-text {
        position: absolute;
        top: -10px;
        left: 50%;
        transform: translateX(-50%);
        background: #fff;
        padding: 0 1rem;
        font-size: 0.875rem;
        color: #495057;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-radius: 0.25rem;
        z-index: 1;
        box-shadow: 0 0 0 1px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    #previewContainer {
        margin-top: 0.75rem;
        display: flex;
        justify-content: center;
        align-items: center;
        border: 1px dashed #dee2e6;
        border-radius: 0.5rem;
        background: #f8f9fa;
        padding: 0.5rem;
        display: none;
        max-width: 100%;
        overflow: hidden;
        position: relative;
    }
    #previewContainer img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        display: block;
    }
    #previewContainer .embed {
        text-align: center;
        padding: 0.75rem;
        font-size: 0.875rem;
        border: 1px dashed #adb5bd;
        border-radius: 0.375rem;
        background: #f8f9fa;
        max-width: 100%;
        word-break: break-all;
    }
    #previewContainer .btn-close {
        position: absolute;
        top: -10px;
        right: -10px;
        background: #fff;
        border: 1px solid #dee2e6;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        font-size: 0.75rem;
        cursor: pointer;
        z-index: 10;
        padding: 0;
        line-height: 1;
    }
    #formBankBaru {
        transition: all 0.3s ease;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: function () {
            return $(this).data('placeholder') || 'Pilih...';
        },
        allowClear: true
    });

    // DOM Elements
    const bankTersimpanEl = document.getElementById('bank_tersimpan_id');
    const formBankBaru = document.getElementById('formBankBaru');
    const chkGunakanBankBaru = document.getElementById('chkGunakanBankBaru');
    const chkBaru = document.getElementById('chkBaru');
    const namaPengirimEl = document.querySelector('input[name="nama_pengirim"]');
    const noRekeningEl = document.querySelector('input[name="no_rekening_pengirim"]');
    const buktiBayarEl = document.getElementById('bukti_bayar');
    const previewContainer = document.getElementById('previewContainer');
    const form = document.getElementById('formPembayaran');
    const btnSimpan = document.getElementById('btnSimpan');
    const btnText = document.getElementById('btnText');
    const loadingSpinner = document.getElementById('loadingSpinner');

    // Toggle form bank baru berdasarkan pilihan bank tersimpan
    function updateBankFormVisibility() {
        const hasSavedBank = bankTersimpanEl?.value;
        if (hasSavedBank) {
            formBankBaru.style.display = 'none';
            if (chkGunakanBankBaru) chkGunakanBankBaru.style.display = 'block';
        } else {
            formBankBaru.style.display = 'block';
            if (chkGunakanBankBaru) chkGunakanBankBaru.style.display = 'none';
        }
    }

    // Isi otomatis field bank dari data tersimpan
    bankTersimpanEl?.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        if (!opt.value) return updateBankFormVisibility();

        const namaBank = opt.getAttribute('data-nama-bank');
        const namaPemilik = opt.getAttribute('data-nama-pemilik');
        const noRekening = opt.getAttribute('data-no-rekening');

        const bankSelect = $('#bank_pengirim_id');
        let bankId = null;
        bankSelect.find('option').each(function () {
            if ($(this).text().trim() === namaBank) {
                bankId = $(this).val();
                return false;
            }
        });
        bankSelect.val(bankId || '').trigger('change');

        if (namaPengirimEl) namaPengirimEl.value = namaPemilik;
        if (noRekeningEl) noRekeningEl.value = noRekening;
        updateBankFormVisibility();
    });

    // Toggle manual input bank
    chkBaru?.addEventListener('change', function () {
        formBankBaru.style.display = this.checked ? 'block' : 'none';
        if (this.checked) {
            $('#bank_pengirim_id').val('').trigger('change');
            if (namaPengirimEl) namaPengirimEl.value = '';
            if (noRekeningEl) noRekeningEl.value = '';
        }
    });

    // Preview file upload
    buktiBayarEl.addEventListener('change', function () {
        const file = this.files[0];
        previewContainer.innerHTML = '';
        if (!file) return previewContainer.classList.add('d-none');

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '120px';
                img.style.height = '120px';
                img.style.objectFit = 'cover';
                img.style.border = '1px solid #dee2e6';
                img.style.borderRadius = '0.375rem';

                const closeBtn = createCloseButton();
                previewContainer.appendChild(img);
                previewContainer.appendChild(closeBtn);
                previewContainer.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        } else if (file.type === 'application/pdf') {
            const pdfDiv = document.createElement('div');
            pdfDiv.className = 'embed';
            pdfDiv.innerHTML = `<i class="bi bi-file-pdf me-1"></i> File PDF terpilih: ${file.name}`;
            const closeBtn = createCloseButton();
            previewContainer.appendChild(pdfDiv);
            previewContainer.appendChild(closeBtn);
            previewContainer.classList.remove('d-none');
        } else {
            previewContainer.classList.add('d-none');
        }
    });

    function createCloseButton() {
        const btn = document.createElement('button');
        btn.className = 'btn-close';
        btn.type = 'button';
        btn.setAttribute('aria-label', 'Hapus preview');
        btn.onclick = () => {
            buktiBayarEl.value = '';
            previewContainer.classList.add('d-none');
            previewContainer.innerHTML = '';
        };
        return btn;
    }

    // Submit handler (loading state)
    form.addEventListener('submit', function () {
        btnSimpan.disabled = true;
        loadingSpinner.classList.remove('d-none');
        btnText.textContent = 'Menyimpan...';
    });

    // Format Rupiah
    document.querySelectorAll('.rupiah').forEach(input => {
        const formatRupiah = () => {
            let value = input.value.replace(/\D/g, '');
            if (value) input.value = new Intl.NumberFormat('id-ID').format(value);
        };

        input.addEventListener('keyup', (e) => {
            if (['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight'].includes(e.key)) return;
            formatRupiah();
        });

        input.addEventListener('blur', formatRupiah);

        input.form.addEventListener('submit', () => {
            input.value = input.value.replace(/\D/g, '');
        });
    });

    // Initial call
    updateBankFormVisibility();
});
</script>
@endpush
