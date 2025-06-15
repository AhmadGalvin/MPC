<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class PetOwnerController extends Controller
{
    public function index()
    {
        $owners = User::where('role', 'owner')
            ->with('pets')
            ->latest()
            ->paginate(10);
        
        return view('admin.pet-owners.index', compact('owners'));
    }

    public function createStepOne()
    {
        return view('admin.pet-owners.create-step-one');
    }

    public function storeStepOne(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'owner',
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.pet-owners.create-step-two', $user)
            ->with('success', 'Basic information saved successfully!');
    }

    public function createStepTwo(User $owner)
    {
        if ($owner->role !== 'owner') {
            abort(404);
        }
        
        return view('admin.pet-owners.create-step-two', compact('owner'));
    }

    public function store(Request $request, User $owner)
    {
        if ($owner->role !== 'owner') {
            abort(404);
        }

        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        DB::transaction(function () use ($validated, $owner) {
            $owner->update($validated);
        });

        return redirect()
            ->route('admin.owners.index')
            ->with('success', 'Pet owner created successfully!');
    }

    public function show(User $owner)
    {
        if ($owner->role !== 'owner') {
            abort(404);
        }

        $owner->load('pets');
        return view('admin.pet-owners.show', compact('owner'));
    }

    public function edit(User $owner)
    {
        if ($owner->role !== 'owner') {
            abort(404);
        }

        return view('admin.pet-owners.edit', compact('owner'));
    }

    public function update(Request $request, User $owner)
    {
        if ($owner->role !== 'owner') {
            abort(404);
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $owner->id],
            'is_active' => ['required', 'boolean'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($validated, $owner) {
            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $owner->update($validated);
        });

        return redirect()
            ->route('admin.owners.index')
            ->with('success', 'Pet owner updated successfully!');
    }

    public function destroy(User $owner)
    {
        if ($owner->role !== 'owner') {
            abort(404);
        }

        $owner->delete();

        return redirect()
            ->route('admin.owners.index')
            ->with('success', 'Pet owner deleted successfully!');
    }
} 