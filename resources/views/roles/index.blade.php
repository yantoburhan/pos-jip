<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Role') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                {{-- Tombol ini hanya muncul jika user punya izin 'create' (dari RolePolicy) --}}
                @can('create', App\Models\Role::class)
                    <a href="{{ route('roles.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        + Buat Role Baru
                    </a>
                @endcan
            </div>

            <x-message />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah User</th>
                                
                                {{-- Kolom Opsi hanya muncul jika user punya izin 'update' ATAU 'delete' --}}
                                @canany(['update', 'delete'], $roles->first())
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Opsi</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($roles as $role)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $role->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $role->users_count }}</td>
                                    
                                    {{-- Tombol Opsi hanya muncul jika user punya izin 'update' ATAU 'delete' --}}
                                    @canany(['update', 'delete'], $role)
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @can('update', $role)
                                                <a href="{{ route('roles.edit', $role->id) }}" class="text-indigo-600 hover:text-indigo-900">Ubah</a>
                                            @endcan
                                            
                                            @can('delete', $role)
                                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Yakin ingin menghapus role ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                                </form>
                                            @endcan
                                        </td>
                                    @endcanany
                                </tr>
                            @empty
                                <tr>
                                    {{-- Sesuaikan colspan berdasarkan izin --}}
                                    @canany(['update', 'delete'], new App\Models\Role)
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">Tidak ada data role.</td>
                                    @else
                                        <td colspan="2" class="px-6 py-4 text-center text-gray-500">Tidak ada data role.</td>
                                    @endcanany
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
