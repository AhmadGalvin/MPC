<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PetController extends Controller
{
    /**
     * Display a listing of the pets.
     */
    public function index()
    {
        $pets = auth()->user()->pets()->with(['consultations', 'medicalRecords'])->get();
        return view('owner.pets.index', compact('pets'));
    }

    /**
     * Show the form for creating a new pet.
     */
    public function create()
    {
        return view('owner.pets.create');
    }

    /**
     * Store a newly created pet.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'breed' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'weight' => 'required|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('pets', 'public');
            $validated['photo'] = $path;
        }

        $validated['owner_id'] = auth()->id();
        Pet::create($validated);

        return redirect()->route('owner.pets.index')->with('success', 'Pet added successfully!');
    }

    /**
     * Display the specified pet.
     */
    public function show(Request $request, Pet $pet)
    {
        // Check if the authenticated user owns the pet
        if ($pet->owner_id !== $request->user()->id) {
            return redirect()->route('owner.pets.index')
                ->with('error', 'Unauthorized access to pet');
        }

        return view('owner.pets.show', compact('pet'));
    }

    /**
     * Show the form for editing the specified pet.
     */
    public function edit(Pet $pet)
    {
        $this->authorize('update', $pet);
        return view('owner.pets.edit', compact('pet'));
    }

    /**
     * Update the specified pet.
     */
    public function update(Request $request, Pet $pet)
    {
        $this->authorize('update', $pet);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'breed' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'weight' => 'required|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            if ($pet->photo) {
                Storage::disk('public')->delete($pet->photo);
            }
            $path = $request->file('photo')->store('pets', 'public');
            $validated['photo'] = $path;
        }

        $pet->update($validated);

        return redirect()->route('owner.pets.index')->with('success', 'Pet updated successfully!');
    }

    /**
     * Remove the specified pet.
     */
    public function destroy(Pet $pet)
    {
        $this->authorize('delete', $pet);

        if ($pet->photo) {
            Storage::disk('public')->delete($pet->photo);
        }

        $pet->delete();

        return redirect()->route('owner.pets.index')->with('success', 'Pet deleted successfully!');
    }

    /**
     * Upload a new photo for the pet.
     */
    public function uploadPhoto(Request $request, Pet $pet)
    {
        // Check if the authenticated user owns the pet
        if ($pet->owner_id !== $request->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access to pet'
            ], 403);
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Delete old photo if exists
        if ($pet->photo) {
            Storage::disk('public')->delete($pet->photo);
        }

        $photoPath = $request->file('photo')->store('pets', 'public');
        $pet->update(['photo' => $photoPath]);

        return response()->json([
            'status' => 'success',
            'message' => 'Photo uploaded successfully',
            'data' => [
                'photo_path' => $photoPath
            ]
        ]);
    }
} 