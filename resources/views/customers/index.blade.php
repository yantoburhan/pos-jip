<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Customer') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                @can('create', App\Models\Customer::class)
                    <a href="{{ route('customers.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        + Tambah Customer Baru
                    </a>
                @endcan
            </div>

            <x-message />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. HP</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Point</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Belanja</th>
                                @can('viewOpsi', App\Models\Customer::class)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Opsi</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($customers as $customer)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $customer->no_hp_cust }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $customer->cust_name }}</td>
                                    {{-- PERBAIKAN: Menambahkan ?? 'N/A' untuk handle customer tanpa level --}}
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $customer->level->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($customer->cust_point, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @can('update', $customer)
                                            <a href="{{ route('customers.edit', $customer->no_hp_cust) }}" class="inline-block px-3 py-1 bg-yellow-500 text-white rounded-md text-xs font-semibold hover:bg-yellow-600">Ubah</a>
                                        @endcan
                                        @can('delete', $customer)
                                            <form action="{{ route('customers.destroy', $customer->no_hp_cust) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Yakin ingin menghapus customer ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded-md text-xs font-semibold hover:bg-red-700">Hapus</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data customer.</td> </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
             <div class="mt-4">
                {{ $customers->links() }}
            </div>
        </div>
    </div>
</x-app-layout>