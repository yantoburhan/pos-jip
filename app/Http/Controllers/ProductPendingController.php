<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPending;
use Illuminate\Http\Request;

class ProductPendingController extends Controller
{
    public function index()
    {
        $pendings = ProductPending::with('user')->get();
        return view('products.pending.index', compact('pendings'));
    }

    // Untuk admin: approve
    public function approve(ProductPending $pending)
    {
        Product::create([
            'name' => $pending->name,
            'price' => $pending->price,
            'point' => $pending->point,
        ]);

        $pending->delete();

        return back()->with('success', 'Produk berhasil di-approve');
    }

    // Untuk admin: reject
    public function reject(ProductPending $pending)
    {
        $pending->delete();
        return back()->with('success', 'Produk ditolak');
    }

    // Untuk user: cancel
    public function cancel(ProductPending $pending)
    {
        if ($pending->created_by !== auth()->id()) {
            abort(403);
        }
        $pending->delete();
        return back()->with('success', 'Produk pending dibatalkan');
    }

    // Untuk user: edit pending
    public function edit(ProductPending $pending)
    {
        if ($pending->created_by !== auth()->id()) {
            abort(403);
        }
        return view('products.pending.edit', compact('pending'));
    }

    public function update(Request $request, ProductPending $pending)
    {
        if ($pending->created_by !== auth()->id()) {
            abort(403);
        }
        $pending->update($request->only(['name','price','point','description']));
        return redirect()->route('products.pending.index')->with('success', 'Produk pending diperbarui');
    }
}
