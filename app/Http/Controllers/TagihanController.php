<?php

namespace App\Http\Controllers;

use App\Models\Biaya;
use App\Models\Siswa;
use App\Models\Tagihan as Model;
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
     * Tampilkan daftar data Tagihan.
     */
    public function index(Request $request)
    {
        $query = Model::with(['user', 'siswa']);

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
            'model'    => new Model(),
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
        // 1. Validasi sudah dilakukan oleh FormRequest
        // 2. Ambil data biaya yang ditagihkan
        // 3. Ambil data siswa berdasarkan kelas atau angkatan
        // 4. Loop data siswa
        // 5. Simpan tagihan berdasarkan biaya & siswa
        // 6. Simpan notifikasi database untuk tagihan
        // 7. Kirim pesan WhatsApp (jika ada integrasi)
        // 8. Redirect back dengan pesan sukses
    }

    /**
     * Detail Tagihan.
     */
    public function show(Model $tagihan)
    {
        // TODO: implementasi detail
    }

    /**
     * Form edit Tagihan.
     */
    public function edit(Model $tagihan)
    {
        // TODO: implementasi form edit
    }

    /**
     * Update data Tagihan.
     */
    public function update(UpdateTagihanRequest $request, Model $tagihan)
    {
        // TODO: implementasi update
    }

    /**
     * Hapus Tagihan.
     */
    public function destroy(Model $tagihan)
    {
        // TODO: implementasi delete
    }
}
