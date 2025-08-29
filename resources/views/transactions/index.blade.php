<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Transaksi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                @can('create', App\Models\Transaction::class)
                    <a href="{{ route('transactions.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        + Buat Transaksi Baru
                    </a>
                @endcan
            </div>

            <x-message />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Transaksi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operator</th>
                                @can('viewOpsi', App\Models\Transaction::class)
                                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Opsi</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->no_transaksi }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}</td>
                                    {{-- PERBAIKAN: Menggunakan cust_name sesuai nama kolom database --}}
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->customer->cust_name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($transaction->total_penjualan, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->operator->name ?? 'N/A' }}</td>
                                    @can('viewOpsi', $transaction)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        {{-- PERBAIKAN: Menambahkan tombol-tombol aksi --}}
                                        @can('view', $transaction)
                                            <a href="{{ route('transactions.show', $transaction->no_transaksi) }}" class="inline-block px-3 py-1 bg-green-500 text-white rounded-md text-xs font-semibold hover:bg-green-600">Detail</a>
                                        @endcan
                                        @can('update', $transaction)
                                            <a href="{{ route('transactions.edit', $transaction->no_transaksi) }}" class="inline-block px-3 py-1 bg-yellow-500 text-white rounded-md text-xs font-semibold hover:bg-yellow-600 ml-1">Ubah</a>
                                        @endcan
                                        @can('delete', $transaction)
                                            <form action="{{ route('transactions.destroy', $transaction->no_transaksi) }}" method="POST" class="inline-block ml-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded-md text-xs font-semibold hover:bg-red-700">Hapus</button>
                                            </form>
                                        @endcan
                                    </td>
                                    @endcan
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        Tidak ada data transaksi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

