<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    // Properti untuk menyimpan pesan validasi kustom dalam Bahasa Indonesia
    public array $customMessages = [
        '*.required' => ':Attribute tidak boleh kosong.',
        '*.max' => ':Attribute maksimal :max karakter.',
        '*.unique' => ':Attribute ini sudah terdaftar, silakan gunakan yang lain.',
        '*.integer' => ':Attribute harus berupa angka.',
    ];

    // Properti untuk mengubah nama atribut default menjadi lebih ramah
    public array $customAttributes = [
        'name' => 'Nama Produk',
        'point' => 'Point',
    ];

    /**
     * Menampilkan daftar semua produk.
     */
    public function index()
    {
        $this->authorize('viewAny', Product::class);
        $products = Product::orderBy('id', 'asc')->paginate(10);
        return view('products.index', compact('products'));
    }

    /**
     * Menampilkan form untuk membuat produk baru.
     */
    public function create()
    {
        $this->authorize('create', Product::class);
        return view('products.create');
    }

    /**
     * Menyimpan produk baru ke database.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Product::class);

        // Validasi disesuaikan dengan kolom 'name' di database
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:products,name'],
            'point' => ['required', 'integer'],
        ], $this->customMessages, $this->customAttributes);

        // Simpan data menggunakan 'name' agar cocok dengan $fillable dan database
        Product::create([
            'name' => $request->name,
            'point' => $request->point,
        ]);

        return redirect()->route('products.index')
                         ->with('success', 'Produk baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit produk.
     */
    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        return view('products.edit', compact('product'));
    }

    /**
     * Memperbarui data produk di database.
     */
    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        // Validasi disesuaikan dengan kolom 'name' di database
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('products', 'name')->ignore($product->id)],
            'point' => ['required', 'integer'],
        ], $this->customMessages, $this->customAttributes);

        // Update model menggunakan 'name' agar cocok dengan $fillable dan database
        $product->update([
            'name' => $validatedData['name'],
            'point' => $validatedData['point'],
        ]);

        return redirect()->route('products.index')
                        ->with('success', 'Data produk berhasil diperbarui.');
    }

    /**
     * Menghapus produk dari database.
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        $product->delete();
        return redirect()->route('products.index')
                        ->with('success', 'Produk berhasil dihapus.');
    }
}