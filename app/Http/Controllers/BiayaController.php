<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBiayaRequest;
use App\Http\Requests\UpdateBiayaRequest;
use App\Models\Biaya as Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Controller untuk manajemen data Biaya.
 *
 * @author  Umar Ulkhak
 * @date    27 Agustus 2025
 */
class BiayaController extends Controller
{
    private string $viewPath    = 'operator.';
    private string $routePrefix = 'biaya';
    private string $viewIndex   = 'biaya_index';
    private string $viewForm    = 'biaya_form';
    private string $viewShow    = 'biaya_show';

    /**
     * Menampilkan daftar Biaya.
     */
    public function index(Request $request)
    {
        $models = $request->filled('q')
            ? Model::search($request->q)->paginate(50)
            : Model::with('user')->latest()->paginate(50);

        return view($this->viewPath . $this->viewIndex, [
            'models'      => $models,
            'routePrefix' => $this->routePrefix,
            'title'       => 'Data Biaya',
        ]);
    }

    /**
     * Menampilkan form tambah Biaya.
     */
    public function create()
    {
        return view($this->viewPath . $this->viewForm, [
            'model'  => new Model(),
            'method' => 'POST',
            'route'  => $this->routePrefix . '.store',
            'button' => 'SIMPAN',
            'title'  => 'Form Data Biaya',
        ]);
    }

    /**
     * Menyimpan data Biaya baru.
     */
    public function store(StoreBiayaRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

        Model::create($validated);

        flash('Data berhasil disimpan')->success();

        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Menampilkan form edit Biaya.
     */
    public function edit(int $id)
    {
        $biaya = Model::findOrFail($id);

        return view($this->viewPath . $this->viewForm, [
            'model'  => $biaya,
            'method' => 'PUT',
            'route'  => [$this->routePrefix . '.update', $biaya->id],
            'button' => 'UPDATE',
            'title'  => 'Form Data Biaya',
        ]);
    }

    /**
     * Memperbarui data Biaya.
     */
    public function update(UpdateBiayaRequest $request, int $id)
    {
        $biaya = Model::findOrFail($id);

        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

        $biaya->update($validated);

        flash('Data berhasil diperbarui')->success();

        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Menghapus data Biaya.
     */
    public function destroy(int $id)
    {
        $biaya = Model::findOrFail($id);

        $this->deleteFotoIfExists($biaya->foto);
        $biaya->delete();

        flash('Data berhasil dihapus')->success();

        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Menampilkan detail Biaya.
     */
    public function show(int $id)
    {
        $biaya = Model::findOrFail($id);

        return view($this->viewPath . $this->viewShow, [
            'model'       => $biaya,
            'title'       => 'Detail Biaya',
            'routePrefix' => $this->routePrefix,
        ]);
    }

    /**
     * Hapus foto Biaya jika ada.
     */
    private function deleteFotoIfExists(?string $path): void
    {
        if ($path && Storage::exists($path)) {
            Storage::delete($path);
        }
    }
}
