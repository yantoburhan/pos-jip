<x-app-layout>
    {{-- Bagian Header Halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Produk Pending') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Komponen untuk menampilkan pesan sukses/error --}}
            <x-message />

            {{-- Tabel Daftar Produk Pending --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Point</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Opsi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($pendings as $product)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $product->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $product->point }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $product->user->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        {{-- Jika user punya akses "update_products" → tampilkan Accept & Reject --}}
                                        @can('update', App\Models\Product::class)
                                            <form action="{{ route('products.pending.accept', $product->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded-md text-xs font-semibold hover:bg-green-700">
                                                    Accept
                                                </button>
                                            </form>

                                            <form action="{{ route('products.pending.reject', $product->id) }}" method="POST" class="inline-block ml-2">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded-md text-xs font-semibold hover:bg-red-700">
                                                    Reject
                                                </button>
                                            </form>
                                        @else
                                            {{-- Jika bukan admin, tapi user yang buat → Cancel & Edit --}}
                                            @if ($product->user_id === auth()->id())
                                                <a href="{{ route('products.pending.edit', $product->id) }}" class="inline-block px-3 py-1 bg-yellow-500 text-white rounded-md text-xs font-semibold hover:bg-yellow-600">
                                                    Edit
                                                </a>

                                                <form action="{{ route('products.pending.cancel', $product->id) }}" method="POST" class="inline-block ml-2">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 bg-gray-600 text-white rounded-md text-xs font-semibold hover:bg-gray-700">
                                                        Cancel
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        Tidak ada produk pending.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>