<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBiayaRequest;
use App\Http\Requests\UpdateBiayaRequest;
use App\Models\Biaya as Model;
use Illuminate\Http\Request;

/**
 * Controller untuk manajemen data Biaya.
 *
 * Mengatur operasi CRUD (Create, Read, Update, Delete) data biaya.
 * Menggunakan implicit model binding untuk validasi & keamanan otomatis.
 * Semua flash message menyertakan konteks (nama biaya) untuk UX lebih baik.
 *
 * @author  Umar Ulkhak
 * @date    27 Agustus 2025
 * @updated 5 April 2025 — Tambah dokumentasi & UX improvement
 */
class BiayaController extends Controller
{
    /**
     * Prefix path untuk view operator.
     */
    private string $viewPath = 'operator.';

    /**
     * Prefix route untuk biaya (tanpa group name prefix).
     * Contoh: 'biaya.index', 'biaya.create', dll.
     */
    private string $routePrefix = 'biaya';

    /**
     * Nama view untuk halaman index.
     */
    private string $viewIndex = 'biaya_index';

    /**
     * Nama view untuk form tambah/edit.
     */
    private string $viewForm = 'biaya_form';

    /**
     * Nama view untuk halaman detail.
     */
    private string $viewShow = 'biaya_show';

    /**
     * Menampilkan daftar biaya dengan fitur pencarian.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Bangun query dasar dengan relasi user
        $query = Model::with('user');

        // Jika ada pencarian, gunakan searchable trait
        if ($request->filled('q')) {
            $query = $query->search($request->q);
        }

        // Ambil data dengan pagination
        $models = $query->latest()->paginate(50);

        return view($this->viewPath . $this->viewIndex, [
            'models'      => $models,
            'routePrefix' => $this->routePrefix,
            'title'       => 'Data Biaya',
        ]);
    }

    /**
     * Menampilkan form tambah data biaya.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view($this->viewPath . $this->viewForm, [
            'model'  => new Model(),
            'method' => 'POST',
            'route'  => $this->routePrefix . '.store',
            'button' => 'SIMPAN',
            'title'  => 'Form Tambah Data Biaya',
        ]);
    }

    /**
     * Menyimpan data biaya baru ke database.
     *
     * @param  \App\Http\Requests\StoreBiayaRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreBiayaRequest $request)
    {
        $validated = $request->validated();

        // Simpan ke database
        $biaya = Model::create($validated);

        // Flash message sukses dengan nama biaya
        flash("✅ Biaya '{$biaya->nama}' berhasil disimpan")->success();

        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Menampilkan form edit data biaya.
     *
     * @param  \App\Models\Biaya  $biaya
     * @return \Illuminate\View\View
     */
    public function edit(Model $biaya)
    {
        return view($this->viewPath . $this->viewForm, [
            'model'  => $biaya,
            'method' => 'PUT',
            'route'  => [$this->routePrefix . '.update', $biaya->id],
            'button' => 'UPDATE',
            'title'  => 'Form Edit Data Biaya',
        ]);
    }

    /**
     * Memperbarui data biaya di database.
     *
     * @param  \App\Http\Requests\UpdateBiayaRequest  $request
     * @param  \App\Models\Biaya  $biaya
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateBiayaRequest $request, Model $biaya)
    {
        $validated = $request->validated();

        // Simpan nama lama untuk flash message
        $namaLama = $biaya->nama;

        // Update data biaya
        $biaya->update($validated);

        // Flash message sukses dengan detail perubahan
        $pesan = "✅ Biaya berhasil diperbarui";
        if ($namaLama !== $validated['nama']) {
            $pesan .= " ({$namaLama} → {$validated['nama']})";
        } else {
            $pesan .= ": {$validated['nama']}";
        }

        flash($pesan)->success();

        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Menghapus data biaya dari database.
     *
     * @param  \App\Models\Biaya  $biaya
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Model $biaya)
    {
        // Simpan nama biaya untuk flash message
        $namaBiaya = $biaya->nama;

        // Hapus data biaya
        $biaya->delete();

        // Flash message sukses dengan nama biaya
        flash("🗑️ Biaya '{$namaBiaya}' berhasil dihapus")->success();

        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Menampilkan detail data biaya.
     *
     * @param  \App\Models\Biaya  $biaya
     * @return \Illuminate\View\View
     */
    public function show(Model $biaya)
    {
        return view($this->viewPath . $this->viewShow, [
            'model'       => $biaya,
            'title'       => 'Detail Biaya',
            'routePrefix' => $this->routePrefix,
        ]);
    }
}
