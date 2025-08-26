<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Produk (Pending)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    {{-- Info untuk user --}}
                    <div class="mb-4 p-4 bg-yellow-100 text-yellow-700 rounded-md">
                        Perubahan produk sedang menunggu persetujuan admin. 
                        Anda hanya dapat membatalkan edit ini.
                    </div>

                    <form method="POST" action="{{ route('products.pending.cancel', $productPending->id) }}">
                        @csrf
                        @method('DELETE')

                        <div>
                            <label class="block font-medium text-sm text-gray-700">Nama</label>
                            <input class="block mt-1 w-full rounded-md bg-gray-100" type="text" value="{{ $productPending->name }}" readonly />
                        </div>

                        <div class="mt-4">
                            <label class="block font-medium text-sm text-gray-700">Point</label>
                            <input class="block mt-1 w-full rounded-md bg-gray-100" type="number" value="{{ $productPending->point }}" readonly />
                        </div>

                        <div class="mt-4">
                            <label class="block font-medium text-sm text-gray-700">Harga</label>
                            <input class="block mt-1 w-full rounded-md bg-gray-100" type="number" value="{{ $productPending->price }}" readonly />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 mr-4">
                                Kembali
                            </a>

                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                Batalkan Edit
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
