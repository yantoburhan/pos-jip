<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role; // Tambah ini
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    // Pesan validasi kustom (bahasa Indonesia)
    public array $customMessages = [
        '*.required' => ':Attribute tidak boleh kosong.',
        '*.max' => ':Attribute maksimal :max karakter.',
        '*.email' => ':Attribute harus berupa email yang valid.',
        '*.unique' => ':Attribute ini sudah terdaftar, silakan gunakan yang lain.',
        '*.min' => ':Attribute minimal :min karakter.',
        'password.confirmed' => 'Konfirmasi password tidak cocok.',
    ];

    // Nama atribut agar lebih ramah di pesan error
    public array $customAttributes = [
        'name' => 'Nama Lengkap',
        'email' => 'Alamat Email',
        'username' => 'Username',
        'password' => 'Password',
        'roles' => 'Role',
    ];

    /**
     * 🔹 Menampilkan daftar semua user.
     */
    public function index()
    {
        // Otorisasi menggunakan UserPolicy. Ini sudah benar.
        $this->authorize('viewAny', User::class);

        // HAPUS logika if/else yang lama.
        // Jika user lolos otorisasi, berarti dia berhak melihat semua user.
        // Gunakan with('role') untuk efisiensi (menghindari N+1 query).
        $users = User::with('role')->orderBy('id', 'asc')->paginate(10);

        return view('users.index', compact('users'));
    }

    /**
     * 🔹 Form create user baru.
     */
    public function create()
    {
        $this->authorize('create', User::class);

        // Ambil semua role dari database
        $roles = Role::orderBy('id')->pluck('name', 'id');

        return view('users.create', compact('roles'));
    }

    /**
     * 🔹 Store user baru.
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['required', 'integer', 'exists:roles,id'], // Validasi role id
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
     * 🔹 Form edit user.
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);

        // Ambil role dari DB
        $roles = Role::orderBy('id')->pluck('name', 'id');

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * 🔹 Update user.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'roles' => ['required', 'integer', 'exists:roles,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], $this->customMessages, $this->customAttributes);

        $userData = Arr::except($validatedData, ['password']);
        $user->fill($userData);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if (!$user->isDirty()) {
            throw ValidationException::withMessages([
                'name' => 'Tidak ada perubahan data, tidak perlu menyimpan.',
            ]);
        }

        $user->save();

        return redirect()->route('users.index')
                        ->with('success', 'Data user berhasil diperbarui.');
    }

    /**
     * 🔹 Hapus user.
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
