<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Ubah Data User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-6 sm:p-8">
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400 rounded-lg">
                            <p class="font-bold">Oops! Ada beberapa kesalahan:</p>
                            <ul class="list-disc list-inside mt-2 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('users.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nama</label>
                                <input id="name" class="block mt-1 w-full bg-gray-50 dark:bg-slate-900/50 border-gray-300 dark:border-slate-600 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 rounded-md shadow-sm text-gray-900 dark:text-gray-200" type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus />
                            </div>

                            <div>
                                <label for="username" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Username</label>
                                <input id="username" class="block mt-1 w-full bg-gray-50 dark:bg-slate-900/50 border-gray-300 dark:border-slate-600 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 rounded-md shadow-sm text-gray-900 dark:text-gray-200" type="text" name="username" value="{{ old('username', $user->username) }}" required />
                            </div>

                            <div>
                                <label for="email" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Email</label>
                                <input id="email" class="block mt-1 w-full bg-gray-50 dark:bg-slate-900/50 border-gray-300 dark:border-slate-600 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 rounded-md shadow-sm text-gray-900 dark:text-gray-200" type="email" name="email" value="{{ old('email', $user->email) }}" required />
                            </div>

                            <div>
                                <label for="roles" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Role</label>
                                <select name="roles" id="roles" class="block mt-1 w-full bg-gray-50 dark:bg-slate-900/50 border-gray-300 dark:border-slate-600 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 rounded-md shadow-sm text-gray-900 dark:text-gray-200">
                                    @foreach ($roles as $key => $label)
                                        <option value="{{ $key }}" {{ old('roles', $user->roles) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="password" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Password Baru (Opsional)</label>
                                <input id="password" class="block mt-1 w-full bg-gray-50 dark:bg-slate-900/50 border-gray-300 dark:border-slate-600 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 rounded-md shadow-sm text-gray-900 dark:text-gray-200" type="password" name="password" />
                                <small class="text-xs text-gray-500 dark:text-gray-400">Kosongkan jika tidak ingin mengubah password.</small>
                            </div>

                            <div>
                                <label for="password_confirmation" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Konfirmasi Password Baru</label>
                                <input id="password_confirmation" class="block mt-1 w-full bg-gray-50 dark:bg-slate-900/50 border-gray-300 dark:border-slate-600 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 rounded-md shadow-sm text-gray-900 dark:text-gray-200" type="password" name="password_confirmation" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8">
                            <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150 mr-4">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>