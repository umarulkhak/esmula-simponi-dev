<?php

namespace App\Http\Controllers;

use App\Models\Biaya;
use App\Models\Siswa;
use App\Models\Tagihan as Model;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTagihanRequest;
use App\Http\Requests\UpdateTagihanRequest;
use Carbon\Carbon;
use Exception;

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
     * Status default tagihan.
     */
    private const STATUS_BARU = 'baru';

    /**
     * Tampilkan daftar data Tagihan.
     */
    public function index(Request $request)
    {
        $query = Model::with(['user', 'siswa'])->groupBy('siswa_id');

        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('tanggal_tagihan', $request->bulan)
                  ->whereYear('tanggal_tagihan', $request->tahun);
        }

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
            'biaya'    => Biaya::pluck('nama_biaya_full', 'id'),
        ]);
    }

    /**
     * Simpan data Tagihan baru.
     */
    public function store(StoreTagihanRequest $request)
    {
        $requestData = $request->validated();

        $biayaIdArray = $requestData['biaya_id'] ?? [];

        // filter siswa
        $siswaQuery = Siswa::query();
        if (!empty($requestData['kelas'])) {
            $siswaQuery->where('kelas', $requestData['kelas']);
        }
        if (!empty($requestData['angkatan'])) {
            $siswaQuery->where('angkatan', $requestData['angkatan']);
        }
        $siswa = $siswaQuery->get();

        // ambil biaya
        $biaya = Biaya::whereIn('id', $biayaIdArray)->get();

        // parsing tanggal
        $tanggalTagihan    = Carbon::parse($requestData['tanggal_tagihan']);
        $tanggalJatuhTempo = Carbon::parse($requestData['tanggal_jatuh_tempo']);
        $bulanTagihan      = $tanggalTagihan->format('m');
        $tahunTagihan      = $tanggalTagihan->format('Y');

        $jumlahTersimpan = 0;

        foreach ($siswa as $itemSiswa) {
            foreach ($biaya as $itemBiaya) {
                $cekTagihan = Model::where('siswa_id', $itemSiswa->id)
                    ->where('nama_biaya', $itemBiaya->nama)
                    ->whereMonth('tanggal_tagihan', $bulanTagihan)
                    ->whereYear('tanggal_tagihan', $tahunTagihan)
                    ->first();

                if ($cekTagihan) {
                    continue;
                }

                Model::create([
                    'siswa_id'            => $itemSiswa->id,
                    'angkatan'            => $itemSiswa->angkatan,
                    'kelas'               => $itemSiswa->kelas,
                    'tanggal_tagihan'     => $tanggalTagihan,
                    'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
                    'nama_biaya'          => $itemBiaya->nama,
                    'jumlah_biaya'        => $itemBiaya->jumlah,
                    'keterangan'          => $requestData['keterangan'] ?? null,
                    'status'              => self::STATUS_BARU,
                ]);

                $jumlahTersimpan++;
            }
        }

        return redirect()
            ->route($this->routePrefix . '.index')
            ->with(
                'success',
                "Tagihan berhasil dibuat: {$jumlahTersimpan} tagihan baru untuk " . count($siswa) . " siswa."
            );
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
        } catch (Exception $e) {
            return redirect()
                ->route($this->routePrefix . '.index')
                ->with('error', 'Gagal menghapus tagihan: ' . $e->getMessage());
        }
    }
}
