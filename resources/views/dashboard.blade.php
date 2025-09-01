<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Kartu Sambutan yang Sudah Diperbarui --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-6 sm:p-10 bg-gradient-to-r from-blue-500 to-teal-400 text-white">
                    <div class="flex items-center">
                        {{-- Ikon Sederhana (Heroicons Check Circle) --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mr-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="text-2xl font-bold">
                                Selamat Datang, {{ Auth::user()->name }}! 👋
                            </h3>
                            <p class="mt-1 text-blue-100">
                                Anda berhasil masuk. Senang melihat Anda di sini.
                            </p>
                        </div>
                    </div>
                </div>
                
                {{-- Konten Tambahan (Opsional) --}}
                <div class="p-6 sm:p-8 border-t border-gray-200 dark:border-gray-700">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Apa Selanjutnya?</h4>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Ini adalah area dasbor Anda. Anda dapat mulai dengan memeriksa notifikasi, melihat laporan, atau mengelola pengaturan profil Anda.
                    </p>
                    <div class="mt-6">
                        <a href="./profile" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Lihat Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>