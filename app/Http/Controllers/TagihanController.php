<?php

namespace App\Http\Controllers;

use App\Models\Biaya;
use App\Models\Siswa;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTagihanRequest;
use App\Http\Requests\UpdateTagihanRequest;

/**
 * Controller untuk manajemen data Tagihan.
 *
 * Mengatur CRUD (Create, Read, Update, Delete) data Tagihan.
 *
 * @author  Umar Ulkhak
 * @date    2 September 2025
 */
class TagihanController extends Controller
{
    /**
     * Path view blade yang digunakan.
     *
     * @var string
     */
    private string $viewPath = 'operator.';

    /**
     * Prefix route yang digunakan.
     *
     * @var string
     */
    private string $routePrefix = 'tagihan';

    /**
     * Nama file view untuk index, form, dan show.
     *
     * @var string
     */
    private string $viewIndex = 'tagihan_index';
    private string $viewForm  = 'tagihan_form';
    private string $viewShow  = 'tagihan_show';

    /**
     * Tampilkan daftar data Tagihan.
     */
    public function index(Request $request)
    {
        $query = Tagihan::with(['user', 'siswa']);

        if ($request->filled('q')) {
            $query->search($request->q);
        } else {
            $query->latest();
        }

        $models = $query->paginate(50);

        return view($this->viewPath . $this->viewIndex, [
            'models'      => $models,
            'routePrefix' => $this->routePrefix,
            'title'       => 'Data Tagihan',
        ]);
    }

    /**
     * Form tambah Tagihan.
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
            'biaya'    => Biaya::get()->pluck('nama_biaya_full', 'id'),
        ]);
    }

    /**
     * Simpan data Tagihan baru.
     */
    public function store(StoreTagihanRequest $request)
    {
        // TODO: implementasi penyimpanan
    }

    /**
     * Detail Tagihan.
     */
    public function show(Tagihan $tagihan)
    {
        // TODO: implementasi detail
    }

    /**
     * Form edit Tagihan.
     */
    public function edit(Tagihan $tagihan)
    {
        // TODO: implementasi form edit
    }

    /**
     * Update data Tagihan.
     */
    public function update(UpdateTagihanRequest $request, Tagihan $tagihan)
    {
        // TODO: implementasi update
    }

    /**
     * Hapus Tagihan.
     */
    public function destroy(Tagihan $tagihan)
    {
        // TODO: implementasi delete
    }
}
