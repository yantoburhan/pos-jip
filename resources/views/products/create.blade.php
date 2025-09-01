<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buat Produk Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-lg sm:rounded-xl">
                @can('create', App\Models\Product::class)
                    <form method="POST" action="{{ route('products.store') }}">
                        @csrf
                        <div class="p-6 sm:p-8">

                            @if ($errors->any())
                                <div class="mb-6 p-4 bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400 rounded-lg">
                                    <p class="font-bold">Oops! Ada beberapa kesalahan:</p>
                                    <ul class="list-disc list-inside mt-2 text-sm">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Kontainer untuk baris-baris produk --}}
                            <div id="product-rows-container" class="space-y-6">
                                {{-- Baris Produk Pertama (Template) --}}
                                <div class="product-row grid grid-cols-12 gap-4 items-end">
                                    {{-- Kolom Nama --}}
                                    <div class="col-span-12 sm:col-span-5">
                                        <label for="products[0][name]" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nama Produk</label>
                                        <input id="products[0][name]" class="block mt-1 w-full bg-gray-50 dark:bg-slate-900/50 border-gray-300 dark:border-slate-600 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 rounded-md shadow-sm text-gray-900 dark:text-gray-200" 
                                               type="text" name="products[0][name]" required />
                                    </div>

                                    {{-- Kolom Point --}}
                                    <div class="col-span-12 sm:col-span-5">
                                        <label for="products[0][point]" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Point</label>
                                        <input id="products[0][point]" min="0" step="any" class="block mt-1 w-full bg-gray-50 dark:bg-slate-900/50 border-gray-300 dark:border-slate-600 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 rounded-md shadow-sm text-gray-900 dark:text-gray-200" 
                                               type="number" name="products[0][point]" required />
                                    </div>
                                    
                                    {{-- Kolom Tombol Hapus (kosong di baris pertama) --}}
                                    <div class="col-span-12 sm:col-span-2"></div>
                                </div>
                            </div>

                            {{-- Tombol untuk menambah baris baru --}}
                            <div class="mt-6">
                                <button type="button" id="add-product-row" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Tambah Produk
                                </button>
                            </div>
                        </div>

                        {{-- Footer dengan tombol aksi utama --}}
                        <div class="flex items-center justify-end px-6 py-4 bg-gray-50 dark:bg-slate-800/50 border-t dark:border-slate-700">
                            <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150 mr-4">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                                Simpan
                            </button>
                        </div>
                    </form>
                @else
                    <div class="p-6 sm:p-8">
                        <p class="text-center text-gray-600 dark:text-gray-400">Anda tidak memiliki izin untuk membuat produk.</p>
                    </div>
                @endcan
            </div>
        </div>
    </div>

    {{-- SCRIPT JAVASCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('product-rows-container');
            const addRowButton = document.getElementById('add-product-row');
            let rowIndex = 1; // Mulai dari 1 karena baris ke-0 sudah ada di HTML

            // Fungsi untuk menambah baris baru
            addRowButton.addEventListener('click', () => {
                const newRow = document.createElement('div');
                newRow.classList.add('product-row', 'grid', 'grid-cols-12', 'gap-4', 'items-end');

                newRow.innerHTML = `
                    <div class="col-span-12 sm:col-span-5">
                        <label for="products[${rowIndex}][name]" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nama Produk</label>
                        <input id="products[${rowIndex}][name]" class="block mt-1 w-full bg-gray-50 dark:bg-slate-900/50 border-gray-300 dark:border-slate-600 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 rounded-md shadow-sm text-gray-900 dark:text-gray-200" 
                               type="text" name="products[${rowIndex}][name]" required />
                    </div>

                    <div class="col-span-12 sm:col-span-5">
                        <label for="products[${rowIndex}][point]" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Point</label>
                        <input id="products[${rowIndex}][point]" min="0" step="any" class="block mt-1 w-full bg-gray-50 dark:bg-slate-900/50 border-gray-300 dark:border-slate-600 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 rounded-md shadow-sm text-gray-900 dark:text-gray-200" 
                               type="number" name="products[${rowIndex}][point]" required />
                    </div>
                    
                    <div class="col-span-12 sm:col-span-2">
                        <button type="button" class="remove-product-row inline-flex items-center justify-center w-full px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800">
                            Hapus
                        </button>
                    </div>
                `;

                container.appendChild(newRow);
                rowIndex++; // Siapkan indeks untuk baris berikutnya
            });

            // Fungsi untuk menghapus baris (menggunakan event delegation)
            container.addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('remove-product-row')) {
                    // Temukan elemen .product-row terdekat dan hapus
                    e.target.closest('.product-row').remove();
                }
            });
        });
    </script>
</x-app-layout>