<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public array $customMessages = [
        'required' => ':Attribute wajib diisi.',
        'exists' => ':Attribute yang dipilih tidak valid atau tidak ditemukan.',
        'array' => ':Attribute harus berupa array.',
        'min' => [
            'array' => ':Attribute harus memiliki setidaknya :min item.',
        ],
        'numeric' => ':Attribute harus berupa angka.',
    ];
    public array $customAttributes = [
        'no_hp_cust' => 'Customer',
        'date' => 'Tanggal',
        'items' => 'Produk',
        'items.*.id_product' => 'Produk pada baris',
        'items.*.quantity' => 'Jumlah produk',
        'items.*.price' => 'Harga produk',
    ];

    /**
     * Menampilkan daftar transaksi dengan paginasi dinamis.
     */
    // app/Http/Controllers/TransactionController.php

    public function index(Request $request)
    {
        $this->authorize('viewAny', Transaction::class);

        $perPage = $request->input('per_page', 10);
        $searchQuery = $request->input('q'); // DIUBAH: Menggunakan 'q' agar konsisten dengan Customer

        // 1. Siapkan query dasar dengan semua kondisi yang dibutuhkan
        $query = Transaction::with('customer', 'operator')
                            ->where('status', 'approved') // Kondisi status digabungkan di sini
                            ->latest(); // Mengurutkan berdasarkan tanggal/waktu pembuatan

        // 2. Terapkan filter pencarian jika ada input 'q'
        if ($searchQuery) {
            $query->where(function($q) use ($searchQuery) {
                $q->where('no_transaksi', 'like', "%{$searchQuery}%")
                ->orWhereHas('customer', function($subQ) use ($searchQuery) {
                    $subQ->where('cust_name', 'like', "%{$searchQuery}%");
                });
            });
        }

        // 3. Lakukan pagination (logika 'all' tetap dipertahankan)
        if ($perPage == 'all') {
            $total = $query->count();
            $perPage = $total > 0 ? $total : 10;
        }

        $transactions = $query->paginate((int)$perPage)->withQueryString();

        // Ambil jumlah pending untuk notifikasi
        $pendingCount = Transaction::where('status', 'pending')->count();

        return view('transactions.index', compact('transactions', 'pendingCount'));
    }

    /**
     * Menampilkan form untuk membuat transaksi baru.
     */
    public function create()
    {
        $this->authorize('create', Transaction::class);
        return view('transactions.create');
    }

    /**
     * Menyimpan transaksi baru ke database.
     */
    public function store(Request $request)
    {
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
            'items.*.price' => 'required|numeric|min:0',
        ], $this->customMessages, $this->customAttributes);

        try {
            DB::beginTransaction();

            $total_penjualan = 0;
            $total_poin = 0;
            foreach ($validatedData['items'] as $itemData) {
                $product = Product::find($itemData['id_product']);
                $total_penjualan += $itemData['quantity'] * $itemData['price'];
                $total_poin += $itemData['quantity'] * $product->point;
            }

            $user = Auth::user();
            $status = $user->hasFeature('approve_transactions') ? 'approved' : 'pending';

            $prefix = 'JI';
            $datePart = Carbon::parse($validatedData['date'])->format('Ymd');
            $operatorInitial = strtoupper(substr($user->name, 0, 1));
            $todayCount = Transaction::whereDate('date', $validatedData['date'])->count();
            $sequenceNumber = str_pad($todayCount + 1, 3, '0', STR_PAD_LEFT);
            $generatedNoTransaksi = "{$prefix}-{$datePart}-{$operatorInitial}-{$sequenceNumber}";

            $transaction = Transaction::create([
                'no_transaksi' => $generatedNoTransaksi,
                'date' => $validatedData['date'],
                'no_hp_cust' => $validatedData['no_hp_cust'],
                'alamat' => $validatedData['alamat'],
                'wilayah' => $validatedData['wilayah'],
                'kecamatan' => $validatedData['kecamatan'],
                'operator_id' => $user->id,
                'total_penjualan' => $total_penjualan,
                'total_poin' => $total_poin,
                'status' => $status,
            ]);

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

            $customer = Customer::find($validatedData['no_hp_cust']);
            if ($customer) {
                $customer->recalculateStats();
            }

            $message = ($status === 'approved')
                ? 'Transaksi berhasil dibuat.'
                : 'Transaksi berhasil dibuat dan menunggu persetujuan admin.';

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('transactions.index')->with('success', $message);
    }

    /**
     * Menampilkan detail satu transaksi.
     */
    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        $transaction->load('customer', 'operator', 'items.product');
        return view('transactions.show', compact('transaction'));
    }

    /**
     * Menampilkan form untuk mengedit transaksi.
     */
    public function edit(Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        $transaction->load('items');
        return view('transactions.edit', compact('transaction'));
    }

    /**
     * Memperbarui data transaksi di database.
     */
    public function update(Request $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        $oldCustomerNoHp = $transaction->no_hp_cust;

        $validatedData = $request->validate([
            'no_hp_cust' => 'required|exists:customers,no_hp_cust',
            'date' => 'required|date',
            'alamat' => 'nullable|string',
            'wilayah' => 'required|in:medan,luar_medan,tidak_diketahui',
            'kecamatan' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.id_product' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ], $this->customMessages, $this->customAttributes);

        try {
            DB::beginTransaction();

            $total_penjualan = 0;
            $total_poin = 0;
            foreach ($validatedData['items'] as $itemData) {
                $product = Product::find($itemData['id_product']);
                $total_penjualan += $itemData['quantity'] * $itemData['price'];
                $total_poin += $itemData['quantity'] * $product->point;
            }

            $transaction->update([
                'date' => $validatedData['date'], 'no_hp_cust' => $validatedData['no_hp_cust'], 'alamat' => $validatedData['alamat'], 'wilayah' => $validatedData['wilayah'], 'kecamatan' => $validatedData['kecamatan'], 'operator_id' => Auth::id(), 'total_penjualan' => $total_penjualan, 'total_poin' => $total_poin,
            ]);

            $transaction->items()->delete();
            foreach ($validatedData['items'] as $itemData) {
                TransactionItem::create([
                    'no_transaksi' => $transaction->no_transaksi, 'id_product' => $itemData['id_product'], 'quantity' => $itemData['quantity'], 'price' => $itemData['price'], 'point_per_item' => Product::find($itemData['id_product'])->point, 'total_price' => $itemData['quantity'] * $itemData['price'],
                ]);
            }

            $newCustomer = Customer::find($validatedData['no_hp_cust']);
            if ($newCustomer) {
                $newCustomer->recalculateStats();
            }

            if ($oldCustomerNoHp !== $validatedData['no_hp_cust']) {
                $oldCustomer = Customer::find($oldCustomerNoHp);
                if ($oldCustomer) {
                    $oldCustomer->recalculateStats();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengupdate transaksi: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Menghapus transaksi dari database.
     */
    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);
        try {
            DB::beginTransaction();
            $customerNoHp = $transaction->no_hp_cust;
            $transaction->items()->delete();
            $transaction->delete();
            $customer = Customer::find($customerNoHp);
            if ($customer) {
                $customer->recalculateStats();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    /**
     * Mencari customer untuk form transaksi.
     */
    public function searchCustomers(Request $request) {
        $query = $request->get('q');
        $customers = Customer::where('cust_name', 'LIKE', "%{$query}%")->orWhere('no_hp_cust', 'LIKE', "%{$query}%")->take(10)->get();
        return response()->json($customers);
    }

    /**
     * Mencari produk untuk form transaksi.
     */
    public function searchProducts(Request $request) {
        $query = $request->get('q');
        $products = Product::where('name', 'LIKE', "%{$query}%")->take(10)->get(['id', 'name', 'point']);
        return response()->json($products);
    }

    /**
     * Menampilkan daftar transaksi yang masih pending.
     */
    public function pending(Request $request)
    {
        $this->authorize('hasFeature', 'view_pending_transactions');

        $pendingTransactions = Transaction::with('customer', 'operator')
                                            ->where('status', 'pending')
                                            ->latest('date')
                                            ->get();

        return view('transactions.pending', compact('pendingTransactions'));
    }

    /**
     * Menyetujui transaksi yang pending.
     */
    public function approve(Transaction $transaction)
    {
        $this->authorize('hasFeature', 'approve_transactions');

        $transaction->update(['status' => 'approved']);

        if ($transaction->customer) {
            $transaction->customer->recalculateStats();
        }

        return redirect()->route('transactions.pending')->with('success', 'Transaksi berhasil disetujui.');
    }

    /**
     * Menolak (menghapus) transaksi yang pending.
     */
    public function reject(Transaction $transaction)
    {
        $this->authorize('hasFeature', 'approve_transactions');

        try {
            DB::beginTransaction();
            $customerNoHp = $transaction->no_hp_cust;

            $transaction->items()->delete();
            $transaction->delete();

            $customer = Customer::find($customerNoHp);
            if ($customer) {
                $customer->recalculateStats();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menolak transaksi: ' . $e->getMessage());
        }

        return redirect()->route('transactions.pending')->with('success', 'Transaksi berhasil ditolak dan dihapus.');
    }
}
