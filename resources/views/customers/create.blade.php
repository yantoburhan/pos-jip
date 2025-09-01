<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Customer Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-lg sm:rounded-xl">
                @can('create', App\Models\Customer::class)
                    <form method="POST" action="{{ route('customers.store') }}">
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

                            <div id="customer-rows-container" class="space-y-6">
                                {{-- Baris Customer Pertama (Template) --}}
                                <div class="customer-row grid grid-cols-12 gap-4 items-end">
                                    
                                    {{-- Kolom No. HP dengan Kode Negara --}}
                                    <div class="col-span-12 sm:col-span-5">
                                        <label for="customers[0][no_hp_cust]" class="block font-medium text-sm text-gray-700 dark:text-gray-300">No. HP Customer</label>
                                        <div class="mt-1 flex rounded-md shadow-sm">
                                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-gray-300">
                                                +62
                                            </span>
                                            {{-- PENYESUAIAN KECIL: Placeholder diubah agar lebih jelas --}}
                                            <input id="customers[0][no_hp_cust]" class="block w-full flex-1 rounded-none rounded-r-md bg-gray-50 dark:bg-slate-900/50 border-gray-300 dark:border-slate-600 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 text-gray-900 dark:text-gray-200" 
                                                   type="text" name="customers[0][no_hp_cust]" value="{{ old('customers.0.no_hp_cust', $no_hp_cust ?? '') }}" placeholder="8123456789" required />
                                        </div>
                                    </div>

                                    {{-- Kolom Nama Customer --}}
                                    <div class="col-span-12 sm:col-span-5">
                                        <label for="customers[0][cust_name]" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nama Customer</label>
                                        <input id="customers[0][cust_name]" class="block mt-1 w-full bg-gray-50 dark:bg-slate-900/50 border-gray-300 dark:border-slate-600 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 rounded-md shadow-sm text-gray-900 dark:text-gray-200" 
                                               type="text" name="customers[0][cust_name]" value="{{ old('customers.0.cust_name') }}" required />
                                    </div>
                                    
                                    <div class="col-span-12 sm:col-span-2"></div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="button" id="add-customer-row" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Tambah Customer
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-end px-6 py-4 bg-gray-50 dark:bg-slate-800/50 border-t dark:border-slate-700">
                            <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150 mr-4">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                                Simpan
                            </button>
                        </div>
                    </form>
                @else
                    <div class="p-6 sm:p-8">
                        <p class="text-center text-gray-600 dark:text-gray-400">Anda tidak memiliki izin untuk menambah customer.</p>
                    </div>
                @endcan
            </div>
        </div>
    </div>

    {{-- SCRIPT JAVASCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('customer-rows-container');
            const addRowButton = document.getElementById('add-customer-row');
            let rowIndex = 1;

            addRowButton.addEventListener('click', () => {
                const newRow = document.createElement('div');
                newRow.classList.add('customer-row', 'grid', 'grid-cols-12', 'gap-4', 'items-end');

                newRow.innerHTML = `
                    <div class="col-span-12 sm:col-span-5">
                        <label for="customers[${rowIndex}][no_hp_cust]" class="block font-medium text-sm text-gray-700 dark:text-gray-300">No. HP Customer</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-gray-300">
                                +62
                            </span>
                             {{-- PENYESUAIAN KECIL: Placeholder diubah agar lebih jelas --}}
                            <input id="customers[${rowIndex}][no_hp_cust]" class="block w-full flex-1 rounded-none rounded-r-md bg-gray-50 dark:bg-slate-900/50 border-gray-300 dark:border-slate-600 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 text-gray-900 dark:text-gray-200" 
                                   type="text" name="customers[${rowIndex}][no_hp_cust]" placeholder="8123456789" required />
                        </div>
                    </div>

                    <div class="col-span-12 sm:col-span-5">
                        <label for="customers[${rowIndex}][cust_name]" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nama Customer</label>
                        <input id="customers[${rowIndex}][cust_name]" class="block mt-1 w-full bg-gray-50 dark:bg-slate-900/50 border-gray-300 dark:border-slate-600 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 rounded-md shadow-sm text-gray-900 dark:text-gray-200" 
                               type="text" name="customers[${rowIndex}][cust_name]" required />
                    </div>
                    
                    <div class="col-span-12 sm:col-span-2">
                        <button type="button" class="remove-customer-row inline-flex items-center justify-center w-full px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800">
                            Hapus
                        </button>
                    </div>
                `;

                container.appendChild(newRow);
                rowIndex++;
            });

            container.addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('remove-customer-row')) {
                    e.target.closest('.customer-row').remove();
                }
            });
        });
    </script>
</x-app-layout>