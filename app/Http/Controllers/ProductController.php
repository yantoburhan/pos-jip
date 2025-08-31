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
        '*.numeric' => ':Attribute harus berupa angka.',
    ];

    // Nama atribut yang lebih ramah
    public array $customAttributes = [
        'name' => 'Nama Produk',
        'point' => 'Point',
    ];

    public function index()
    {
        $this->authorize('viewAny', Product::class);

        $products = Product::orderBy('id', 'asc')->paginate(10);
        $pendingCount = ProductPending::count();

        return view('products.index', compact('products', 'pendingCount'));
    }

    public function create()
    {
        // Izin untuk membuat akan dicek di method store
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:products,name'],
            'point' => ['required', 'numeric'],
        ], $this->customMessages, $this->customAttributes);

        // 🔑 LOGIKA BARU: Cek apakah user punya izin 'update' pada produk.
        // Ini lebih fleksibel daripada cek 'roles == 1'.
        if (auth()->user()->can('update', new Product())) {
            // Jika BISA (punya izin 'update_products'), langsung masuk ke tabel utama.
            Product::create($validated);

            return redirect()->route('products.index')
                ->with('success', 'Produk baru berhasil ditambahkan.');
        } else {
            // Jika TIDAK BISA, masuk ke tabel pending.
            ProductPending::create([
                'created_by' => auth()->id(),
                'name' => $validated['name'],
                'point' => $validated['point'],
                'description' => 'Menunggu persetujuan admin',
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
            'point' => ['required', 'numeric'],
        ], $this->customMessages, $this->customAttributes);

        $product->update($validatedData);

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