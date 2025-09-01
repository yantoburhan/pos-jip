<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Ubah Data Produk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-6 sm:p-8">

                    {{-- Menampilkan Error Validasi --}}
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

                    @can('update', $product)
                        <form method="POST" action="{{ route('products.update', $product->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nama Produk</label>
                                    <input id="name" class="block mt-1 w-full bg-gray-50 dark:bg-slate-900/50 border-gray-300 dark:border-slate-600 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 rounded-md shadow-sm text-gray-900 dark:text-gray-200" 
                                           type="text" name="name" value="{{ old('name', $product->name) }}" required autofocus />
                                </div>

                                <div>
                                    <label for="point" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Point</label>
                                    <input id="point" min="0" step="any" class="block mt-1 w-full bg-gray-50 dark:bg-slate-900/50 border-gray-300 dark:border-slate-600 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 rounded-md shadow-sm text-gray-900 dark:text-gray-200" 
                                           type="number" name="point" value="{{ old('point', $product->point) }}" required />
                                </div>
                            </div>

                            {{-- Bagian Tombol Aksi --}}
                            <div class="flex items-center justify-end mt-8">
                                <a href="{{ route('products.index') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150 mr-4">
                                    Batal
                                </a>

                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    @endcan

                </div>
            </div>
        </div>
    </div>
</x-app-layout>