<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBankSekolahRequest;
use App\Http\Requests\UpdateBankSekolahRequest;
use App\Models\BankSekolah as Model;
use Illuminate\Http\Request;

/**
 * Controller untuk manajemen data Bank Sekolah.
 *
 * Mengatur operasi CRUD (Create, Read, Update, Delete) data rekening bank sekolah.
 * Menggunakan implicit model binding untuk validasi & keamanan otomatis.
 * Semua flash message menyertakan konteks (nama bank) untuk UX lebih baik.
 *
 * @author  Umar Ulkhak
 * @date    27 Agustus 2025
 * @updated 5 April 2025 — Tambah dokumentasi & UX improvement
 */
class BankSekolahController extends Controller
{
    /**
     * Prefix path untuk view operator.
     */
    private string $viewPath = 'operator.';

    /**
     * Prefix route untuk bank sekolah (tanpa group name prefix).
     * Contoh: 'bank-sekolah.index', 'bank-sekolah.create', dll.
     */
    private string $routePrefix = 'banksekolah';

    /**
     * Nama view untuk halaman index.
     */
    private string $viewIndex = 'banksekolah_index';

    /**
     * Nama view untuk form tambah/edit.
     */
    private string $viewForm = 'banksekolah_form';

    /**
     * Nama view untuk halaman detail.
     */
    private string $viewShow = 'banksekolah_show';

    /**
     * Menampilkan daftar bank sekolah dengan fitur pencarian.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Model::query();

        if ($request->filled('q')) {
            $query->where('nama_rekening', 'like', '%' . $request->q . '%');
        }

        $models = $query->latest()->paginate(50);

        return view($this->viewPath . $this->viewIndex, [
            'models'      => $models,
            'routePrefix' => $this->routePrefix,
            'title'       => 'Data Rekening Sekolah',
        ]);
    }

    /**
     * Menampilkan form tambah data bank sekolah.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view($this->viewPath . $this->viewForm, [
            'model'    => new Model(),
            'method'   => 'POST',
            'route'    => $this->routePrefix . '.store',
            'button'   => 'SIMPAN',
            'title'    => 'Form Tambah Rekening Sekolah',
            'listbank' => \App\Models\Bank::pluck('nama_bank', 'id'),
        ]);
    }

    /**
     * Menyimpan data bank sekolah baru ke database.
     *
     * @param  \App\Http\Requests\StoreBankSekolahRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreBankSekolahRequest $request)
    {
        $requestData = $request->validated();

        // Ambil data bank yang dipilih
        $bank = \App\Models\Bank::find($requestData['bank_id']);

        // Hapus bank_id dari requestData agar tidak disimpan ke tabel bank_sekolahs
        unset($requestData['bank_id']);

        // Isi field tambahan dari relasi bank
        $requestData['kode']       = $bank->sandi_bank;
        $requestData['nama_bank']  = $bank->nama_bank;

        // Simpan data ke tabel bank_sekolahs
        Model::create($requestData);

        flash('Data berhasil disimpan');

        return back();
    }

    /**
     * Menampilkan form edit data bank sekolah.
     *
     * @param  \App\Models\BankSekolah  $bankSekolah
     * @return \Illuminate\View\View
     */
    public function edit(Model $bankSekolah)
    {
        return view($this->viewPath . $this->viewForm, [
            'model'  => $bankSekolah,
            'method' => 'PUT',
            'route'  => [$this->routePrefix . '.update', $bankSekolah->id],
            'button' => 'UPDATE',
            'title'  => 'Form Edit Data Rekening Sekolah',
        ]);
    }

    /**
     * Memperbarui data bank sekolah di database.
     *
     * @param  \App\Http\Requests\UpdateBankSekolahRequest  $request
     * @param  \App\Models\BankSekolah  $bankSekolah
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateBankSekolahRequest $request, Model $bankSekolah)
    {
        $validated = $request->validated();

        // Simpan nama lama untuk flash message
        $namaLama = $bankSekolah->nama_bank;

        // Update data bank
        $bankSekolah->update($validated);

        // Flash message sukses dengan detail perubahan
        $pesan = "✅ Bank berhasil diperbarui";
        if ($namaLama !== $validated['nama_bank']) {
            $pesan .= " ({$namaLama} → {$validated['nama_bank']})";
        } else {
            $pesan .= ": {$validated['nama_bank']}";
        }

        flash($pesan)->success();

        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Menghapus data bank sekolah dari database.
     *
     * @param  \App\Models\BankSekolah  $bankSekolah
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Model $bankSekolah)
    {
        // Simpan nama bank untuk flash message
        $namaBank = $bankSekolah->nama_bank;

        // Hapus data bank
        $bankSekolah->delete();

        // Flash message sukses dengan nama bank
        flash("🗑️ Bank '{$namaBank}' berhasil dihapus")->success();

        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Menampilkan detail data bank sekolah.
     *
     * @param  \App\Models\BankSekolah  $bankSekolah
     * @return \Illuminate\View\View
     */
    public function show(Model $bankSekolah)
    {
        return view($this->viewPath . $this->viewShow, [
            'model'       => $bankSekolah,
            'title'       => 'Detail Bank Sekolah',
            'routePrefix' => $this->routePrefix,
        ]);
    }
}
