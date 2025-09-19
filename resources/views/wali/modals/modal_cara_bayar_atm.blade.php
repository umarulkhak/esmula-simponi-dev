<!-- resources/views/wali/tagihan/modal_cara_bayar_atm.blade.php -->

<div class="modal fade" id="modalCaraBayarATM" tabindex="-1" aria-labelledby="modalCaraBayarATMLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content rounded-3 shadow">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="modalCaraBayarATMLabel">Cara Pembayaran via ATM / Mobile Banking</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body fs-7">
        <h6 class="fw-bold mb-2">Pembayaran BTN Virtual Account dengan ATM BTN</h6>
        <ol>
          <li>Masukkan Kartu Anda</li>
          <li>Pilih Bahasa</li>
          <li>Masukkan PIN ATM Anda</li>
          <li>Pilih “Menu Lainnya"</li>
          <li>Pilih “Pembayaran”</li>
          <li>Pilih “Multipayment”</li>
          <li>Pilih “Virtual Account”</li>
          <li>Masukkan nomor Virtual Account Anda</li>
          <li>Tagihan akan muncul, konfirmasi jika sesuai</li>
          <li>Transaksi selesai</li>
        </ol>

        <h6 class="fw-bold mt-3 mb-2">Pembayaran BTN Virtual Account dengan Mobile Banking BTN</h6>
        <ol>
          <li>Akses BTN Mobile Banking, masukkan user ID dan password</li>
          <li>Pilih menu “Pembayaran” → “Virtual Account”</li>
          <li>Masukkan nomor Virtual Account Anda, tekan “Kirim”</li>
          <li>Konfirmasi transaksi, masukkan PIN & OTP</li>
          <li>Pembayaran selesai</li>
        </ol>

        <h6 class="fw-bold mt-3 mb-2">Pembayaran BTN Virtual Account dari Teller</h6>
        <ol>
          <li>Kunjungi kantor cabang BTN</li>
          <li>Informasikan ke teller untuk bayar “Virtual Account”</li>
          <li>Berikan nomor Virtual Account, tunggu konfirmasi</li>
          <li>Terima bukti pembayaran</li>
        </ol>

        <h6 class="fw-bold mt-3 mb-2">Pembayaran via ATM Bersama / Bank Lain</h6>
        <ol>
          <li>Pilih “Transfer ke Bank Lain”</li>
          <li>Masukkan kode bank BTN (200) + nomor Virtual Account</li>
          <li>Masukkan nominal sesuai tagihan</li>
          <li>Konfirmasi, transaksi berhasil</li>
        </ol>

        <h6 class="fw-bold mt-3 mb-2">Pembayaran dari OVO</h6>
        <ol>
          <li>Buka aplikasi OVO</li>
          <li>Pilih “Transfer” → “Rekening Bank”</li>
          <li>Pilih Bank BTN, masukkan nomor VA</li>
          <li>Masukkan nominal sesuai tagihan</li>
          <li>Konfirmasi transaksi, selesai</li>
        </ol>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
