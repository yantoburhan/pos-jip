<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Bagian Header Halaman & Tombol Aksi --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 px-4 sm:px-0">
                <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Manajemen Transaksi') }}
                </h2>

                <div class="flex items-center space-x-3 mt-4 sm:mt-0 w-full sm:w-auto">
                    {{-- Form Pencarian --}}
                    <form method="GET" action="{{ route('transactions.index') }}">
                        <div class="w-full sm:w-64">
                            <label for="search-input" class="sr-only">Cari</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                                </div>
                                <input type="text" id="search-input"
                                name="q"
                                value="{{ request('q') }}"
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white dark:bg-slate-900/50 dark:border-slate-600 dark:text-gray-200 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Ketik untuk mencari...">
                            </div>
                        </div>
                    </form>
                    @can('hasFeature', 'view_pending_transactions')
                    <a href="{{ route('transactions.pending') }}" class="relative inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                        <span>Pending</span>
                        @if(Auth::user()->hasFeature('approve_transactions') && isset($pendingCount) && $pendingCount > 0)
                            <span class="absolute -top-2 -right-2 flex h-5 w-5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-5 w-5 bg-red-500 text-white text-xs items-center justify-center">
                                    {{ $pendingCount }}
                                </span>
                            </span>
                        @endif
                    </a>
                    @endcan

                    @can('create', App\Models\Transaction::class)
                        <a href="{{ route('transactions.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" /></svg>
                            <span>Buat Transaksi</span>
                        </a>
                    @endcan
                </div>
            </div>

            <x-message />

            {{-- Tabel Daftar Transaksi --}}
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-lg sm:rounded-xl">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 dark:bg-slate-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No. Transaksi</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Operator</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Opsi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                            @forelse ($transactions as $transaction)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-700 dark:text-gray-300">{{ $transaction->no_transaksi }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ \Carbon\Carbon::parse($transaction->date)->isoFormat('D MMMM YYYY') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $transaction->customer->cust_name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200 text-right">Rp {{ number_format($transaction->total_penjualan, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $transaction->operator->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex items-center justify-center space-x-2">
                                            @can('view', $transaction)
                                                <a href="{{ route('transactions.show', $transaction->no_transaksi) }}" class="inline-block px-3 py-1.5 bg-sky-500 text-white rounded-md text-xs font-semibold hover:bg-sky-600">Detail</a>
                                            @endcan
                                            @can('update', $transaction)
                                                <a href="{{ route('transactions.edit', $transaction->no_transaksi) }}" class="inline-block px-3 py-1.5 bg-yellow-500 text-white rounded-md text-xs font-semibold hover:bg-yellow-600">Ubah</a>
                                            @endcan
                                            @can('delete', $transaction)
                                                <form action="{{ route('transactions.destroy', $transaction->no_transaksi) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-md text-xs font-semibold hover:bg-red-700">Hapus</button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                        Tidak ada data transaksi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Bagian Navigasi Halaman --}}
            <div class="mt-6 flex flex-col sm:flex-row justify-between items-center px-4 sm:px-0 space-y-4 sm:space-y-0">
                {{-- Dropdown untuk "Show Entries" --}}
                <div class="flex items-center space-x-2 text-sm text-gray-700 dark:text-gray-400">
                    <span>Tampilkan</span>
                    <select id="per-page-select" class="w-24 block text-sm pr-8 py-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md dark:bg-slate-700 dark:border-slate-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-600 dark:focus:border-indigo-600">
                        <option value="10" @if(request('per_page', 10) == 10) selected @endif>10</option>
                        <option value="50" @if(request('per_page') == 50) selected @endif>50</option>
                        <option value="100" @if(request('per_page') == 100) selected @endif>100</option>
                        <option value="all" @if(request('per_page') == 'all') selected @endif>Semua</option>
                    </select>
                     <span>entri</span>
                </div>

                {{-- Indikator Halaman & Link Paginasi --}}
                @if ($transactions->hasPages() && request('per_page') != 'all')
                    <div class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-4">
                        <div class="text-sm text-gray-700 dark:text-gray-400">
                            Menampilkan
                            <span class="font-medium">{{ $transactions->firstItem() }}</span>
                            sampai
                            <span class="font-medium">{{ $transactions->lastItem() }}</span>
                            dari
                            <span class="font-medium">{{ $transactions->total() }}</span>
                            hasil
                        </div>

                        <div class="w-full sm:w-auto">
                            {{ $transactions->appends(request()->query())->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-sm text-gray-700 dark:text-gray-400">
                        Menampilkan semua
                        <span class="font-medium">{{ $transactions->count() }}</span>
                        hasil
                    </div>
                @endif
            </div>

        </div>
    </div>

    <script>
        document.getElementById('per-page-select').addEventListener('change', function() {
            let url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            url.searchParams.delete('page'); // Kembali ke halaman 1 saat mengubah jumlah data
            window.location.href = url.toString();
        });
    </script>
</x-app-layout>

