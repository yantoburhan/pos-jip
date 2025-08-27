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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Opsi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            {{-- Ganti variabel $product menjadi $pending agar tidak membingungkan --}}
                            @forelse ($pendings as $pending)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $pending->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $pending->point }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($pending->price, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $pending->user->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        {{-- LOGIKA BARU: Cek apakah user punya izin 'update' pada produk utama --}}
                                        @can('update', new App\Models\Product())
                                            {{-- Jika BISA, tampilkan tombol Accept & Reject --}}
                                            <form action="{{ route('products.pending.approve', $pending->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded-md text-xs font-semibold hover:bg-green-700">
                                                    Accept
                                                </button>
                                            </form>

                                            <form action="{{ route('products.pending.reject', $pending->id) }}" method="POST" class="inline-block ml-2">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded-md text-xs font-semibold hover:bg-red-700">
                                                    Reject
                                                </button>
                                            </form>
                                        @else
                                            {{-- Jika TIDAK BISA, cek apakah dia adalah pemilik item pending ini --}}
                                            @if ($pending->created_by === auth()->id())
                                                <a href="{{ route('products.pending.edit', $pending->id) }}" class="inline-block px-3 py-1 bg-yellow-500 text-white rounded-md text-xs font-semibold hover:bg-yellow-600">
                                                    Edit
                                                </a>

                                                <form action="{{ route('products.pending.cancel', $pending->id) }}" method="POST" class="inline-block ml-2">
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