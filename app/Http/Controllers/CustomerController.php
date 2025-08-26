<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Customer::class);
        $customers = Customer::with('level')->orderBy('cust_name', 'asc')->paginate(10);
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        $this->authorize('create', Customer::class);
        $levels = Level::all(); // Ambil semua level untuk dropdown
        return view('customers.create', compact('levels'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Customer::class);

        // 1. TAMBAHKAN 'total_spent' ke dalam aturan validasi
        $validatedData = $request->validate([
            'no_hp_cust' => ['required', 'string', 'max:15', 'unique:customers,no_hp_cust'],
            'cust_name' => ['required', 'string', 'max:255'],
            'level_id' => ['required', 'exists:levels,id'],
            'total_spent' => ['required', 'integer', 'min:0'], // <-- Baris ini penting
        ]);

        // 2. Cari level yang dipilih
        $level = Level::find($validatedData['level_id']);

        // 3. Simpan customer baru dengan data dari form
        Customer::create([
            'no_hp_cust' => $validatedData['no_hp_cust'],
            'cust_name' => $validatedData['cust_name'],
            'level_id' => $validatedData['level_id'],
            'cust_point' => $level->level_point,
            'total_spent' => $validatedData['total_spent'], // <-- Ambil dari form, BUKAN 0 lagi
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer baru berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        $this->authorize('update', $customer);
        $levels = Level::all(); // Ambil semua level untuk dropdown
        return view('customers.edit', compact('customer', 'levels'));
    }

    public function update(Request $request, Customer $customer)
    {
        $this->authorize('update', $customer);

        // 1. Validasi data dari form seperti biasa
        $validatedData = $request->validate([
            'no_hp_cust' => ['required', 'string', 'max:15', Rule::unique('customers', 'no_hp_cust')->ignore($customer->no_hp_cust, 'no_hp_cust')],
            'cust_name' => ['required', 'string', 'max:255'],
            'level_id' => ['required', 'exists:levels,id'],
            'total_spent' => ['required', 'integer', 'min:0'],
        ]);

        // 2. Cari level BARU yang dipilih di database
        $newLevel = Level::find($validatedData['level_id']);

        // 3. Perbarui customer dengan data dari form DAN poin dari level baru
        $customer->update([
            'no_hp_cust' => $validatedData['no_hp_cust'],
            'cust_name' => $validatedData['cust_name'],
            'level_id' => $validatedData['level_id'],
            'total_spent' => $validatedData['total_spent'],
            'cust_point' => $newLevel->level_point, // <-- Poin diperbarui dari level yang baru
        ]);

        return redirect()->route('customers.index')->with('success', 'Data customer berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $this->authorize('delete', $customer);
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer berhasil dihapus.');
    }
}