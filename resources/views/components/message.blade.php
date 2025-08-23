@if (session('success'))
    <div class="mb-4 rounded-md bg-green-100 p-4 text-sm font-medium text-green-800" role="alert">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 rounded-md bg-red-100 p-4 text-sm font-medium text-red-800" role="alert">
        {{ session('error') }}
    </div>
@endif

@if (session('info'))
    <div class="mb-4 rounded-md bg-blue-100 p-4 text-sm font-medium text-blue-800" role="alert">
        {{ session('info') }}
    </div>
@endif
