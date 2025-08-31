<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Transaksi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <!-- ================================================== -->
            <!-- 1. TAMPILAN DI LAYAR (DESAIN BARU)              -->
            <!-- ================================================== -->
            <div id="on-screen-view">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 sm:p-8 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800">{{ $transaction->no_transaksi }}</h3>
                                <p class="text-sm text-gray-500">Tanggal: {{ \Carbon\Carbon::parse($transaction->date)->isoFormat('dddd, D MMMM Y') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-block bg-green-100 text-green-800 text-sm font-semibold px-3 py-1 rounded-full">Selesai</span>
                            </div>
                        </div>

                        <div class="mt-6 border-t border-gray-200 pt-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                            <div>
                                <p class="font-semibold text-gray-600">Customer:</p>
                                <p>{{ $transaction->customer->cust_name ?? 'N/A' }} ({{ $transaction->no_hp_cust }})</p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-600">Alamat:</p>
                                <p>{{ $transaction->alamat ?: '-' }}</p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-600">Operator:</p>
                                <p>{{ $transaction->operator->name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <!-- Rincian Produk -->
                        <div class="mt-8 flow-root">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Rincian Produk</h4>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($transaction->items as $item)
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap">{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center">{{ $item->quantity }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-right font-medium">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Total Keseluruhan -->
                        <div class="mt-6 pt-6 border-t border-gray-200 flex justify-end">
                            <div class="w-full max-w-sm space-y-3">
                                <div class="flex justify-between text-lg">
                                    <span class="text-gray-700 font-semibold">Total Penjualan:</span>
                                    <span class="font-bold text-gray-900">Rp {{ number_format($transaction->total_penjualan, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-lg">
                                    <span class="text-gray-700 font-semibold">Poin Didapat:</span>
                                    <span class="font-bold text-indigo-600">+{{ number_format($transaction->total_poin, 0, ',', '.') }} Poin</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex items-center justify-end mt-6">
                    <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Kembali</a>
                    @can('update', $transaction)
                    <a href="{{ route('transactions.edit', $transaction->no_transaksi) }}" class="ml-4 px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">Ubah</a>
                    @endcan
                    <button onclick="printReceipt()" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Cetak Struk</button>
                </div>
            </div>

        </div>
    </div>

    <!-- ================================================== -->
    <!-- 2. TAMPILAN CETAK (STRUK KASIR) - TERSEMBUNYI      -->
    <!-- ================================================== -->
    <div id="print-receipt-area" style="display: none;">
        <div style="width: 302px; font-family: 'Courier New', Courier, monospace; font-size: 12px; color: black;">
            <div style="text-align: center;">
                <h2 style="font-size: 16px; font-weight: bold; margin-bottom: 4px; margin-top: 0;">Jaya Intero</h2>
                <p style="margin: 0;">Jl. A.H. Nasution No.25 Medan (Sp.Pos)</p>
                <p style="margin: 0;">Telp: 0821-6863-9543</p>
            </div>
            <div style="border-top: 1px dashed black; margin: 8px 0;"></div>
            <table style="width: 100%; font-size: 12px;">
                <tr><td style="width: 50px;">No</td><td>: {{ $transaction->no_transaksi }}</td></tr>
                <tr><td>Tgl</td><td>: {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y H:i') }}</td></tr>
                <tr><td>Kasir</td><td>: {{ $transaction->operator->name ?? 'N/A' }}</td></tr>
                <tr><td>Cust</td><td>: {{ $transaction->customer->cust_name ?? 'N/A' }}</td></tr>
            </table>
            <div style="border-top: 1px dashed black; margin: 8px 0;"></div>
            
            @foreach($transaction->items as $item)
            <div style="margin-bottom: 4px;">
                <p style="margin: 0;">{{ $item->product->name ?? 'Produk Dihapus' }}</p>
                <table style="width: 100%;">
                    <tr>
                        <td style="text-align: left;">{{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td style="text-align: right;">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
            @endforeach
            
            <div style="border-top: 1px dashed black; margin: 8px 0;"></div>
            
            <table style="width: 100%; font-weight: bold;">
                <tr>
                    <td style="text-align: left;">TOTAL</td>
                    <td style="text-align: right;">Rp {{ number_format($transaction->total_penjualan, 0, ',', '.') }}</td>
                </tr>
                 <tr>
                    <td style="text-align: left;">POIN DIDAPAT</td>
                    <td style="text-align: right;">+{{ number_format($transaction->total_poin, 0, ',', '.') }}</td>
                </tr>
            </table>

            <div style="border-top: 1px dashed black; margin: 8px 0;"></div>
            <div style="text-align: center; margin-top: 16px;">
                <p style="margin: 0;">Terima Kasih!</p>
                <p style="margin: 0;">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.</p>
            </div>
        </div>
    </div>

    <script>
        function printReceipt() {
            // Simpan konten asli halaman
            const originalContents = document.body.innerHTML;
            // Ambil konten struk yang tersembunyi
            const printContents = document.getElementById('print-receipt-area').innerHTML;

            // Ganti isi body dengan konten struk
            document.body.innerHTML = printContents;

            // Panggil fungsi cetak browser
            window.print();

            // Kembalikan konten asli halaman setelah mencetak
            document.body.innerHTML = originalContents;

            // Muat ulang halaman untuk memastikan semua skrip berjalan normal kembali
            location.reload();
        }
    </script>
</x-app-layout>