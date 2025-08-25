<?php

namespace App\Http\Controllers;

use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LevelController extends Controller
{
    // Properti untuk menyimpan pesan validasi kustom dalam Bahasa Indonesia
    public array $customMessages = [
        '*.required' => ':Attribute tidak boleh kosong.',
        '*.max' => ':Attribute maksimal :max karakter.',
        '*.unique' => ':Attribute ini sudah terdaftar, silakan gunakan yang lain.',
        '*.integer' => ':Attribute harus berupa angka.',
    ];

    // Properti untuk mengubah nama atribut default menjadi lebih ramah
    public array $customAttributes = [
        'name' => 'Nama Level',
        'level_point' => 'level_Point',
    ];

    /**
     * Menampilkan daftar semua produk.
     */
    public function index()
    {
        $this->authorize('viewAny', Level::class);
        $levels = Level::orderBy('id', 'asc')->paginate(10);
        return view('levels.index', compact('levels'));
    }

    /**
     * Menampilkan form untuk membuat produk baru.
     */
    public function create()
    {
        $this->authorize('create', Level::class);
        return view('levels.create');
    }

    /**
     * Menyimpan produk baru ke database.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Level::class);

        // Validasi disesuaikan dengan kolom 'name' di database
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:levels,name'],
            'level_point' => ['required', 'integer'],
        ], $this->customMessages, $this->customAttributes);

        // Simpan data menggunakan 'name' agar cocok dengan $fillable dan database
        Level::create([
            'name' => $request->name,
            'level_point' => $request->level_point,
        ]);

        return redirect()->route('levels.index')
                        ->with('success', 'Level baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit produk.
     */
    public function edit(Level $level)
    {
        $this->authorize('update', $level);
        return view('levels.edit', compact('level'));
    }

    /**
     * Memperbarui data produk di database.
     */
    public function update(Request $request, Level $level)
    {
        $this->authorize('update', $level);

        // Validasi disesuaikan dengan kolom 'name' di database
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('products', 'name')->ignore($level->id)],
            'level_point' => ['required', 'integer'],
        ], $this->customMessages, $this->customAttributes);

        // Update model menggunakan 'name' agar cocok dengan $fillable dan database
        $level->update([
            'name' => $validatedData['name'],
            'level_point' => $validatedData['level_point'],
        ]);

        return redirect()->route('levels.index')
                        ->with('success', 'Data level berhasil diperbarui.');
    }

    /**
     * Menghapus produk dari database.
     */
    public function destroy(Level $level)
    {
        $this->authorize('delete', $level);
        $level->delete();
        return redirect()->route('levels.index')
                        ->with('success', 'Level berhasil dihapus.');
    }
}