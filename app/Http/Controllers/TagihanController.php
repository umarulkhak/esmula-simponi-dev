<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagihanRequest;
use App\Http\Requests\UpdateTagihanRequest;
use App\Models\Siswa;
use App\Models\Tagihan;
use Illuminate\Http\Request;

/**
 * Controller untuk manajemen data Tagihan.
 *
 * Mengatur CRUD (Create, Read, Update, Delete) data Tagihan.
 *
 * @author  Umar Ulkhak
 * @date    2 Sepetember 2025
 */
class TagihanController extends Controller
{
    /**
     * Path view blade yang digunakan.
     */
    private string $viewPath = 'operator.';

    /**
     * Prefix route yang digunakan.
     */
    private string $routePrefix = 'tagihan';

    /**
     * Nama file view untuk index, form, dan show.
     */
    private string $viewIndex = 'tagihan_index';
    private string $viewForm  = 'tagihan_form';
    private string $viewShow  = 'tagihan_show';

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $models = $request->filled('q')
            ? Tagihan::with(['user', 'siswa'])->search($request->q)->paginate(50)
            : Tagihan::with(['user', 'siswa'])->latest()->paginate(50);

        return view($this->viewPath . $this->viewIndex, [
            'models'      => $models,
            'routePrefix' => $this->routePrefix,
            'title'       => 'Data Tagihan',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $siswa = Siswa::all();

        return view($this->viewPath . $this->viewForm, [
            'model'    => new Tagihan(),
            'method'   => 'POST',
            'route'    => $this->routePrefix . '.store',
            'button'   => 'SIMPAN',
            'title'    => 'Form Data Tagihan',
            'angkatan' => $siswa->pluck('angkatan', 'angkatan'),
            'kelas'    => $siswa->pluck('kelas', 'kelas'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTagihanRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Tagihan $tagihan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tagihan $tagihan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTagihanRequest $request, Tagihan $tagihan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tagihan $tagihan)
    {
        //
    }
}
