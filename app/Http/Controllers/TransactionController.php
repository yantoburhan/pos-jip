<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    // ... (customMessages dan customAttributes tetap sama) ...
    public array $customMessages = [
        'required' => ':Attribute wajib diisi.', 'exists' => ':Attribute yang dipilih tidak valid atau tidak ditemukan.', 'array' => ':Attribute harus berupa array.', 'min' => ['array' => ':Attribute harus memiliki setidaknya :min item.',], 'integer' => ':Attribute harus berupa angka.',
    ];
    public array $customAttributes = [
        'no_hp_cust' => 'Customer', 'date' => 'Tanggal', 'items' => 'Produk', 'items.*.id_product' => 'Produk pada baris', 'items.*.quantity' => 'Jumlah produk', 'items.*.price' => 'Harga produk',
    ];

    public function index()
    {
        $this->authorize('viewAny', Transaction::class);
        $transactions = Transaction::with('customer', 'operator')->latest()->paginate(10);
        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $this->authorize('create', Transaction::class);
        return view('transactions.create');
    }

    public function store(Request $request)
    {
        // ... (Logika store tetap sama) ...
        $this->authorize('create', Transaction::class);

        $validatedData = $request->validate([
            'no_hp_cust' => 'required|exists:customers,no_hp_cust',
            'date' => 'required|date',
            'alamat' => 'nullable|string',
            'wilayah' => 'required|in:medan,luar_medan,tidak_diketahui',
            'kecamatan' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.id_product' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|integer|min:0',
        ], $this->customMessages, $this->customAttributes);

        $total_penjualan = 0;
        $total_poin = 0;
        foreach ($validatedData['items'] as $itemData) {
            $product = Product::find($itemData['id_product']);
            $total_penjualan += $itemData['quantity'] * $itemData['price'];
            $total_poin += $itemData['quantity'] * $product->point;
        }

        try {
            DB::beginTransaction();
            $transaction = Transaction::create([
                'no_transaksi' => 'TRX-' . time(), 'date' => $validatedData['date'], 'no_hp_cust' => $validatedData['no_hp_cust'], 'alamat' => $validatedData['alamat'], 'wilayah' => $validatedData['wilayah'], 'kecamatan' => $validatedData['kecamatan'], 'operator_id' => Auth::id(), 'total_penjualan' => $total_penjualan, 'total_poin' => $total_poin,
            ]);

            foreach ($validatedData['items'] as $itemData) {
                TransactionItem::create([
                    'no_transaksi' => $transaction->no_transaksi, 'id_product' => $itemData['id_product'], 'quantity' => $itemData['quantity'], 'price' => $itemData['price'], 'point_per_item' => Product::find($itemData['id_product'])->point, 'total_price' => $itemData['quantity'] * $itemData['price'],
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dibuat.');
    }

    /**
     * BARU: Menampilkan detail transaksi.
     */
    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        // Eager load relasi untuk efisiensi
        $transaction->load('customer', 'operator', 'items.product');
        return view('transactions.show', compact('transaction'));
    }

    /**
     * BARU: Menampilkan form untuk edit transaksi.
     */
    public function edit(Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        $transaction->load('items');
        return view('transactions.edit', compact('transaction'));
    }

    /**
     * BARU: Mengupdate data transaksi di database.
     */
    public function update(Request $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        $validatedData = $request->validate([
            'no_hp_cust' => 'required|exists:customers,no_hp_cust',
            'date' => 'required|date',
            'alamat' => 'nullable|string',
            'wilayah' => 'required|in:medan,luar_medan,tidak_diketahui',
            'kecamatan' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.id_product' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|integer|min:0',
        ], $this->customMessages, $this->customAttributes);
        
        $total_penjualan = 0;
        $total_poin = 0;
        foreach ($validatedData['items'] as $itemData) {
            $product = Product::find($itemData['id_product']);
            $total_penjualan += $itemData['quantity'] * $itemData['price'];
            $total_poin += $itemData['quantity'] * $product->point;
        }

        try {
            DB::beginTransaction();

            $transaction->update([
                'date' => $validatedData['date'],
                'no_hp_cust' => $validatedData['no_hp_cust'],
                'alamat' => $validatedData['alamat'],
                'wilayah' => $validatedData['wilayah'],
                'kecamatan' => $validatedData['kecamatan'],
                'operator_id' => Auth::id(), // Operator yang mengedit
                'total_penjualan' => $total_penjualan,
                'total_poin' => $total_poin,
            ]);

            // Hapus item lama dan buat yang baru
            $transaction->items()->delete();
            foreach ($validatedData['items'] as $itemData) {
                TransactionItem::create([
                    'no_transaksi' => $transaction->no_transaksi,
                    'id_product' => $itemData['id_product'],
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                    'point_per_item' => Product::find($itemData['id_product'])->point,
                    'total_price' => $itemData['quantity'] * $itemData['price'],
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengupdate transaksi: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * BARU: Menghapus transaksi dari database.
     */
    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);
        try {
            // Items akan terhapus otomatis karena foreign key cascade
            $transaction->delete();
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    // ... (Fungsi searchCustomers dan searchProducts tetap sama) ...
    public function searchCustomers(Request $request) {
        $query = $request->get('q');
        $customers = Customer::where('cust_name', 'LIKE', "%{$query}%")->orWhere('no_hp_cust', 'LIKE', "%{$query}%")->take(10)->get();
        return response()->json($customers);
    }
    public function searchProducts(Request $request) {
        $query = $request->get('q');
        $products = Product::where('name', 'LIKE', "%{$query}%")->take(10)->get(['id', 'name', 'point']);
        return response()->json($products);
    }
}

