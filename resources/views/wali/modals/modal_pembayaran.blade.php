<div class="modal fade" id="pembayaranModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 border-0 shadow">
            <div class="modal-header bg-light border-bottom-0 pb-3">
                <h5 class="modal-title fw-bold">Konfirmasi Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                {!! Form::model($model, [
                    'route' => $route,
                    'method' => $method,
                    'files' => true,
                    'id' => 'formPembayaran'
                ]) !!}
                <input type="hidden" name="tagihan_id" id="modal_tagihan_id">
                <input type="hidden" name="bank_id" id="modal_bank_id">

                <div class="mb-3">
                    <label class="form-label fw-medium">Bank Tujuan</label>
                    <div class="bg-light p-2 rounded border" id="bankInfo">
                        <small class="text-muted">Akan otomatis terisi saat modal dibuka</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="tanggal_bayar" class="form-label fw-medium">Tanggal Bayar</label>
                    {!! Form::date('tanggal_bayar', $model->tanggal_bayar ?? date('Y-m-d'), [
                        'class' => 'form-control rounded-2',
                        'required'
                    ]) !!}
                    @error('tanggal_bayar')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="jumlah_dibayar" class="form-label fw-medium">Jumlah Dibayar</label>
                    {!! Form::text('jumlah_dibayar', null, [
                        'class' => 'form-control rounded-2 rupiah',
                        'placeholder' => 'Contoh: 500000',
                        'required'
                    ]) !!}
                    @error('jumlah_dibayar')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="bukti_bayar" class="form-label fw-medium">Upload Bukti (JPG/PNG/PDF)</label>
                    {!! Form::file('bukti_bayar', [
                        'class' => 'form-control',
                        'accept' => 'image/*,.pdf',
                        'required'
                    ]) !!}
                    @error('bukti_bayar')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                    <div class="form-text text-muted small">Maks. 2MB</div>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-outline-secondary px-4 rounded-2" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="submit" class="btn btn-dark px-4 rounded-2">
                    Simpan
                </button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('pembayaranModal');
        const bankInfo = document.getElementById('bankInfo');

        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const tagihanId = button.getAttribute('data-tagihan-id');
            const bankId = button.getAttribute('data-bank-id');

            // Set hidden inputs
            document.getElementById('modal_tagihan_id').value = tagihanId;
            document.getElementById('modal_bank_id').value = bankId;

            // Ambil info bank dari card (asumsi: nama bank & nomor ada di elemen sebelumnya)
            const bankNameElem = button.closest('.bg-white').querySelectorAll('p.mb-0.fs-6')[0];
            const bankAccountElem = button.closest('.bg-white').querySelectorAll('p.mb-0.fs-6')[1];

            if (bankNameElem && bankAccountElem) {
                bankInfo.innerHTML = `
                    <div class="fw-medium">${bankNameElem.textContent}</div>
                    <div class="small">${bankAccountElem.textContent}</div>
                `;
            } else {
                bankInfo.innerHTML = '<small class="text-muted">Informasi bank tidak tersedia</small>';
            }
        });

        // Format Rupiah (opsional, jika ingin otomatis format)
        document.querySelectorAll('.rupiah').forEach(el => {
            el.addEventListener('keyup', function(e) {
                let value = this.value.replace(/\D/g, '');
                if (value) {
                    this.value = new Intl.NumberFormat('id-ID').format(value);
                }
            });
        });
    });
</script>
@endpush
