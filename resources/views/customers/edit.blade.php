<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Ubah Data Customer') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="POST" action="{{ route('customers.update', $customer->no_hp_cust) }}">
                        @csrf
                        @method('PUT')

                        {{-- Input yang bisa diubah --}}
                        <div>
                            <x-input-label for="no_hp_cust" :value="__('No. HP Customer')" />
                            <x-text-input id="no_hp_cust" class="block mt-1 w-full" type="text" name="no_hp_cust" :value="old('no_hp_cust', $customer->no_hp_cust)" required autofocus />
                            <x-input-error :messages="$errors->get('no_hp_cust')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="cust_name" :value="__('Nama Customer')" />
                            <x-text-input id="cust_name" class="block mt-1 w-full" type="text" name="cust_name" :value="old('cust_name', $customer->cust_name)" required />
                            <x-input-error :messages="$errors->get('cust_name')" class="mt-2" />
                        </div>
                        
                        {{-- Data yang dihitung otomatis (ditampilkan sebagai read-only) --}}
                        <div class="mt-6 pt-4 border-t">
                            <h3 class="text-lg font-medium text-gray-900">Statistik (Otomatis)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 text-sm">
                                <div>
                                    <span class="block font-medium text-gray-700">Level</span>
                                    <p class="mt-1 p-2 bg-gray-100 rounded-md">{{ $customer->level->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="block font-medium text-gray-700">Total Poin</span>
                                    <p class="mt-1 p-2 bg-gray-100 rounded-md">{{ number_format($customer->cust_point, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <span class="block font-medium text-gray-700">Total Belanja</span>
                                    <p class="mt-1 p-2 bg-gray-100 rounded-md">Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>


                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('customers.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline mr-4">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>