<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buat Transaksi Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                            <div class="font-medium">{{ __('Whoops! Ada beberapa kesalahan.') }}</div>
                            <ul class="mt-3 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('transactions.store') }}" id="transaction-form">
                        @csrf
                        
                        <!-- Bagian Utama Transaksi -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label for="date" class="block font-medium text-sm text-gray-700">{{ __('Tanggal') }}</label>
                                <input id="date" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required />
                            </div>

                            <div class="relative">
                                <label for="customer_search" class="block font-medium text-sm text-gray-700">{{ __('No. HP Customer') }}</label>
                                <input id="customer_search" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="text" placeholder="Ketik No. HP atau Nama Customer..." autocomplete="off" />
                                <input type="hidden" name="no_hp_cust" id="no_hp_cust" value="{{ old('no_hp_cust') }}">
                                <div id="customer_suggestions" class="absolute z-20 w-full bg-white border rounded-md mt-1 shadow-lg hidden"></div>
                                <div id="customer-info-display" class="mt-2"></div>
                            </div>

                            <div class="md:col-span-2">
                                <label for="alamat" class="block font-medium text-sm text-gray-700">{{ __('Alamat') }}</label>
                                <textarea id="alamat" name="alamat" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('alamat') }}</textarea>
                            </div>

                             <div>
                                <label for="wilayah" class="block font-medium text-sm text-gray-700">{{ __('Wilayah') }}</label>
                                <select name="wilayah" id="wilayah" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="tidak_diketahui" @if(old('wilayah') == 'tidak_diketahui') selected @endif>Tidak Diketahui</option>
                                    <option value="medan" @if(old('wilayah') == 'medan') selected @endif>Medan</option>
                                    <option value="luar_medan" @if(old('wilayah') == 'luar_medan') selected @endif>Luar Medan</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="kecamatan" class="block font-medium text-sm text-gray-700">{{ __('Kecamatan (Opsional)') }}</label>
                                <input id="kecamatan" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="text" name="kecamatan" value="{{ old('kecamatan') }}" />
                            </div>
                        </div>

                        <!-- Bagian Item Transaksi -->
                        <div class="mt-10">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Produk</h3>
                            <div id="transaction-items-container" class="space-y-4">
                                @if(old('items'))
                                    @foreach(old('items') as $index => $item)
                                        @php
                                            $product = \App\Models\Product::find($item['id_product']);
                                        @endphp
                                        <div class="grid grid-cols-12 gap-3 p-3 bg-gray-50 rounded-lg border items-center transaction-item-row">
                                            <div class="col-span-12 md:col-span-4 relative">
                                                <input type="text" class="product-search block w-full border-gray-300 rounded-md shadow-sm" placeholder="Cari produk..." autocomplete="off" value="{{ $product ? $product->name : '' }}"/>
                                                <input type="hidden" class="id_product" name="items[{{ $index }}][id_product]" value="{{ $item['id_product'] }}"/>
                                                <div class="product-suggestions absolute z-10 w-full bg-white border rounded-md mt-1 shadow-lg hidden"></div>
                                            </div>
                                            <div class="col-span-6 md:col-span-1"><input type="text" class="point_per_item bg-gray-200 text-center border-gray-300 rounded-md shadow-sm" disabled placeholder="Poin" value="{{ $product ? $product->point : '' }}" /></div>
                                            <div class="col-span-6 md:col-span-2"><input type="number" name="items[{{ $index }}][price]" class="price block w-full border-gray-300 rounded-md shadow-sm" placeholder="Harga" value="{{ $item['price'] }}"/></div>
                                            <div class="col-span-6 md:col-span-1"><input type="number" name="items[{{ $index }}][quantity]" class="quantity block w-full border-gray-300 rounded-md shadow-sm" value="{{ $item['quantity'] }}" min="1"/></div>
                                            <div class="col-span-6 md:col-span-3"><input type="text" class="total_price bg-gray-200 font-semibold border-gray-300 rounded-md shadow-sm" disabled /></div>
                                            <div class="col-span-12 md:col-span-1 flex justify-end">
                                                <button type="button" class="remove-item-btn text-red-500 hover:text-red-700 p-2 rounded-full hover:bg-red-100">&times;</button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <template id="transaction-item-template">
                                <div class="grid grid-cols-12 gap-3 p-3 bg-gray-50 rounded-lg border items-center transaction-item-row">
                                    <div class="col-span-12 md:col-span-4 relative">
                                        <input type="text" class="product-search block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Cari produk..." autocomplete="off"/>
                                        <input type="hidden" class="id_product"/>
                                        <div class="product-suggestions absolute z-10 w-full bg-white border rounded-md mt-1 shadow-lg hidden"></div>
                                    </div>
                                    <div class="col-span-6 md:col-span-1"><input type="text" class="point_per_item bg-gray-200 text-center border-gray-300 rounded-md shadow-sm" disabled placeholder="Poin" /></div>
                                    <div class="col-span-6 md:col-span-2"><input type="number" class="price block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Harga"/></div>
                                    <div class="col-span-6 md:col-span-1"><input type="number" class="quantity block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" value="1" min="1"/></div>
                                    <div class="col-span-6 md:col-span-3"><input type="text" class="total_price bg-gray-200 font-semibold border-gray-300 rounded-md shadow-sm" disabled /></div>
                                    <div class="col-span-12 md:col-span-1 flex justify-end">
                                        <button type="button" class="remove-item-btn text-red-500 hover:text-red-700 p-2 rounded-full hover:bg-red-100">&times;</button>
                                    </div>
                                </div>
                            </template>
                            
                            <button type="button" id="add-item-btn" class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Tambah Produk
                            </button>
                        </div>

                        <!-- Total -->
                         <div class="mt-10 pt-6 border-t-2 border-dashed">
                            <div class="flex justify-end">
                                <div class="w-full max-w-sm space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-medium text-gray-600">Total Penjualan:</span>
                                        <span id="total-penjualan" class="text-xl font-bold text-gray-900">Rp 0</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-medium text-gray-600">Total Poin:</span>
                                        <span id="total-poin" class="text-xl font-bold text-indigo-600">0 Poin</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                             <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
                                Batal
                            </a>
                            <button type="submit" class="ml-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Simpan Transaksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('transaction-items-container');
    const template = document.getElementById('transaction-item-template');
    const addItemBtn = document.getElementById('add-item-btn');
    
    let itemIndex = {{ count(old('items', [])) }};

    const formatCurrency = (number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);

    function addNewItem() {
        const clone = template.content.cloneNode(true);
        const row = clone.querySelector('.transaction-item-row');
        
        const newIndex = itemIndex++;
        row.querySelector('.id_product').name = `items[${newIndex}][id_product]`;
        row.querySelector('.price').name = `items[${newIndex}][price]`;
        row.querySelector('.quantity').name = `items[${newIndex}][quantity]`;
        
        container.appendChild(clone);
    }

    function updateProductRow(row, product) {
        row.querySelector('.id_product').value = product.id;
        row.querySelector('.product-search').value = product.name;
        row.querySelector('.point_per_item').value = product.point;
        row.querySelector('.price').value = product.price;
        row.querySelector('.product-suggestions').innerHTML = '';
        row.querySelector('.product-suggestions').classList.add('hidden');
        updateCalculations();
    }

    function updateCalculations() {
        let totalPenjualan = 0;
        let totalPoin = 0;
        document.querySelectorAll('.transaction-item-row').forEach(row => {
            const price = parseFloat(row.querySelector('.price').value) || 0;
            const quantity = parseInt(row.querySelector('.quantity').value) || 0;
            const points = parseInt(row.querySelector('.point_per_item').value) || 0;
            const subtotal = price * quantity;
            
            row.querySelector('.total_price').value = formatCurrency(subtotal);
            totalPenjualan += subtotal;
            totalPoin += points * quantity;
        });
        document.getElementById('total-penjualan').textContent = formatCurrency(totalPenjualan);
        document.getElementById('total-poin').textContent = `${totalPoin} Poin`;
    }

    // --- Event Listeners ---
    addItemBtn.addEventListener('click', addNewItem);
    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item-btn')) {
            e.target.closest('.transaction-item-row').remove();
            updateCalculations();
        }
    });
    container.addEventListener('input', function(e) {
        if (e.target.classList.contains('price') || e.target.classList.contains('quantity')) {
            updateCalculations();
        }
    });

    // --- AJAX SEARCH ---
    const customerSearch = document.getElementById('customer_search');
    const customerSuggestions = document.getElementById('customer_suggestions');
    const customerInfoDisplay = document.getElementById('customer-info-display');
    const hiddenCustomerInput = document.getElementById('no_hp_cust');

    customerSearch.addEventListener('keyup', async function(e) {
        const query = e.target.value;
        hiddenCustomerInput.value = ''; 
        customerInfoDisplay.classList.add('hidden');

        if (query.length < 3) {
            customerSuggestions.classList.add('hidden');
            return;
        }

        const response = await fetch(`{{ route('search.customers') }}?q=${query}`);
        const customers = await response.json();
        
        customerSuggestions.innerHTML = '';
        if (customers.length > 0) {
            customers.forEach(cust => {
                const div = document.createElement('div');
                div.innerHTML = `<div class="font-bold">${cust.cust_name}</div><div class="text-sm text-gray-600">${cust.no_hp_cust}</div>`;
                div.className = 'p-2 hover:bg-gray-100 cursor-pointer border-b';
                div.onclick = () => {
                    customerSearch.value = cust.no_hp_cust;
                    hiddenCustomerInput.value = cust.no_hp_cust;
                    document.getElementById('alamat').value = cust.alamat || '';
                    
                    customerInfoDisplay.innerHTML = `<div class="p-2 bg-green-50 border border-green-200 rounded-md text-sm text-green-700">Customer: <strong>${cust.cust_name}</strong></div>`;
                    customerInfoDisplay.classList.remove('hidden');
                    customerSuggestions.classList.add('hidden');
                };
                customerSuggestions.appendChild(div);
            });
        } else {
            // PERBAIKAN: Menampilkan link untuk membuat customer baru
            const createCustomerUrl = `{{ route('customers.create') }}?no_hp_cust=${encodeURIComponent(query)}`;
            customerSuggestions.innerHTML = `
                <div class="p-2 text-center text-gray-500">
                    Data Tidak Ada. | 
                    <a href="${createCustomerUrl}" class="text-blue-600 hover:text-blue-800 no-underline font-semibold">
                        + create ${query}
                    </a>
                </div>
            `;
        }
        customerSuggestions.classList.remove('hidden');
    });

    container.addEventListener('keyup', async function(e) {
        if (!e.target.classList.contains('product-search')) return;

        const row = e.target.closest('.transaction-item-row');
        const suggestionsDiv = row.querySelector('.product-suggestions');
        const idInput = row.querySelector('.id_product');
        const query = e.target.value;

        idInput.value = '';

        if (query.length < 2) {
            suggestionsDiv.classList.add('hidden');
            return;
        }
        const response = await fetch(`{{ route('search.products') }}?q=${query}`);
        const products = await response.json();

        suggestionsDiv.innerHTML = '';
        if (products.length > 0) {
             products.forEach(prod => {
                const div = document.createElement('div');
                div.innerHTML = `${prod.name} (${prod.point} Pts)`;
                div.className = 'p-2 hover:bg-gray-100 cursor-pointer';
                div.onclick = () => updateProductRow(row, prod);
                suggestionsDiv.appendChild(div);
            });
        } else {
            suggestionsDiv.innerHTML = `<div class="p-2 text-gray-500">Produk tidak ditemukan.</div>`;
        }
        suggestionsDiv.classList.remove('hidden');
    });
    
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.relative')) {
            customerSuggestions.classList.add('hidden');
        }
        document.querySelectorAll('.product-suggestions').forEach(el => {
            if (!el.parentElement.contains(e.target)) {
                el.classList.add('hidden');
            }
        });
    });
    
    if (itemIndex === 0) {
        addNewItem();
    } else {
        updateCalculations();
    }
});
</script>
</x-app-layout>