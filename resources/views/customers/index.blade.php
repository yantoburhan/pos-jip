<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Bagian Header Halaman, Pencarian & Tombol Aksi --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 px-4 sm:px-0">
                <div class="mb-4 sm:mb-0">
                    <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ __('Manajemen Customer') }}
                    </h2>
                </div>

                <div class="flex items-center space-x-4 w-full sm:w-auto">
                    {{-- Input Pencarian --}}
                    <div class="w-full sm:w-64">
                        <label for="search-input" class="sr-only">Cari</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                            </div>
                            <input type="text" id="search-input"
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white dark:bg-slate-900/50 dark:border-slate-600 dark:text-gray-200 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   placeholder="Ketik untuk mencari...">
                        </div>
                    </div>
                    @can('create', App\Models\Customer::class)
                        <a href="{{ route('customers.create') }}" class="flex-shrink-0 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" /></svg>
                            <span>Tambah</span>
                        </a>
                    @endcan
                </div>
            </div>

            <x-message />

            {{-- Input tersembunyi untuk menyimpan token CSRF --}}
            <input type="hidden" id="csrf-token" value="{{ csrf_token() }}">

            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-lg sm:rounded-xl">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 dark:bg-slate-700">
                             <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No. HP</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Level</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Point</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Belanja</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Opsi</th>
                            </tr>
                        </thead>
                        <tbody id="customer-table-body" class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                            {{-- Data awal dimuat langsung di sini --}}
                            @forelse ($customers as $customer)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $customer->no_hp_cust }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $customer->cust_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $customer->level->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200 text-right">{{ number_format($customer->cust_point, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200 text-right">Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex items-center justify-center space-x-2">
                                            @can('update', $customer)
                                                <a href="{{ route('customers.edit', $customer->no_hp_cust) }}" class="inline-block px-3 py-1.5 bg-yellow-500 text-white rounded-md text-xs font-semibold hover:bg-yellow-600 transition-all">Ubah</a>
                                            @endcan
                                            @can('delete', $customer)
                                                <form action="{{ route('customers.destroy', $customer->no_hp_cust) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus customer ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-md text-xs font-semibold hover:bg-red-700 transition-all">Hapus</button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                        @if (request('q'))
                                            Customer dengan nama atau no. hp "{{ request('q') }}" tidak ditemukan.
                                        @else
                                            Tidak ada data customer.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="pagination-links" class="mt-6">
                {{ $customers->appends(['q' => request('q')])->links() }}
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        const tableBody = document.getElementById('customer-table-body');
        const paginationLinks = document.getElementById('pagination-links');
        const initialContent = tableBody.innerHTML;
        const csrfToken = document.getElementById('csrf-token').value;

        const userCanDelete = @json(auth()->user()->can('deleteAny', App\Models\Customer::class));
        const userCanUpdate = @json(auth()->user()->can('updateAny', App\Models\Customer::class));

        const debounce = (func, delay) => {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), delay);
            };
        };

        const performSearch = (query) => {
            if (!query.trim()) {
                tableBody.innerHTML = initialContent;
                paginationLinks.style.display = 'block';
                return;
            }

            paginationLinks.style.display = 'none';
            tableBody.innerHTML = `<tr><td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">Mencari...</td></tr>`;

            fetch(`{{ route('customers.search') }}?q=${encodeURIComponent(query)}`)
                .then(response => {
                    if (!response.ok) { throw new Error('Network response was not ok'); }
                    return response.json();
                })
                .then(customers => {
                    tableBody.innerHTML = '';
                    if (customers.length > 0) {
                        customers.forEach(customer => {
                            const levelName = customer.level ? customer.level.name : 'N/A';
                            const totalSpent = new Intl.NumberFormat('id-ID').format(customer.total_spent);
                            const custPoint = new Intl.NumberFormat('id-ID').format(customer.cust_point);
                            const editUrl = `{{ url('customers') }}/${customer.no_hp_cust}/edit`;
                            const deleteUrl = `{{ url('customers') }}/${customer.no_hp_cust}`;

                            let optionsHtml = '';
                            if (userCanUpdate) {
                                optionsHtml += `<a href="${editUrl}" class="inline-block px-3 py-1.5 bg-yellow-500 text-white rounded-md text-xs font-semibold hover:bg-yellow-600 transition-all">Ubah</a>`;
                            }
                            if (userCanDelete) {
                                optionsHtml += `
                                    <form action="${deleteUrl}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus customer ini?');">
                                        <input type="hidden" name="_token" value="${csrfToken}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-md text-xs font-semibold hover:bg-red-700 transition-all">Hapus</button>
                                    </form>
                                `;
                            }

                            const rowHtml = `
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">${customer.no_hp_cust}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">${customer.cust_name}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">${levelName}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200 text-right">${custPoint}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200 text-right">Rp ${totalSpent}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex items-center justify-center space-x-2">
                                            ${optionsHtml}
                                        </div>
                                    </td>
                                </tr>
                            `;
                            tableBody.insertAdjacentHTML('beforeend', rowHtml);
                        });
                    } else {
                        tableBody.innerHTML = `<tr><td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">Customer dengan nama atau no. hp "${query}" tidak ditemukan.</td></tr>`;
                    }
                })
                .catch(error => {
                    console.error('Error fetching search results:', error);
                    tableBody.innerHTML = `<tr><td colspan="6" class="px-6 py-10 text-center text-red-500">Terjadi kesalahan saat memuat data.</td></tr>`;
                });
        };

        searchInput.addEventListener('input', debounce(e => {
            performSearch(e.target.value);
        }, 300));
    });
    </script>
</x-app-layout>
