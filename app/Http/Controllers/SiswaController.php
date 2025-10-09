<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Siswa as Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreSiswaRequest;
use App\Http\Requests\UpdateSiswaRequest;

/**
 * Controller untuk manajemen data Siswa.
 *
 * Mengelola operasi CRUD (Create, Read, Update, Delete) data siswa,
 * termasuk upload foto, relasi wali murid, dan pencarian.
 * Semua operasi dilindungi oleh FormRequest untuk validasi otomatis.
 *
 * @author  Umar Ulkhak
 * @date    3 Agustus 2025
 * @updated 5 April 2025 — Tambah dokumentasi & clean code
 */
class SiswaController extends Controller
{
    /**
     * Prefix path untuk view operator.
     */
    private string $viewPath = 'operator.';

    /**
     * Prefix route untuk siswa (tanpa group name prefix).
     * Contoh: 'siswa.index', 'siswa.create', dll.
     */
    private string $routePrefix = 'siswa';

    /**
     * Nama view untuk halaman index.
     */
    private string $viewIndex = 'siswa_index';

    /**
     * Nama view untuk form tambah/edit.
     */
    private string $viewForm = 'siswa_form';

    /**
     * Nama view untuk halaman detail.
     */
    private string $viewShow = 'siswa_show';

    /**
     * Menampilkan daftar siswa dengan fitur pencarian.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Bangun query dasar dengan relasi
        $query = Model::with('wali', 'user');

        // Jika ada pencarian, gunakan searchable trait
        if ($request->filled('q')) {
            $query = $query->search($request->q);
        }

        // Ambil data dengan pagination
        $models = $query->latest()->paginate(20);

        return view($this->viewPath . $this->viewIndex, [
            'models'      => $models,
            'routePrefix' => $this->routePrefix,
            'title'       => 'Data Siswa',
        ]);
    }

    /**
     * Menampilkan form tambah data siswa.
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
            'title'  => 'Form Tambah Data Siswa',
            'wali'   => $this->getWaliOptions(),
        ]);
    }

    /**
     * Menyimpan data siswa baru ke database.
     *
     * @param  \App\Http\Requests\StoreSiswaRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreSiswaRequest $request)
    {
        $validated = $request->validated();

        // Handle upload foto jika ada
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('public/foto_siswa');
        }

        // Set status wali jika wali_id diisi
        if ($request->filled('wali_id')) {
            $validated['wali_status'] = 'ok';
        }

        // Set user yang membuat data
        $validated['user_id'] = Auth::id();

        // Simpan ke database
        $siswa = Model::create($validated);

        // Flash message sukses
        flash("✅ Data siswa '{$siswa->nama}' berhasil disimpan")->success();

        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Menampilkan form edit data siswa.
     *
     * @param  int  $id  ID siswa
     * @return \Illuminate\View\View
     */
    public function edit(int $id)
    {
        $siswa = Model::findOrFail($id);

        return view($this->viewPath . $this->viewForm, [
            'model'  => $siswa,
            'method' => 'PUT',
            'route'  => [$this->routePrefix . '.update', $siswa->id],
            'button' => 'UPDATE',
            'title'  => 'Form Edit Data Siswa',
            'wali'   => $this->getWaliOptions(),
        ]);
    }

    /**
     * Memperbarui data siswa di database.
     *
     * @param  \App\Http\Requests\UpdateSiswaRequest  $request
     * @param  int  $id  ID siswa
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateSiswaRequest $request, int $id)
    {
        $siswa = Model::findOrFail($id);
        $validated = $request->validated();

        // Simpan nilai lama untuk flash message
        $kelasLama = $siswa->kelas;
        $namaSiswa = $siswa->nama;

        // Handle upload foto jika ada (hapus foto lama)
        if ($request->hasFile('foto')) {
            $this->deleteFotoIfExists($siswa->foto);
            $validated['foto'] = $request->file('foto')->store('public/foto_siswa');
        }

        // Update status wali
        $validated['wali_status'] = $request->filled('wali_id') ? 'ok' : null;

        // Set/update user_id
        $validated['user_id'] = Auth::id();

        // Update data siswa
        $siswa->update($validated);

        // Flash message sukses dengan detail perubahan
        $pesan = "✅ Data siswa '{$namaSiswa}' berhasil diperbarui";
        if ($kelasLama !== $validated['kelas']) {
            $pesan .= " (Kelas: {$kelasLama} → {$validated['kelas']})";
        }

        flash($pesan)->success();

        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Menghapus data siswa dari database.
     *
     * @param  int  $id  ID siswa
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        $siswa = Model::findOrFail($id);

        // Hapus foto jika ada
        $this->deleteFotoIfExists($siswa->foto);

        // Hapus data siswa
        $namaSiswa = $siswa->nama;
        $siswa->delete();

        // Flash message sukses
        flash("🗑️ Data siswa '{$namaSiswa}' berhasil dihapus")->success();

        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Menampilkan detail data siswa.
     *
     * @param  int  $id  ID siswa
     * @return \Illuminate\View\View
     */
    public function show(int $id)
    {
        $siswa = Model::findOrFail($id);

        return view($this->viewPath . $this->viewShow, [
            'model'       => $siswa,
            'title'       => 'Detail Siswa',
            'routePrefix' => $this->routePrefix,
        ]);
    }

    /**
     * Ambil daftar wali murid untuk dropdown select.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getWaliOptions()
    {
        return User::where('akses', 'wali')
                   ->orderBy('name')
                   ->pluck('name', 'id');
    }

    /**
     * Hapus file foto dari storage jika path-nya valid dan ada.
     *
     * @param  string|null  $path
     * @return void
     */
    private function deleteFotoIfExists(?string $path): void
    {
        if (!empty($path) && Storage::exists($path)) {
            Storage::delete($path);
        }
    }
}
