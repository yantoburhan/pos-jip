<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function __construct()
    {
        // Menerapkan Policy ke semua method di controller ini
        $this->authorizeResource(Role::class, 'role');
    }

    public function index()
    {
        $roles = Role::withCount('users')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $featuresByGroup = Feature::all()->groupBy('group');
        return view('roles.create', compact('featuresByGroup'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'features' => ['nullable', 'array'],
            'features.*' => ['exists:features,id'],
        ]);

        $role = Role::create($validated);
        $role->features()->attach($request->features);

        return redirect()->route('roles.index')->with('success', 'Role baru berhasil dibuat.');
    }

    public function edit(Role $role)
    {
        $role->load('features');
        $featuresByGroup = Feature::all()->groupBy('group');
        $roleFeatures = $role->features->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'featuresByGroup', 'roleFeatures'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'features' => ['nullable', 'array'],
            'features.*' => ['exists:features,id'],
        ]);

        $role->update($validated);
        $role->features()->sync($request->features ?? []);

        return redirect()->route('roles.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Role tidak bisa dihapus karena masih digunakan oleh user.');
        }
        
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus.');
    }
}
