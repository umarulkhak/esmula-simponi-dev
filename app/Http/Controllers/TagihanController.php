<?php

namespace App\Http\Controllers;

use App\Models\Biaya;
use App\Models\Siswa;
use App\Models\Tagihan as Model;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTagihanRequest;
use App\Http\Requests\UpdateTagihanRequest;
use Carbon\Carbon;

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
        $requestData = $request->validated();

        // 2. Ambil data biaya yang ditagihkan
        $biayaIdArray = $requestData['biaya_id'] ?? [];

        // 3. Ambil data siswa berdasarkan kelas atau angkatan
        $siswa = Siswa::query();

        if (!empty($requestData['kelas'])) {
            $siswa->where('kelas', $requestData['kelas']);
        }

        if (!empty($requestData['angkatan'])) {
            $siswa->where('angkatan', $requestData['angkatan']);
        }

        $siswa = $siswa->get();
        $biaya = Biaya::whereIn('id', $biayaIdArray)->get();

        // parsing tanggal agar bisa ambil bulan & tahun
        $tanggalTagihan    = Carbon::parse($requestData['tanggal_tagihan']);
        $tanggalJatuhTempo = Carbon::parse($requestData['tanggal_jatuh_tempo']);

        $bulanTagihan = $tanggalTagihan->format('m');
        $tahunTagihan = $tanggalTagihan->format('Y');

        $jumlahTersimpan = 0;

        // 4. Loop data siswa dan biaya
        foreach ($siswa as $itemSiswa) {
            foreach ($biaya as $itemBiaya) {
                // cek apakah tagihan sudah ada
                $cekTagihan = Model::where('siswa_id', $itemSiswa->id)
                    ->where('nama_biaya', $itemBiaya->nama)
                    ->whereMonth('tanggal_tagihan', $bulanTagihan)
                    ->whereYear('tanggal_tagihan', $tahunTagihan)
                    ->first();

                if (!$cekTagihan) {
                    $dataTagihan = [
                        'siswa_id'            => $itemSiswa->id,
                        'angkatan'            => $itemSiswa->angkatan,
                        'kelas'               => $itemSiswa->kelas,
                        'tanggal_tagihan'     => $tanggalTagihan,
                        'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
                        'nama_biaya'          => $itemBiaya->nama,
                        'jumlah_biaya'        => $itemBiaya->jumlah,
                        'keterangan'          => $requestData['keterangan'] ?? null,
                        'status'              => 'baru',
                    ];

                    Model::create($dataTagihan);
                    $jumlahTersimpan++;
                }
            }
        }

        // 5. Redirect dengan pesan sukses
        return redirect()
            ->route($this->routePrefix . '.index')
            ->with('success', "Tagihan berhasil dibuat: {$jumlahTersimpan} tagihan baru untuk " . count($siswa) . " siswa.");
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
        try {
            $tagihan->delete();

            return redirect()
                ->route($this->routePrefix . '.index')
                ->with('success', 'Tagihan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->route($this->routePrefix . '.index')
                ->with('error', 'Gagal menghapus tagihan: ' . $e->getMessage());
        }
    }
}
