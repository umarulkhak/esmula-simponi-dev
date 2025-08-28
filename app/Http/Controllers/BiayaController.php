<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBiayaRequest;
use App\Http\Requests\UpdateBiayaRequest;
use App\Models\Biaya as Model;
use Illuminate\Http\Request;

/**
 * Controller untuk manajemen data Biaya.
 *
 * Mengatur CRUD (Create, Read, Update, Delete) data Biaya.
 *
 * @author  Umar Ulkhak
 * @date    27 Agustus 2025
 */
class BiayaController extends Controller
{
    /**
     * Path view blade yang digunakan.
     */
    private string $viewPath = 'operator.';

    /**
     * Prefix route yang digunakan.
     */
    private string $routePrefix = 'biaya';

    /**
     * Nama file view untuk index, form, dan show.
     */
    private string $viewIndex = 'biaya_index';
    private string $viewForm  = 'biaya_form';
    private string $viewShow  = 'biaya_show';

    /**
     * Menampilkan daftar Biaya.
     * Mendukung pencarian dengan parameter `q`.
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
        Model::create($request->validated());

        flash('Data berhasil disimpan')->success();

        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Menampilkan form edit Biaya.
     */
    public function edit(Model $biaya)
    {
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
    public function update(UpdateBiayaRequest $request, Model $biaya)
    {
        $biaya->update($request->validated());

        flash('Data berhasil diperbarui')->success();

        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Menghapus data Biaya.
     */
    public function destroy(Model $biaya)
    {
        $biaya->delete();

        flash('Data berhasil dihapus')->success();

        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Menampilkan detail Biaya.
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
