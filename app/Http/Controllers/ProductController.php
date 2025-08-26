<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPending;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    // Pesan validasi kustom dalam Bahasa Indonesia
    public array $customMessages = [
        '*.required' => ':Attribute tidak boleh kosong.',
        '*.max' => ':Attribute maksimal :max karakter.',
        '*.unique' => ':Attribute ini sudah terdaftar, silakan gunakan yang lain.',
        '*.integer' => ':Attribute harus berupa angka.',
    ];

    // Nama atribut yang lebih ramah
    public array $customAttributes = [
        'name' => 'Nama Produk',
        'price' => 'Harga Produk',
        'point' => 'Point',
    ];

    public function index()
    {
        $this->authorize('viewAny', Product::class);

        $products = Product::orderBy('id', 'asc')->paginate(10);

        // Hitung jumlah pending
        $pendingCount = ProductPending::count();

        return view('products.index', compact('products', 'pendingCount'));
    }

    public function create()
    {
        $this->authorize('create', Product::class);

        return view('products.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Product::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:products,name'],
            'price' => ['required', 'integer'],
            'point' => ['required', 'integer'],
        ], $this->customMessages, $this->customAttributes);

        // 🔑 cek role user
        if (auth()->user()->roles == 1) {
            // 1 = admin → langsung masuk ke tabel products
            Product::create([
                'name' => $validated['name'],
                'price' => $validated['price'],
                'point' => $validated['point'],
            ]);

            return redirect()->route('products.index')
                ->with('success', 'Produk baru berhasil ditambahkan.');
        } else {
            // selain admin → masuk ke tabel pending
            ProductPending::create([
                'user_id' => auth()->id(),
                'name' => $validated['name'],
                'price' => $validated['price'],
                'point' => $validated['point'],
            ]);

            return redirect()->route('products.pending.index')
                ->with('success', 'Produk berhasil diajukan dan menunggu persetujuan admin.');
        }
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);

        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('products', 'name')->ignore($product->id)],
            'price' => ['required', 'integer'],
            'point' => ['required', 'integer'],
        ], $this->customMessages, $this->customAttributes);

        $product->update([
            'name' => $validatedData['name'],
            'price' => $validatedData['price'],
            'point' => $validatedData['point'],
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Data produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
