<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Biaya;
use App\Models\Siswa;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTagihanRequest;

/**
 * Controller untuk manajemen Tagihan (CRUD) dengan detail biaya.
 */
class TagihanController extends Controller
{
    private string $viewPath = 'operator.';
    private string $routePrefix = 'tagihan';
    private string $viewIndex = 'tagihan_index';
    private string $viewForm  = 'tagihan_form';
    private string $viewShow  = 'tagihan_show';
    private const STATUS_BARU = 'baru';

    /**
     * Daftar tagihan.
     */
    public function index(Request $request)
    {
        $query = Tagihan::with(['user', 'siswa'])
            ->groupBy('siswa_id');

        if ($request->filled(['bulan', 'tahun'])) {
            $query->whereMonth('tanggal_tagihan', $request->bulan)
                  ->whereYear('tanggal_tagihan', $request->tahun);
        }

        if ($request->filled('q')) {
            $query->search($request->q);
        } else {
            $query->latest();
        }

        return view($this->viewPath . $this->viewIndex, [
            'models'      => $query->paginate(50),
            'routePrefix' => $this->routePrefix,
            'title'       => 'Data Tagihan',
        ]);
    }

    /**
     * Form tambah tagihan.
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
     * Simpan tagihan baru dengan detail biaya.
     */
    public function store(StoreTagihanRequest $request)
    {
        $data      = $request->validated();
        $biayaIds  = $data['biaya_id'] ?? [];
        $siswaList = $this->filterSiswa($data);
        $biayaList = Biaya::whereIn('id', $biayaIds)->get();

        $tanggalTagihan    = Carbon::parse($data['tanggal_tagihan']);
        $tanggalJatuhTempo = Carbon::parse($data['tanggal_jatuh_tempo']);
        $bulan             = $tanggalTagihan->format('m');
        $tahun             = $tanggalTagihan->format('Y');

        $jumlahTersimpan = 0;

        foreach ($siswaList as $siswa) {
            foreach ($biayaList as $biaya) {
                if ($this->cekDuplikat($siswa->id, $biaya->nama, $bulan, $tahun)) {
                    continue;
                }

                // Simpan Tagihan utama
                $tagihan = Tagihan::create([
                    'siswa_id'            => $siswa->id,
                    'angkatan'            => $siswa->angkatan,
                    'kelas'               => $siswa->kelas,
                    'tanggal_tagihan'     => $tanggalTagihan,
                    'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
                    'keterangan'          => $data['keterangan'] ?? null,
                    'status'              => self::STATUS_BARU,
                ]);

                // Simpan TagihanDetail
                $tagihan->details()->create([
                    'nama_biaya'   => $biaya->nama,
                    'jumlah_biaya' => $biaya->jumlah,
                ]);

                $jumlahTersimpan++;
            }
        }

        return redirect()
            ->route($this->routePrefix . '.index')
            ->with(
                'success',
                "Tagihan berhasil dibuat: {$jumlahTersimpan} tagihan baru untuk " . count($siswaList) . " siswa."
            );
    }

    /**
     * Detail tagihan per siswa.
     */
    public function show(Request $request, int $id)
    {
        $siswa = Siswa::findOrFail($id);

        $query = Tagihan::with('details')
            ->where('siswa_id', $siswa->id);

        if ($bulan = $request->input('bulan')) {
            $query->whereMonth('tanggal_tagihan', $bulan);
        }

        if ($tahun = $request->input('tahun')) {
            $tahun = strlen($tahun) === 2 ? '20' . $tahun : $tahun;
            $query->whereYear('tanggal_tagihan', $tahun);
        }

        return view($this->viewPath . $this->viewShow, [
            'siswa'   => $siswa,
            'tagihan' => $query->orderBy('tanggal_tagihan', 'desc')->get(),
            'title'   => "Daftar Tagihan {$siswa->nama}",
        ]);
    }

    /**
     * Hapus tagihan.
     */
    public function destroy(Tagihan $tagihan)
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

    /* ==========================
     |  PRIVATE HELPER
     |==========================*/

    /**
     * Filter siswa berdasarkan kelas & angkatan.
     */
    private function filterSiswa(array $data)
    {
        return Siswa::query()
            ->when(!empty($data['kelas']), fn($q) => $q->where('kelas', $data['kelas']))
            ->when(!empty($data['angkatan']), fn($q) => $q->where('angkatan', $data['angkatan']))
            ->get();
    }

    /**
     * Cek duplikat tagihan berdasarkan detail biaya.
     */
    private function cekDuplikat(int $siswaId, string $namaBiaya, string $bulan, string $tahun): bool
    {
        return Tagihan::where('siswa_id', $siswaId)
            ->whereMonth('tanggal_tagihan', $bulan)
            ->whereYear('tanggal_tagihan', $tahun)
            ->whereHas('details', fn($q) => $q->where('nama_biaya', $namaBiaya))
            ->exists();
    }
}
