<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetController extends Controller
{
    public function index()
    {
        $pets = Auth::user()->pets()->paginate(10);
        return view('owner.pets.index', compact('pets'));
    }

    public function create()
    {
        return view('owner.pets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'breed' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female',
            'weight' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $pet = new Pet($validated);
        $pet->owner_id = Auth::id();
        $pet->save();

        return redirect()
            ->route('owner.pets.show', $pet)
            ->with('success', 'Pet registered successfully.');
    }

    public function show(Pet $pet)
    {
        if ($pet->owner_id !== Auth::id()) {
            abort(403);
        }

        return view('owner.pets.show', compact('pet'));
    }

    public function edit(Pet $pet)
    {
        if ($pet->owner_id !== Auth::id()) {
            abort(403);
        }

        return view('owner.pets.edit', compact('pet'));
    }

    public function update(Request $request, Pet $pet)
    {
        if ($pet->owner_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'breed' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female',
            'weight' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $pet->update($validated);

        return redirect()
            ->route('owner.pets.show', $pet)
            ->with('success', 'Pet information updated successfully.');
    }

    public function destroy(Pet $pet)
    {
        if ($pet->owner_id !== Auth::id()) {
            abort(403);
        }

        $pet->delete();

        return redirect()
            ->route('owner.pets.index')
            ->with('success', 'Pet removed successfully.');
    }
} 