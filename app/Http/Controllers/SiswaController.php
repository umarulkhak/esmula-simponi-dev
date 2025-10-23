<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Siswa as Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreSiswaRequest;
use App\Http\Requests\UpdateSiswaRequest;
use App\Exports\SiswaExport;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Controller untuk manajemen data Siswa.
 *
 * Mengelola seluruh operasi CRUD (Create, Read, Update, Delete) data siswa,
 * termasuk fitur tambahan:
 * - Upload & hapus foto profil
 * - Relasi dengan wali murid dan user pembuat
 * - Pencarian berbasis nama
 * - Hapus massal (beberapa atau semua data)
 *
 * Semua operasi create/update dilindungi oleh FormRequest untuk validasi otomatis.
 * Operasi delete (termasuk massal) menangani penghapusan file foto secara aman.
 *
 * @author  Umar Ulkhak
 * @date    3 Agustus 2025
 * @updated 14 Oktober 2025 — Tambah fitur hapus massal & perbarui dokumentasi
 */
class SiswaController extends Controller
{
    private string $viewPath = 'operator.';
    private string $routePrefix = 'siswa';
    private string $viewIndex = 'siswa_index';
    private string $viewForm = 'siswa_form';
    private string $viewShow = 'siswa_show';

    // =========================================================================
    // READ — Menampilkan Data
    // =========================================================================

    public function index(Request $request)
    {
        $query = Model::with('wali', 'user');

        // 🔹 Filter berdasarkan status
        // Jika tidak ada input 'status', default ke 'aktif'
        // Jika 'status' dikirim tapi kosong (''), tampilkan SEMUA
        if ($request->filled('status')) {
            $statusValue = $request->status;
            if ($statusValue !== '') {
                $query->where('status', $statusValue);
            }
            // Jika $statusValue === '', maka tidak ada where → tampilkan semua
        } else {
            // 🔸 Default: hanya tampilkan siswa aktif
            $query->where('status', 'aktif');
        }

        // Filter pencarian
        if ($request->filled('q')) {
            $query = $query->search($request->q);
        }

        $models = $query->latest()->paginate(20);

        return view($this->viewPath . $this->viewIndex, [
            'models'      => $models,
            'routePrefix' => $this->routePrefix,
            'title'       => 'Data Siswa',
        ]);
    }

    public function show(int $id)
    {
        $siswa = Model::findOrFail($id);

        return view($this->viewPath . $this->viewShow, [
            'model'       => $siswa,
            'title'       => 'Detail Siswa',
            'routePrefix' => $this->routePrefix,
        ]);
    }

    // =========================================================================
    // CREATE — Form & Penyimpanan
    // =========================================================================

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

    public function store(StoreSiswaRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('foto_siswa', 'public');
        }

        if ($request->filled('wali_id')) {
            $validated['wali_status'] = 'ok';
        }

        $validated['user_id'] = Auth::id();
        $siswa = Model::create($validated);

        flash("✅ Data siswa '{$siswa->nama}' berhasil disimpan")->success();

        return redirect()->route($this->routePrefix . '.index');
    }

    // =========================================================================
    // UPDATE — Form & Pembaruan
    // =========================================================================

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

    public function update(UpdateSiswaRequest $request, int $id)
    {
        $siswa = Model::findOrFail($id);
        $validated = $request->validated();

        $kelasLama = $siswa->kelas;
        $namaSiswa = $siswa->nama;

        if ($request->hasFile('foto')) {
            $this->deleteFotoIfExists($siswa->foto);
            $validated['foto'] = $request->file('foto')->store('foto_siswa', 'public');
        }

        $validated['wali_status'] = $request->filled('wali_id') ? 'ok' : null;
        $validated['user_id'] = Auth::id();

        $siswa->update($validated);

        $pesan = "✅ Data siswa '{$namaSiswa}' berhasil diperbarui";
        if ($kelasLama !== $validated['kelas']) {
            $pesan .= " (Kelas: {$kelasLama} → {$validated['kelas']})";
        }

        flash($pesan)->success();

        return redirect()->route($this->routePrefix . '.index');
    }

    // =========================================================================
    // DELETE — Penghapusan Data
    // =========================================================================

    public function destroy(int $id)
    {
        $siswa = Model::findOrFail($id);
        $this->deleteFotoIfExists($siswa->foto);

        $namaSiswa = $siswa->nama;
        $siswa->delete();

        flash("🗑️ Data siswa '{$namaSiswa}' berhasil dihapus")->success();

        return redirect()->route($this->routePrefix . '.index');
    }

    public function massDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'nullable|string',
            'delete_all' => 'nullable|boolean',
        ]);

        try {
            if ($request->boolean('delete_all')) {
                $siswas = Model::select('nama', 'kelas')->get();
                $total = $siswas->count();

                Model::chunk(200, function ($chunk) {
                    foreach ($chunk as $siswa) {
                        $this->deleteFotoIfExists($siswa->foto);
                        $siswa->delete();
                    }
                });

                if ($total > 0) {
                    $pesan = $this->formatDaftarSiswa($siswas);
                    flash("🗑️ Berhasil menghapus semua data siswa ($total):<br>{$pesan}")->success();
                } else {
                    flash("🗑️ Tidak ada data siswa untuk dihapus")->warning();
                }
            } else {
                $ids = [];
                if ($request->filled('ids')) {
                    $parsed = json_decode($request->ids, true);
                    if (is_array($parsed)) {
                        $ids = array_filter($parsed, fn($id) => is_numeric($id));
                    }
                }

                if (!empty($ids)) {
                    $siswas = Model::whereIn('id', $ids)->select('id', 'nama', 'kelas')->get();

                    foreach ($siswas as $siswa) {
                        $this->deleteFotoIfExists($siswa->foto);
                        $siswa->delete();
                    }

                    if ($siswas->isNotEmpty()) {
                        $pesan = $this->formatDaftarSiswa($siswas);
                        flash("🗑️ Berhasil menghapus " . count($siswas) . " data siswa:<br>{$pesan}")->success();
                    } else {
                        flash("❌ Tidak ada data siswa yang valid untuk dihapus")->warning();
                    }
                } else {
                    flash("❌ Tidak ada data yang dipilih untuk dihapus")->warning();
                }
            }
        } catch (\Exception $e) {
            flash("❌ Gagal menghapus data: " . $e->getMessage())->error();
        }

        return redirect()->route($this->routePrefix . '.index');
    }

    private function formatDaftarSiswa($siswas): string
    {
        $maxTampil = 10;
        $total = $siswas->count();

        if ($total <= $maxTampil) {
            return $siswas->map(fn($s) => "{$s->nama} ({$s->kelas})")->join(', ');
        }

        $daftarAwal = $siswas->take($maxTampil)
            ->map(fn($s) => "{$s->nama} ({$s->kelas})")
            ->join(', ');

        $sisa = $total - $maxTampil;
        return "{$daftarAwal}, dan {$sisa} lainnya";
    }

    // =========================================================================
    // HELPER — Fungsi Bantuan Internal
    // =========================================================================

    private function getWaliOptions()
    {
        return User::where('akses', 'wali')
                   ->orderBy('name')
                   ->pluck('name', 'id');
    }

    private function deleteFotoIfExists(?string $path): void
    {
        if (!empty($path) && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
    public function export()
    {
        return Excel::download(new SiswaExport, 'data_siswa_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    // =========================================================================
    // PROMOSI & KELULUSAN
    // =========================================================================

    public function promosiForm()
    {
        $jumlahVII = Model::where('kelas', 'VII')->where('status', 'aktif')->count();
        $jumlahVIII = Model::where('kelas', 'VIII')->where('status', 'aktif')->count();
        $jumlahIX = Model::where('kelas', 'IX')->where('status', 'aktif')->count();

        return view($this->viewPath . 'siswa_promosi', [
            'title' => 'Promosi & Kelulusan Siswa',
            'jumlahVII' => $jumlahVII,
            'jumlahVIII' => $jumlahVIII,
            'jumlahIX' => $jumlahIX,
            'tahunSekarang' => now()->year,
        ]);
    }

    public function prosesPromosi(Request $request)
    {
        // Validasi: hanya operator
        if (auth()->user()->akses !== 'operator') {
            abort(403);
        }

        // Validasi konfirmasi
        $request->validate([
            'confirm' => 'required|accepted',
        ], [
            'confirm.accepted' => 'Anda harus mencentang konfirmasi untuk melanjutkan.'
        ]);

        \DB::beginTransaction();
        try {
            $tahunSekarang = now()->year;

            // ✅ UPDATE SEMUA DALAM SATU QUERY — BERDASARKAN KONDISI AWAL
            Model::where('status', 'aktif')
                ->update([
                    'kelas' => \DB::raw("CASE
                        WHEN kelas = 'VII' THEN 'VIII'
                        WHEN kelas = 'VIII' THEN 'IX'
                        ELSE kelas
                    END"),
                    'status' => \DB::raw("CASE
                        WHEN kelas = 'IX' THEN 'lulus'
                        ELSE status
                    END"),
                    'tahun_lulus' => \DB::raw("CASE
                        WHEN kelas = 'IX' THEN {$tahunSekarang}
                        ELSE tahun_lulus
                    END")
                ]);

            \DB::commit();

            flash("✅ Promosi & kelulusan berhasil! Tahun ajaran baru dimulai.")->success();
            return redirect()->route($this->routePrefix . '.index');

        } catch (\Exception $e) {
            \DB::rollBack();
            flash("❌ Gagal memproses promosi: " . $e->getMessage())->error();
            return back();
        }
    }
}
