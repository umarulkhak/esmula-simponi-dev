<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBiayaRequest;
use App\Http\Requests\UpdateBiayaRequest;
use App\Models\Biaya as Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller untuk manajemen data Biaya.
 *
 * Mengatur operasi CRUD (Create, Read, Update, Delete) data biaya.
 * Termasuk proteksi penghapusan jika biaya sedang digunakan di tagihan.
 *
 * @author  Umar Ulkhak
 */
class BiayaController extends Controller
{
    private string $viewPath = 'operator.';
    private string $routePrefix = 'biaya';
    private string $viewIndex = 'biaya_index';
    private string $viewForm = 'biaya_form';
    private string $viewShow = 'biaya_show';

    public function index(Request $request)
    {
        $query = Model::with('user');

        if ($request->filled('q')) {
            $query = $query->search($request->q);
        }

        $models = $query->latest()->paginate(50);

        return view($this->viewPath . $this->viewIndex, [
            'models'      => $models,
            'routePrefix' => $this->routePrefix,
            'title'       => 'Data Biaya',
        ]);
    }

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

    public function store(StoreBiayaRequest $request)
    {
        $validated = $request->validated();
        $biaya = Model::create($validated);

        flash("✅ Biaya '{$biaya->nama}' berhasil disimpan")->success();
        return redirect()->route($this->routePrefix . '.index');
    }

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

    public function update(UpdateBiayaRequest $request, Model $biaya)
    {
        $validated = $request->validated();
        $namaLama = $biaya->nama;
        $biaya->update($validated);

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
     * Hapus satu biaya — dengan proteksi jika dipakai di tagihan.
     */
    public function destroy(Model $biaya)
    {
        if ($biaya->tagihans()->exists()) {
            flash("❌ Tidak bisa menghapus biaya '{$biaya->nama}' karena sedang digunakan di tagihan.")->error();
            return back();
        }

        $namaBiaya = $biaya->nama;
        $biaya->delete();

        flash("🗑️ Biaya '{$namaBiaya}' berhasil dihapus")->success();
        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Hapus massal: terpilih atau semua — dengan proteksi integritas data.
     */
    public function massDestroy(Request $request)
    {
        $request->validate([
            'ids'         => 'nullable|array',
            'ids.*'       => 'exists:biayas,id',
            'delete_all'  => 'nullable|boolean',
        ]);

        if ($request->has('delete_all') && $request->delete_all == '1') {
            // Ambil semua nama biaya sebelum hapus
            $deletedNames = Model::pluck('nama')->toArray();

            Model::truncate();
            flash("🗑️ Semua (" . count($deletedNames) . ") biaya berhasil dihapus: " . implode(', ', $deletedNames))->success();
        } elseif ($request->filled('ids')) {
            $ids = $request->ids;
            $ids = array_map('intval', array_filter($ids, 'is_numeric'));
            if (empty($ids)) {
                flash("⚠️ Tidak ada ID valid yang dipilih.")->warning();
                return back();
            }

            // Ambil nama biaya yang akan dihapus
            $deletedNames = Model::whereIn('id', $ids)->pluck('nama')->toArray();

            // Hapus data
            Model::whereIn('id', $ids)->delete();

            // Tampilkan nama biaya yang dihapus
            flash("🗑️ " . count($deletedNames) . " biaya berhasil dihapus: " . implode(', ', $deletedNames))->success();
        } else {
            flash("⚠️ Tidak ada data yang dipilih untuk dihapus.")->warning();
        }

        return redirect()->route($this->routePrefix . '.index');
    }

    public function show(Model $biaya)
    {
        return view($this->viewPath . $this->viewShow, [
            'model'       => $biaya,
            'title'       => 'Detail Biaya',
            'routePrefix' => $this->routePrefix,
        ]);
    }
}
