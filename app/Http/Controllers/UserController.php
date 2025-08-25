<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    // Properti untuk menyimpan pesan validasi kustom dalam Bahasa Indonesia
    public array $customMessages = [
        '*.required' => ':Attribute tidak boleh kosong.',
        '*.max' => ':Attribute maksimal :max karakter.',
        '*.email' => ':Attribute harus berupa email yang valid.',
        '*.unique' => ':Attribute ini sudah terdaftar, silakan gunakan yang lain.',
        '*.min' => ':Attribute minimal :min karakter.',
        'password.confirmed' => 'Konfirmasi password tidak cocok.',
    ];

    // Properti untuk mengubah nama atribut default menjadi lebih ramah
    public array $customAttributes = [
        'name' => 'Nama Lengkap',
        'email' => 'Alamat Email',
        'username' => 'Username',
        'password' => 'Password',
        'roles' => 'Role',
    ];

    /**
     * Menampilkan daftar semua user.
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);

        if (auth()->user()->roles == 1) {
            // Diperbarui: Mengurutkan dari ID terkecil (tertua)
            $users = User::orderBy('id', 'asc')->paginate(10);
        } else {
            $users = User::where('id', auth()->id())->paginate(10);
        }

        return view('users.index', compact('users'));
    }

    /**
     * Menampilkan form untuk membuat user baru.
     */
    public function create()
    {
        $this->authorize('create', User::class);
        return view('users.create');
    }

    /**
     * Menyimpan user baru ke database.
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        // Panggil validasi dengan pesan dan atribut kustom
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['required', 'integer', Rule::in([0, 1])],
        ], $this->customMessages, $this->customAttributes);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'roles' => $request->roles,
        ]);

        return redirect()->route('users.index')
                        ->with('success', 'User baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit user.
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    /**
     * Memperbarui data user di database.
     */
    public function update(Request $request, User $user)
{
    $this->authorize('update', $user);

    // 1. Validasi semua input
    $validatedData = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
        'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        'roles' => ['required', 'integer', Rule::in([0, 1])],
        'password' => ['nullable', 'string', 'min:8', 'confirmed'],
    ], $this->customMessages, $this->customAttributes);

    // 2. Pisahkan data password dari data lain
    $userData = Arr::except($validatedData, ['password']);

    // 3. Isi model HANYA dengan data non-password untuk pengecekan
    $user->fill($userData);

    // 4. Jika ada password baru di request, tambahkan ke model
    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    // 5. Sekarang, cek apakah ada perubahan
    if (!$user->isDirty()) {
        throw ValidationException::withMessages([
            'name' => 'Tidak ada perubahan data, tidak perlu menyimpan.',
        ]);
    }

    // 6. Jika ada perubahan, simpan data
    $user->save();

    // 7. Redirect dengan pesan sukses
    return redirect()->route('users.index')
                    ->with('success', 'Data user berhasil diperbarui.');
}

    /**
     * Menghapus user dari database.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                            ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')
                        ->with('success', 'User berhasil dihapus.');
    }
}
