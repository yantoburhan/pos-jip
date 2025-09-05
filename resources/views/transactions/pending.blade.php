<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Bagian Header Halaman --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 px-4 sm:px-0">
                <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Transaksi Pending') }}
                </h2>
                <a href="{{ route('transactions.index') }}" class="inline-flex items-center mt-4 sm:mt-0 px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    <span>Kembali ke Transaksi</span>
                </a>
            </div>

            <x-message />

            {{-- Tabel Daftar Transaksi Pending --}}
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-lg sm:rounded-xl">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 dark:bg-slate-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Dibuat Oleh</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Opsi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                            @forelse ($pendingTransactions as $transaction)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ \Carbon\Carbon::parse($transaction->date)->isoFormat('D MMMM YYYY') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $transaction->customer->cust_name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200 text-right">Rp {{ number_format($transaction->total_penjualan, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $transaction->operator->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex items-center justify-center space-x-2">
                                            {{-- OPSI UNTUK ADMIN --}}
                                            @if (Auth::user()->hasFeature('approve_transactions'))
                                                <form action="{{ route('transactions.reject', $transaction->no_transaksi) }}" method="POST" onsubmit="return confirm('Yakin ingin MENOLAK transaksi ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-md text-xs font-semibold hover:bg-red-700">Tolak</button>
                                                </form>
                                                <form action="{{ route('transactions.approve', $transaction->no_transaksi) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="px-3 py-1.5 bg-green-600 text-white rounded-md text-xs font-semibold hover:bg-green-700">Setuju</button>
                                                </form>

                                            {{-- OPSI UNTUK KASIR (HANYA JIKA DIA PEMBUATNYA) --}}
                                            @elseif (Auth::id() === $transaction->operator_id)
                                                <a href="{{ route('transactions.edit', $transaction->no_transaksi) }}" class="inline-block px-3 py-1.5 bg-yellow-500 text-white rounded-md text-xs font-semibold hover:bg-yellow-600">Ubah</a>
                                                <form action="{{ route('transactions.destroy', $transaction->no_transaksi) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-md text-xs font-semibold hover:bg-red-700">Hapus</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                        Tidak ada transaksi yang menunggu persetujuan.
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