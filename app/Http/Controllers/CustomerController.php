<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Level;
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
        '*.integer' => ':Attribute harus berupa angka.',
        '*.exists' => ':Attribute tidak valid.',
        '*.min' => ':Attribute minimal :min.',
    ];

    /**
     * Custom attribute names agar lebih ramah
     */
    public array $customAttributes = [
        'no_hp_cust'   => 'No HP',
        'cust_name'    => 'Nama Customer',
        'level_id'     => 'Level',
        'total_spent'  => 'Total Belanja',
    ];

    /**
     * Tampilkan daftar customer
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Customer::class);

        $query = Customer::with('level')->orderBy('cust_name', 'asc');

        // fitur pencarian (opsional)
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
    public function create()
    {
        $this->authorize('create', Customer::class);

        $levels = Level::all();
        return view('customers.create', compact('levels'));
    }

    /**
     * Simpan customer baru
     */
    public function store(Request $request)
    {
        $this->authorize('create', Customer::class);

        $validatedData = $request->validate([
            'no_hp_cust'   => ['required', 'string', 'max:15', 'unique:customers,no_hp_cust'],
            'cust_name'    => ['required', 'string', 'max:255'],
            'level_id'     => ['required', 'exists:levels,id'],
            'total_spent'  => ['required', 'integer', 'min:0'],
        ], $this->customMessages, $this->customAttributes);

        // ambil data level
        $level = Level::findOrFail($validatedData['level_id']);

        Customer::create([
            'no_hp_cust'  => $validatedData['no_hp_cust'],
            'cust_name'   => $validatedData['cust_name'],
            'level_id'    => $validatedData['level_id'],
            'cust_point'  => $level->level_point ?? 0, // default aman
            'total_spent' => $validatedData['total_spent'],
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

        $levels = Level::all();
        return view('customers.edit', compact('customer', 'levels'));
    }

    /**
     * Update customer
     */
    public function update(Request $request, Customer $customer)
    {
        $this->authorize('update', $customer);

        $validatedData = $request->validate([
            'no_hp_cust'   => [
                'required',
                'string',
                'max:15',
                Rule::unique('customers', 'no_hp_cust')->ignore($customer->id),
            ],
            'cust_name'    => ['required', 'string', 'max:255'],
            'level_id'     => ['required', 'exists:levels,id'],
            'total_spent'  => ['required', 'integer', 'min:0'],
        ], $this->customMessages, $this->customAttributes);

        $newLevel = Level::findOrFail($validatedData['level_id']);

        $customer->update([
            'no_hp_cust'  => $validatedData['no_hp_cust'],
            'cust_name'   => $validatedData['cust_name'],
            'level_id'    => $validatedData['level_id'],
            'total_spent' => $validatedData['total_spent'],
            'cust_point'  => $newLevel->level_point ?? 0,
        ]);

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
}
