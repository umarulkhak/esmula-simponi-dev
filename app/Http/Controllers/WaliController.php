<?php
/**
 * WaliController
 *
 * Controller untuk mengelola data wali murid.
 *
 * @author Umar Ulkhak
 */

namespace App\Http\Controllers;

use App\Models\User as Model;
use Illuminate\Http\Request;

class WaliController extends Controller
{
    /**
     * Path view untuk halaman operator.
     */
    private string $viewPath = 'operator.';

    /**
     * Prefix route untuk wali.
     */
    private string $routePrefix = 'wali';

    /**
     * Nama view untuk halaman index wali.
     */
    private string $viewIndex = 'wali_index';

    /**
     * Nama view untuk form wali.
     */
    private string $viewForm = 'user_form';

    /**
     * Nama view untuk detail wali.
     */
    private string $viewShow = 'wali_show';

    /**
     * Menampilkan daftar wali murid.
     */
    public function index()
    {
        $models = Model::wali()
            ->latest()
            ->paginate(50);

        return view($this->viewPath . $this->viewIndex, [
            'models'      => $models,
            'routePrefix' => $this->routePrefix,
            'title'       => 'Data Wali Murid',
        ]);
    }

    /**
     * Menampilkan form tambah wali murid.
     */
    public function create()
    {
        return view($this->viewPath . $this->viewForm, [
            'model'  => new Model(),
            'method' => 'POST',
            'route'  => $this->routePrefix . '.store',
            'button' => 'SIMPAN',
            'title'  => 'Form Data Wali Murid',
        ]);
    }

    /**
     * Menyimpan data wali murid baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'nohp'     => 'required|string|unique:users,nohp',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['email_verified_at'] = now();
        $validated['akses'] = 'wali';

        Model::create($validated);

        flash('Data berhasil disimpan')->success();
        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Menampilkan form edit wali murid.
     */
    public function edit($id)
    {
        $user = Model::wali()->findOrFail($id);

        return view($this->viewPath . $this->viewForm, [
            'model'  => $user,
            'method' => 'PUT',
            'route'  => [$this->routePrefix . '.update', $user->id],
            'button' => 'UPDATE',
            'title'  => 'Form Data Wali Murid',
        ]);
    }

    /**
     * Memperbarui data wali murid.
     */
    public function update(Request $request, $id)
    {
        $user = Model::wali()->findOrFail($id);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'nohp'     => 'required|string|unique:users,nohp,' . $user->id,
            'password' => 'nullable|string|min:6',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        flash('Data berhasil diperbarui')->success();
        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Menghapus wali murid berdasarkan ID.
     */
    public function destroy($id)
    {
        $user = Model::wali()->findOrFail($id);
        $user->delete();

        flash('Data berhasil dihapus')->success();
        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Menampilkan detail wali dan daftar siswa yang belum punya wali.
     */
    public function show($id)
    {
        return view($this->viewPath . $this->viewShow, [
            'siswa' => \App\Models\Siswa::whereNull('wali_id')->pluck('nama', 'id'),
            'model' => Model::wali()->findOrFail($id),
            'title' => 'Detail Data Wali'
        ]);
    }
}
