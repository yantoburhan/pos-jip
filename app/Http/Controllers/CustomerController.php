<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Level;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Custom messages untuk validasi
     */
    public array $customMessages = [
        '*.required' => ':Attribute tidak boleh kosong.',
        '*.max' => ':Attribute maksimal :max karakter.',
        '*.unique' => ':Attribute sudah terdaftar.',
    ];

    /**
     * Custom attribute names agar lebih ramah
     */
    public array $customAttributes = [
        'no_hp_cust'   => 'No HP',
        'cust_name'    => 'Nama Customer',
    ];

    /**
     * Tampilkan daftar customer
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Customer::class);
        $query = Customer::with('level')->orderBy('cust_name', 'asc');
        if ($request->filled('q')) {
            $query->where('cust_name', 'like', '%'.$request->q.'%')
                  ->orWhere('no_hp_cust', 'like', '%'.$request->q.'%');
        }
        $customers = $query->paginate(10);
        return view('customers.index', compact('customers'));
    }

    /**
     * Form tambah customer
     */
    public function create(Request $request)
    {
        $this->authorize('create', Customer::class);
        $no_hp_cust = $request->query('no_hp_cust', '');
        return view('customers.create', compact('no_hp_cust'));
    }

    /**
     * Simpan customer baru
     */
    public function store(Request $request)
    {
        $this->authorize('create', Customer::class);

        // PERBAIKAN: Validasi HANYA untuk field yang diinput oleh user.
        $validatedData = $request->validate([
            'no_hp_cust' => ['required', 'string', 'max:15', 'unique:customers,no_hp_cust'],
            'cust_name'  => ['required', 'string', 'max:255'],
        ], $this->customMessages, $this->customAttributes);

        // Cari level "N/A" sebagai level default.
        $defaultLevel = Level::where('name', 'N/A')->first();

        if (!$defaultLevel) {
            return back()->with('error', 'Level default "N/A" tidak ditemukan. Harap buat level dengan nama "N/A" di Manajemen Level.')->withInput();
        }

        Customer::create([
            'no_hp_cust'  => $validatedData['no_hp_cust'],
            'cust_name'   => $validatedData['cust_name'],
            'cust_point'  => 0,
            'total_spent' => 0,
            'level_id'    => $defaultLevel->id, // Gunakan ID dari level "N/A"
        ]);

        return redirect()->route('customers.index')
                         ->with('success', 'Customer baru berhasil ditambahkan.');
    }

    /**
     * Form edit customer
     */
    public function edit(Customer $customer)
    {
        $this->authorize('update', $customer);
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update customer
     */
    public function update(Request $request, Customer $customer)
    {
        $this->authorize('update', $customer);

        // PERBAIKAN: Validasi HANYA untuk field yang bisa diedit.
        $validatedData = $request->validate([
            'no_hp_cust'   => [
                'required', 'string', 'max:15',
                Rule::unique('customers')->ignore($customer->no_hp_cust, 'no_hp_cust'),
            ],
            'cust_name'    => ['required', 'string', 'max:255'],
        ], $this->customMessages, $this->customAttributes);
        
        // Gabungkan data dari user DENGAN data hasil kalkulasi otomatis
        $stats = $this->recalculateCustomerStats($validatedData['no_hp_cust']);
        
        $customer->update(array_merge($validatedData, $stats));

        return redirect()->route('customers.index')
                         ->with('success', 'Data customer berhasil diperbarui.');
    }

    /**
     * Hapus customer
     */
    public function destroy(Customer $customer)
    {
        $this->authorize('delete', $customer);
        $customer->delete();
        return redirect()->route('customers.index')
                         ->with('success', 'Customer berhasil dihapus.');
    }
    
    /**
     * Fungsi private untuk menghitung ulang statistik customer.
     */
    private function recalculateCustomerStats(string $noHpCust): array
    {
        $totalSpent = Transaction::where('no_hp_cust', $noHpCust)->sum('total_penjualan');
        $totalPoin = Transaction::where('no_hp_cust', $noHpCust)->sum('total_poin');

        // Cari level yang sesuai, selain "N/A"
        $level = Level::where('level_point', '<=', $totalPoin)
                      ->where('name', '!=', 'N/A')
                      ->orderBy('level_point', 'desc')
                      ->first();
        
        // Jika tidak ada level yang cocok (poin < terendah), gunakan level 'N/A'
        if (!$level) {
            $level = Level::where('name', 'N/A')->first();
        }
        
        return [
            'total_spent' => $totalSpent,
            'cust_point'  => $totalPoin,
            'level_id'    => $level ? $level->id : null,
        ];
    }
}