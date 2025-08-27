<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ubah Role') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('roles.update', $role->id) }}" id="edit-role-form">
                        @csrf
                        @method('PUT')

                        <!-- Nama Role -->
                        <div>
                            <x-input-label for="name" :value="__('Nama Role')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $role->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Izin Akses -->
                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900">Izin Akses (Permissions)</h3>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach ($featuresByGroup as $group => $features)
                                    <div class="border rounded-lg shadow-sm">
                                        <div class="bg-gray-50 px-4 py-3 border-b rounded-t-lg">
                                            <label class="font-bold flex items-center text-gray-700">
                                                <input type="checkbox" class="group-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" data-group="{{ Str::slug($group) }}">
                                                <span class="ms-3">{{ $group }}</span>
                                            </label>
                                        </div>
                                        <div class="p-4 space-y-2">
                                            @foreach ($features as $feature)
                                                <label class="flex items-center">
                                                    <input type="checkbox" name="features[]" value="{{ $feature->id }}" class="feature-checkbox {{ Str::slug($group) }} rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                        @if(in_array($feature->id, $roleFeatures)) checked @endif>
                                                    <span class="ms-2 text-sm text-gray-600">{{ ucwords(str_replace(['_','-'], ' ', $feature->name)) }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('roles.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                                {{ __('Batal') }}
                            </a>
                            
                            {{-- Tombol Nonaktif (Abu-abu) --}}
                            <div id="disabled-button-container" class="ms-4">
                                <x-secondary-button disabled>
                                    {{ __('Simpan Perubahan') }}
                                </x-secondary-button>
                            </div>

                            {{-- Tombol Aktif (Hijau) - Awalnya disembunyikan --}}
                            <div id="enabled-button-container" class="ms-4 hidden">
                                <x-success-button>
                                    {{ __('Simpan Perubahan') }}
                                </x-success-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('edit-role-form');
            const disabledButtonContainer = document.getElementById('disabled-button-container');
            const enabledButtonContainer = document.getElementById('enabled-button-container');
            const nameInput = document.getElementById('name');

            // Fungsi untuk mendapatkan kondisi form saat ini sebagai string unik
            function getFormState() {
                const name = nameInput.value;
                const features = Array.from(form.querySelectorAll('.feature-checkbox:checked'))
                                    .map(cb => cb.value)
                                    .sort();
                return JSON.stringify({ name, features });
            }

            const initialState = getFormState();

            // Fungsi utama untuk menukar visibilitas tombol
            function updateButtonVisibility() {
                const currentState = getFormState();
                const hasChanged = initialState !== currentState;

                if (hasChanged) {
                    disabledButtonContainer.classList.add('hidden');
                    enabledButtonContainer.classList.remove('hidden');
                } else {
                    disabledButtonContainer.classList.remove('hidden');
                    enabledButtonContainer.classList.add('hidden');
                }
            }

            // Fungsi untuk update checkbox induk
            function updateGroupCheckbox(groupSlug) {
                const groupCheckbox = document.querySelector(`.group-checkbox[data-group="${groupSlug}"]`);
                if (!groupCheckbox) return;

                const allFeaturesInGroup = document.querySelectorAll(`.feature-checkbox.${groupSlug}`);
                const allChecked = Array.from(allFeaturesInGroup).every(checkbox => checkbox.checked);
                
                groupCheckbox.checked = allChecked && allFeaturesInGroup.length > 0;
            }

            // --- Event Listeners ---
            
            // Listener utama pada form untuk mendeteksi semua perubahan
            form.addEventListener('input', updateButtonVisibility);

            // Listener untuk logika induk-anak
            document.querySelectorAll('.group-checkbox').forEach(groupCheckbox => {
                groupCheckbox.addEventListener('change', function () {
                    const groupSlug = this.dataset.group;
                    document.querySelectorAll(`.feature-checkbox.${groupSlug}`).forEach(feature => {
                        feature.checked = this.checked;
                    });
                });
            });

            document.querySelectorAll('.feature-checkbox').forEach(featureCheckbox => {
                featureCheckbox.addEventListener('change', function () {
                    const groupSlug = this.className.split(' ')[1];
                    updateGroupCheckbox(groupSlug);
                });
            });

            // --- Inisialisasi Halaman ---

            // Cek status awal checkbox induk saat halaman dimuat
            document.querySelectorAll('.group-checkbox').forEach(groupCheckbox => {
                updateGroupCheckbox(groupCheckbox.dataset.group);
            });
        });
    </script>
</x-app-layout>
