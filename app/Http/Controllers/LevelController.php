<?php

namespace App\Http\Controllers;

use App\Models\Customer;
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
        // Urutkan berdasarkan poin agar lebih logis
        $levels = Level::orderBy('level_point', 'asc')->paginate(10);
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

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:levels,name'],
            'level_point' => ['required', 'integer', 'min:0'],
        ], $this->customMessages, $this->customAttributes);

        Level::create($validated);
        
        // PERBAIKAN: Picu pembaruan statistik semua customer
        $this->updateAllCustomerStats();

        return redirect()->route('levels.index')->with('success', 'Level baru berhasil dibuat.');
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

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('levels', 'name')->ignore($level->id)],
            'level_point' => ['required', 'integer', 'min:0'],
        ], $this->customMessages, $this->customAttributes);

        $level->update($validatedData);

        // PERBAIKAN: Picu pembaruan statistik semua customer
        $this->updateAllCustomerStats();

        return redirect()->route('levels.index')->with('success', 'Data level berhasil diperbarui.');
    }

    /**
     * Menghapus level dari database.
     */
    public function destroy(Level $level)
    {
        $this->authorize('delete', $level);

        // Pengaman: Jangan biarkan level "N/A" dihapus
        if ($level->name === 'N/A') {
            return back()->with('error', 'Level "N/A" tidak boleh dihapus.');
        }

        // Cari level "N/A" untuk dijadikan level default bagi customer yang ditinggalkan
        $defaultLevel = Level::where('name', 'N/A')->first();
        if ($defaultLevel) {
            Customer::where('level_id', $level->id)->update(['level_id' => $defaultLevel->id]);
        }
        
        $level->delete();

        // PERBAIKAN: Picu pembaruan statistik semua customer
        $this->updateAllCustomerStats();

        return redirect()->route('levels.index')->with('success', 'Level berhasil dihapus.');
    }

    /**
     * Fungsi untuk memicu kalkulasi ulang statistik SEMUA customer.
     */
    private function updateAllCustomerStats()
    {
        // Gunakan chunking agar efisien dan tidak menghabiskan memori
        Customer::chunk(200, function ($customers) {
            foreach ($customers as $customer) {
                // Panggil method recalculateStats() yang ada di Model Customer
                $customer->recalculateStats();
            }
        });
    }
}