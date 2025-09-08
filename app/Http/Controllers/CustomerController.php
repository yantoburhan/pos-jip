<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Level;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Custom messages untuk validasi
     */
    public array $customMessages = [
        '*.required' => ':Attribute tidak boleh kosong.',
        '*.max' => ':Attribute maksimal :max karakter.',
        '*.unique' => ':Attribute sudah terdaftar.',
        '*.array' => ':Attribute harus berupa array.',
        '*.min' => ':Attribute harus memiliki minimal :min item.',
        '*.distinct' => 'Setiap :attribute harus unik (tidak boleh ada yang sama).',
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
     * Menangani request pencarian real-time (AJAX).
     */
    public function search(Request $request)
    {
        $this->authorize('viewAny', Customer::class);
        $query = Customer::with('level')->orderBy('cust_name', 'asc');
        if ($request->filled('q')) {
            $searchQuery = '%' . $request->q . '%';
            $query->where('cust_name', 'like', $searchQuery)
                  ->orWhere('no_hp_cust', 'like', $searchQuery);
        }
        $customers = $query->limit(50)->get();
        return response()->json($customers);
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
        $customersData = $request->input('customers', []);
        foreach ($customersData as $index => $customer) {
            if (isset($customer['no_hp_cust'])) {
                $phoneNumber = ltrim($customer['no_hp_cust'], '0');
                $customersData[$index]['no_hp_cust'] = '+62' . $phoneNumber;
            }
        }
        $request->merge(['customers' => $customersData]);

        $validatedData = $request->validate([
            'customers'              => ['required', 'array', 'min:1'],
            'customers.*.no_hp_cust' => ['required', 'string', 'max:20', 'distinct', 'unique:customers,no_hp_cust'],
            'customers.*.cust_name'  => ['required', 'string', 'max:255'],
        ], $this->customMessages, [
            'customers.*.no_hp_cust' => 'No HP Customer',
            'customers.*.cust_name'  => 'Nama Customer',
        ]);
        
        $defaultLevel = Level::where('name', 'N/A')->first();
        if (!$defaultLevel) {
            return back()->with('error', 'Level default "N/A" tidak ditemukan. Harap buat level dengan nama "N/A" terlebih dahulu.')->withInput();
        }

        $customersToCreate = [];
        foreach ($validatedData['customers'] as $customerData) {
            $customersToCreate[] = [
                'no_hp_cust'  => $customerData['no_hp_cust'],
                'cust_name'   => $customerData['cust_name'],
                'cust_point'  => 0,
                'total_spent' => 0,
                'level_id'    => $defaultLevel->id,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }
        
        DB::transaction(function () use ($customersToCreate) {
            Customer::insert($customersToCreate);
        });

        return redirect()->route('customers.index')
            ->with('success', count($customersToCreate) . ' customer baru berhasil ditambahkan.');
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
     * INI ADALAH FUNGSI YANG DIPERBARUI
     */
    public function update(Request $request, Customer $customer)
    {
        $this->authorize('update', $customer);

        // Simpan No HP original untuk validasi unique
        $originalNoHp = $customer->no_hp_cust;

        // Format No HP yang baru dari input form
        if ($request->has('no_hp_cust')) {
            $phoneNumber = ltrim($request->input('no_hp_cust'), '0');
            $request->merge(['no_hp_cust' => '+62' . $phoneNumber]);
        }

        // Validasi data yang masuk
        $validatedData = $request->validate([
            'no_hp_cust' => [
                'required',
                'string',
                'max:20',
                // Cek unique, tapi abaikan No HP original dari customer ini
                Rule::unique('customers')->ignore($originalNoHp, 'no_hp_cust'),
            ],
            'cust_name'  => ['required', 'string', 'max:255'],
        ], $this->customMessages, $this->customAttributes);

        // Gunakan DB Transaction untuk memastikan semua operasi berhasil
        DB::transaction(function () use ($customer, $validatedData) {
            // Lakukan update.
            // Karena sudah ada onUpdate('cascade'), semua data di tabel 'transactions'
            // akan otomatis diperbarui oleh database jika no_hp_cust berubah.
            $customer->update($validatedData);

            // Panggil method recalculateStats dari model Customer.
            // Method ini akan menghitung ulang total poin, belanja, dan level
            // berdasarkan No HP yang baru.
            $customer->recalculateStats();
        });

        return redirect()->route('customers.index')
                         ->with('success', 'Data customer berhasil diperbarui.');
    }


    /**
     * Hapus customer
     */
    public function destroy(Customer $customer)
    {
        $this->authorize('delete', $customer);

        // Cek apakah customer memiliki transaksi
        if (Transaction::where('no_hp_cust', $customer->no_hp_cust)->exists()) {
            return redirect()->route('customers.index')
                             ->with('error', 'Gagal menghapus. Customer ini memiliki riwayat transaksi.');
        }

        $customer->delete();
        
        return redirect()->route('customers.index')
                         ->with('success', 'Customer berhasil dihapus.');
    }
}