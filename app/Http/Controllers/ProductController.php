<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; // <-- TAMBAHAN BARU

class ProductController extends Controller
{
    // Pesan validasi kustom dalam Bahasa Indonesia
    public array $customMessages = [
        '*.required' => ':Attribute tidak boleh kosong.',
        '*.max' => ':Attribute maksimal :max karakter.',
        '*.unique' => ':Attribute ini sudah terdaftar, silakan gunakan yang lain.',
        '*.numeric' => ':Attribute harus berupa angka.',
        '*.min' => ':Attribute minimal :min.',
        '*.array' => ':Attribute harus berupa array.',
    ];

    // Nama atribut yang lebih ramah (akan di-override di store)
    public array $customAttributes = [
        'name' => 'Nama Produk',
        'point' => 'Point',
    ];

    public function index()
    {
        $this->authorize('viewAny', Product::class);
        $products = Product::orderBy('id', 'asc')->paginate(10);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $this->authorize('create', Product::class);
        return view('products.create');
    }

    // --- METHOD STORE DIPERBARUI ---
    public function store(Request $request)
    {
        // Izin untuk membuat produk dicek di sini
        $this->authorize('create', Product::class);

        // Validasi sekarang akan memeriksa sebuah array 'products'
        $validated = $request->validate([
            'products'         => ['required', 'array', 'min:1'], // Pastikan ada minimal 1 produk
            'products.*.name'  => ['required', 'string', 'max:255', 'unique:products,name'],
            'products.*.point' => ['required', 'numeric', 'min:0'],
        ], $this->customMessages, [ // Ganti customAttributes agar sesuai dengan array
            'products.*.name' => 'Nama Produk',
            'products.*.point' => 'Point',
        ]);

        // Gunakan DB Transaction untuk memastikan semua data berhasil disimpan
        // atau tidak sama sekali jika ada error. Ini menjaga integritas data.
        DB::transaction(function () use ($validated) {
            foreach ($validated['products'] as $productData) {
                Product::create([
                    'name' => $productData['name'],
                    'point' => $productData['point'],
                ]);
            }
        });

        return redirect()->route('products.index')
            ->with('success', count($validated['products']) . ' produk baru berhasil ditambahkan.');
    }
    // --- AKHIR PERUBAHAN ---

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