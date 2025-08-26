<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Customer Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="POST" action="{{ route('customers.store') }}">
                        @csrf

                        <div>
                            <x-input-label for="no_hp_cust" :value="__('No. HP Customer')" />
                            <x-text-input id="no_hp_cust" class="block mt-1 w-full" type="text" name="no_hp_cust" :value="old('no_hp_cust')" required autofocus />
                            <x-input-error :messages="$errors->get('no_hp_cust')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="cust_name" :value="__('Nama Customer')" />
                            <x-text-input id="cust_name" class="block mt-1 w-full" type="text" name="cust_name" :value="old('cust_name')" required />
                            <x-input-error :messages="$errors->get('cust_name')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="level_id" :value="__('Level')" />
                            <select name="level_id" id="level_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="" disabled selected>-- Pilih Level --</option>
                                @foreach ($levels as $level)
                                    <option value="{{ $level->id }}" {{ old('level_id') == $level->id ? 'selected' : '' }}>
                                        {{ $level->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('level_id')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="total_spent" :value="__('Total Belanja Awal')" />
                            <x-text-input id="total_spent" class="block mt-1 w-full" type="number" name="total_spent" :value="old('total_spent', 0)" required min="0"/>
                            <x-input-error :messages="$errors->get('total_spent')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('customers.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                                {{ __('Batal') }}
                            </a>

                            <x-primary-button class="ms-4">
                                {{ __('Simpan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>