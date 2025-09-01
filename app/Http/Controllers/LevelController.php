<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; // <-- Tambahan baru

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
        '*.array' => ':Attribute harus berupa array.',
        '*.min' => ':Attribute minimal :min.',
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
    // --- METHOD STORE DIPERBARUI UNTUK MENANGANI BANYAK DATA ---
    public function store(Request $request)
    {
        $this->authorize('create', Level::class);

        // Validasi sekarang akan memeriksa sebuah array 'levels'
        $validated = $request->validate([
            'levels'              => ['required', 'array', 'min:1'],
            'levels.*.name'       => ['required', 'string', 'max:255', 'unique:levels,name'],
            'levels.*.level_point'=> ['required', 'integer', 'min:0'],
        ], $this->customMessages, [
            'levels.*.name'       => 'Nama Level',
            'levels.*.level_point'=> 'Point Level',
        ]);

        // Gunakan DB Transaction untuk memastikan semua data berhasil disimpan
        // atau tidak sama sekali jika ada error.
        DB::transaction(function () use ($validated) {
            foreach ($validated['levels'] as $levelData) {
                Level::create([
                    'name' => $levelData['name'],
                    'level_point' => $levelData['level_point'],
                ]);
            }
        });

        // Picu pembaruan statistik semua customer SETELAH semua level baru dibuat
        $this->updateAllCustomerStats();

        return redirect()->route('levels.index')
            ->with('success', count($validated['levels']) . ' level baru berhasil dibuat.');
    }
    // --- AKHIR PERUBAHAN ---

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

        $this->updateAllCustomerStats();

        return redirect()->route('levels.index')->with('success', 'Data level berhasil diperbarui.');
    }

    /**
     * Menghapus level dari database.
     */
    public function destroy(Level $level)
    {
        $this->authorize('delete', $level);

        if ($level->name === 'N/A') {
            return back()->with('error', 'Level "N/A" tidak boleh dihapus.');
        }

        $defaultLevel = Level::where('name', 'N/A')->first();
        if ($defaultLevel) {
            Customer::where('level_id', $level->id)->update(['level_id' => $defaultLevel->id]);
        }
        
        $level->delete();

        $this->updateAllCustomerStats();

        return redirect()->route('levels.index')->with('success', 'Level berhasil dihapus.');
    }

    /**
     * Fungsi untuk memicu kalkulasi ulang statistik SEMUA customer.
     */
    private function updateAllCustomerStats()
    {
        Customer::chunk(200, function ($customers) {
            foreach ($customers as $customer) {
                $customer->recalculateStats();
            }
        });
    }
}