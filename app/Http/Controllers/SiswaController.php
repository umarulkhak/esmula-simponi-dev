<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Siswa as Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreSiswaRequest;
use App\Http\Requests\UpdateSiswaRequest;

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
    /**
     * Prefix path untuk view operator.
     */
    private string $viewPath = 'operator.';

    /**
     * Prefix route untuk siswa (tanpa group name prefix).
     * Contoh: 'siswa.index', 'siswa.create', dll.
     */
    private string $routePrefix = 'siswa';

    /**
     * Nama view untuk halaman index.
     */
    private string $viewIndex = 'siswa_index';

    /**
     * Nama view untuk form tambah/edit.
     */
    private string $viewForm = 'siswa_form';

    /**
     * Nama view untuk halaman detail.
     */
    private string $viewShow = 'siswa_show';

    // =========================================================================
    // READ — Menampilkan Data
    // =========================================================================

    /**
     * Menampilkan daftar siswa dengan fitur pencarian dan pagination.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Model::with('wali', 'user');

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

    /**
     * Menampilkan detail data siswa.
     *
     * @param  int  $id  ID siswa
     * @return \Illuminate\View\View
     */
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

    /**
     * Menampilkan form tambah data siswa.
     *
     * @return \Illuminate\View\View
     */
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

    /**
     * Menyimpan data siswa baru ke database.
     *
     * @param  \App\Http\Requests\StoreSiswaRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreSiswaRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('public/foto_siswa');
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

    /**
     * Menampilkan form edit data siswa.
     *
     * @param  int  $id  ID siswa
     * @return \Illuminate\View\View
     */
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

    /**
     * Memperbarui data siswa di database.
     *
     * @param  \App\Http\Requests\UpdateSiswaRequest  $request
     * @param  int  $id  ID siswa
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateSiswaRequest $request, int $id)
    {
        $siswa = Model::findOrFail($id);
        $validated = $request->validated();

        $kelasLama = $siswa->kelas;
        $namaSiswa = $siswa->nama;

        if ($request->hasFile('foto')) {
            $this->deleteFotoIfExists($siswa->foto);
            $validated['foto'] = $request->file('foto')->store('public/foto_siswa');
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

    /**
     * Menghapus satu data siswa dari database.
     *
     * @param  int  $id  ID siswa
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        $siswa = Model::findOrFail($id);
        $this->deleteFotoIfExists($siswa->foto);

        $namaSiswa = $siswa->nama;
        $siswa->delete();

        flash("🗑️ Data siswa '{$namaSiswa}' berhasil dihapus")->success();

        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Menghapus beberapa atau semua data siswa sekaligus.
     *
     * Fitur:
     * - Hapus berdasarkan daftar ID (dikirim sebagai JSON string)
     * - Hapus semua data siswa (dengan konfirmasi ekstra di frontend)
     * - Tampilkan daftar nama & kelas yang dihapus di flash message
     * - Batasi tampilan daftar jika >10 data (hindari overload UI)
     * - Aman: hanya hapus data yang benar-benar ada di database
     * - Otomatis hapus file foto terkait
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Format daftar siswa untuk ditampilkan di flash message.
     *
     * Jika jumlah siswa ≤ 10: tampilkan semua.
     * Jika > 10: tampilkan 10 pertama + "dan X lainnya".
     *
     * @param  \Illuminate\Support\Collection  $siswas
     * @return string
     */
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

    /**
     * Ambil daftar wali murid untuk dropdown select.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getWaliOptions()
    {
        return User::where('akses', 'wali')
                   ->orderBy('name')
                   ->pluck('name', 'id');
    }

    /**
     * Hapus file foto dari storage jika path-nya valid dan ada.
     *
     * @param  string|null  $path
     * @return void
     */
    private function deleteFotoIfExists(?string $path): void
    {
        if (!empty($path) && Storage::exists($path)) {
            Storage::delete($path);
        }
    }
}
