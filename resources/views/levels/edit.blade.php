<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Ubah Data Level') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-6 sm:p-8">

                    {{-- Menampilkan Error Validasi --}}
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
                    
                    {{-- Notifikasi khusus jika level adalah N/A --}}
                    @if ($level->name === 'N/A')
                        <div class="mb-6 p-4 bg-yellow-100 dark:bg-yellow-500/20 text-yellow-800 dark:text-yellow-400 rounded-lg text-sm">
                            <p><span class="font-bold">Perhatian:</span> Level "N/A" adalah level default dan datanya tidak dapat diubah.</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('levels.update', $level->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nama Level</label>
                                <input id="name" class="block mt-1 w-full bg-gray-50 dark:bg-slate-900/50 border-gray-300 dark:border-slate-600 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 rounded-md shadow-sm text-gray-900 dark:text-gray-200 disabled:opacity-50" 
                                       type="text" name="name" value="{{ old('name', $level->name) }}" required
                                       @if($level->name === 'N/A') disabled @endif />
                            </div>

                            <div>
                                <label for="level_point" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Minimal Point</label>
                                <input id="level_point" min="0" class="block mt-1 w-full bg-gray-50 dark:bg-slate-900/50 border-gray-300 dark:border-slate-600 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 rounded-md shadow-sm text-gray-900 dark:text-gray-200 disabled:opacity-50" 
                                       type="number" name="level_point" value="{{ old('level_point', $level->level_point) }}" required
                                       @if($level->name === 'N/A') disabled @endif />
                            </div>
                        </div>

                        {{-- Bagian Tombol Aksi --}}
                        <div class="flex items-center justify-end mt-8">
                            <a href="{{ route('levels.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150 mr-4">
                                Kembali
                            </a>

                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                                    @if($level->name === 'N/A') disabled @endif>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>