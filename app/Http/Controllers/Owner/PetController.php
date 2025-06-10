<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PetController extends Controller
{
    public function index()
    {
        $pets = Auth::user()->pets()
            ->with(['consultations', 'medicalRecords'])
            ->paginate(10);
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
            'weight' => 'required|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('pets', 'public');
            $validated['photo'] = $path;
        }

        $validated['owner_id'] = Auth::id();
        $pet = Pet::create($validated);

        return redirect()
            ->route('owner.pets.index')
            ->with('success', 'Pet added successfully!');
    }

    public function show(Pet $pet)
    {
        if ($pet->owner_id !== Auth::id()) {
            return redirect()->route('owner.pets.index')
                ->with('error', 'Unauthorized access to pet');
        }

        $pet->load(['consultations', 'medicalRecords']);
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
            'weight' => 'required|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
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
            ->route('owner.pets.show', $pet)
            ->with('success', 'Pet information updated successfully.');
    }

    public function destroy(Pet $pet)
    {
        if ($pet->owner_id !== Auth::id()) {
            abort(403);
        }

        if ($pet->photo) {
            Storage::disk('public')->delete($pet->photo);
        }

        $pet->delete();

        return redirect()
            ->route('owner.pets.index')
            ->with('success', 'Pet removed successfully.');
    }

    public function uploadPhoto(Request $request, Pet $pet)
    {
        if ($pet->owner_id !== Auth::id()) {
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