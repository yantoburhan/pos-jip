<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buat Produk Baru') }}
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

                    @can('create', App\Models\Product::class)
                        <form method="POST" action="{{ route('products.store') }}">
                            @csrf

                            <div>
                                <label for="name" class="block font-medium text-sm text-gray-700">Nama</label>
                                <input id="name" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 
                                    focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                    type="text" name="name" value="{{ old('name') }}" required autofocus />
                            </div>

                            <div class="mt-4">
                                <label for="point" class="block font-medium text-sm text-gray-700">Point</label>
                                <input id="point" min="0" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 
                                    focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                    type="number" name="point" value="{{ old('point') }}" />
                            </div>

                            {{-- Bagian Tombol Aksi --}}
                            <div class="flex items-center justify-end mt-4">
                                <a href="{{ route('products.index') }}" 
                                   class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 
                                          focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 mr-4">
                                    Batal
                                </a>

                                <button type="submit" 
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 
                                               focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    @else
                        <p class="text-gray-600">Anda tidak memiliki izin untuk membuat produk.</p>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
