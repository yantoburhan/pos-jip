<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Ubah Data Produk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    {{-- Menampilkan Error Validasi --}}
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                            <ul class="list-disc list-inside">
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

                            <div>
                                <label for="name" class="block font-medium text-sm text-gray-700">Nama</label>
                                <input id="name" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                    type="text" name="name" value="{{ old('name', $product->name) }}" required autofocus />
                            </div>

                            <div class="mt-4">
                                <label for="point" class="block font-medium text-sm text-gray-700">Point</label>
                                {{-- PERBAIKAN: Menambahkan step="any" untuk mengizinkan desimal --}}
                                <input id="point" min="0" step="any" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                    type="number" name="point" value="{{ old('point', $product->point) }}" required />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <a href="{{ route('products.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline mr-4">
                                    Batal
                                </a>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
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