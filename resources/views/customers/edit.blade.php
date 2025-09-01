<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Ubah Data Customer') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-lg sm:rounded-xl">
                <form method="POST" action="{{ route('customers.update', $customer->no_hp_cust) }}">
                    @csrf
                    @method('PUT')
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

                        {{-- Bagian Input yang Bisa Diedit --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">No. HP Customer</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-gray-300">
                                        +62
                                    </span>
                                    {{-- Menghilangkan '+62' dari nomor yang ditampilkan agar sesuai format input --}}
                                    @php
                                        $phoneNumber = str_replace('+62', '', $customer->no_hp_cust);
                                    @endphp
                                    <input class="block w-full flex-1 rounded-none rounded-r-md bg-gray-50 dark:bg-slate-900/50 border-gray-300 dark:border-slate-600 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 text-gray-900 dark:text-gray-200" 
                                           type="text" name="no_hp_cust" value="{{ old('no_hp_cust', $phoneNumber) }}" placeholder="8123456789" required />
                                </div>
                            </div>
                            <div>
                                <label for="cust_name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nama Customer</label>
                                <input id="cust_name" class="block mt-1 w-full bg-gray-50 dark:bg-slate-900/50 border-gray-300 dark:border-slate-600 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 rounded-md shadow-sm text-gray-900 dark:text-gray-200" 
                                       type="text" name="cust_name" value="{{ old('cust_name', $customer->cust_name) }}" required />
                            </div>
                        </div>

                        {{-- Data Statistik (Read-only) --}}
                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-slate-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200">Statistik (Otomatis)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 text-sm">
                                <div>
                                    <span class="block font-medium text-gray-700 dark:text-gray-400">Level</span>
                                    <p class="mt-1 p-3 bg-gray-100 dark:bg-slate-700/50 dark:text-gray-200 rounded-md">{{ $customer->level->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="block font-medium text-gray-700 dark:text-gray-400">Total Poin</span>
                                    <p class="mt-1 p-3 bg-gray-100 dark:bg-slate-700/50 dark:text-gray-200 rounded-md">{{ number_format($customer->cust_point, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <span class="block font-medium text-gray-700 dark:text-gray-400">Total Belanja</span>
                                    <p class="mt-1 p-3 bg-gray-100 dark:bg-slate-700/50 dark:text-gray-200 rounded-md">Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end px-6 py-4 bg-gray-50 dark:bg-slate-800/50 border-t dark:border-slate-700">
                        <a href="{{ route('customers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150 mr-4">
                            Kembali
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>