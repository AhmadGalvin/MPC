<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PetController extends Controller
{
    public function index()
    {
        $pets = Pet::with(['owner', 'medicalRecords', 'appointments'])
            ->latest()
            ->paginate(10);
        return view('admin.pets.index', compact('pets'));
    }

    public function create(Request $request)
    {
        $owner = null;
        if ($request->has('owner_id')) {
            $owner = User::where('role', 'owner')->findOrFail($request->owner_id);
        }
        $owners = User::where('role', 'owner')->where('is_active', true)->get();
        return view('admin.pets.create', compact('owners', 'owner'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'species' => ['required', 'string', 'max:255'],
            'breed' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date'],
            'weight' => ['required', 'numeric', 'min:0'],
            'owner_id' => ['required', 'exists:users,id'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('pets', 'public');
            $validated['photo'] = $path;
        }

        $pet = Pet::create($validated);

        if ($request->has('from_owner')) {
            return redirect()
                ->route('admin.owners.show', $pet->owner)
                ->with('success', 'Pet added successfully!');
        }

        return redirect()
            ->route('admin.pets.index')
            ->with('success', 'Pet added successfully!');
    }

    public function show(Pet $pet)
    {
        $pet->load(['owner', 'medicalRecords', 'appointments']);
        return view('admin.pets.show', compact('pet'));
    }

    public function edit(Pet $pet)
    {
        $owners = User::where('role', 'owner')->where('is_active', true)->get();
        return view('admin.pets.edit', compact('pet', 'owners'));
    }

    public function update(Request $request, Pet $pet)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'species' => ['required', 'string', 'max:255'],
            'breed' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date'],
            'weight' => ['required', 'numeric', 'min:0'],
            'owner_id' => ['required', 'exists:users,id'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($pet->photo) {
                Storage::disk('public')->delete($pet->photo);
            }
            $path = $request->file('photo')->store('pets', 'public');
            $validated['photo'] = $path;
        }

        $pet->update($validated);

        return redirect()
            ->route('admin.pets.show', $pet)
            ->with('success', 'Pet updated successfully!');
    }

    public function destroy(Pet $pet)
    {
        if ($pet->photo) {
            Storage::disk('public')->delete($pet->photo);
        }
        
        $pet->delete();

        return back()->with('success', 'Pet deleted successfully!');
    }
} 