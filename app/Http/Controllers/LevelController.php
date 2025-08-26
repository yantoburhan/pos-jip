<?php

namespace App\Http\Controllers;

use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LevelController extends Controller
{
    // =========================
    // Pesan validasi kustom (Bahasa Indonesia)
    // =========================
    public array $customMessages = [
        '*.required' => ':Attribute tidak boleh kosong.',
        '*.max' => ':Attribute maksimal :max karakter.',
        '*.unique' => ':Attribute ini sudah terdaftar, silakan gunakan yang lain.',
        '*.integer' => ':Attribute harus berupa angka.',
    ];

    // =========================
    // Nama atribut ramah pengguna
    // =========================
    public array $customAttributes = [
        'name' => 'Nama Level',
        'level_point' => 'Point Level',
    ];

    /**
     * Menampilkan daftar semua level.
     */
    public function index()
    {
        $this->authorize('viewAny', Level::class);

        $levels = Level::orderBy('id', 'asc')->paginate(10);

        return view('levels.index', compact('levels'));
    }

    /**
     * Menampilkan form untuk membuat level baru.
     */
    public function create()
    {
        $this->authorize('create', Level::class);

        return view('levels.create');
    }

    /**
     * Menyimpan level baru ke database.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Level::class);

        // Validasi data
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:levels,name'],
            'level_point' => ['required', 'integer'],
        ], $this->customMessages, $this->customAttributes);

        // Simpan ke database
        Level::create([
            'name' => $request->name,
            'level_point' => $request->level_point,
        ]);

        return redirect()->route('levels.index')
                        ->with('success', 'Level baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit level.
     */
    public function edit(Level $level)
    {
        $this->authorize('update', $level);

        return view('levels.edit', compact('level'));
    }

    /**
     * Memperbarui data level di database.
     */
    public function update(Request $request, Level $level)
    {
        $this->authorize('update', $level);

        // ✅ Perbaikan: validasi unique harus ke tabel "levels", bukan "products"
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('levels', 'name')->ignore($level->id)
            ],
            'level_point' => ['required', 'integer'],
        ], $this->customMessages, $this->customAttributes);

        // Update ke database
        $level->update([
            'name' => $validatedData['name'],
            'level_point' => $validatedData['level_point'],
        ]);

        return redirect()->route('levels.index')
                        ->with('success', 'Data level berhasil diperbarui.');
    }

    /**
     * Menghapus level dari database.
     */
    public function destroy(Level $level)
    {
        $this->authorize('delete', $level);

        $level->delete();

        return redirect()->route('levels.index')
                        ->with('success', 'Level berhasil dihapus.');
    }
}
