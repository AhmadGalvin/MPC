<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Models\Pet;
use App\Models\Product;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctor = auth()->user();
        $prescriptions = Prescription::where('doctor_id', $doctor->id)
            ->with(['pet', 'pet.owner'])
            ->latest()
            ->paginate(10);

        return view('doctor.prescriptions.index', compact('prescriptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $doctor = auth()->user();
        $patients = Pet::whereHas('appointments', function ($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id);
        })->with('owner')->get();
        
        $products = Product::where('type', 'medicine')->get();

        return view('doctor.prescriptions.create', compact('patients', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'notes' => 'required|string',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.instructions' => 'required|string',
        ]);

        $doctor = auth()->user();
        
        // Check if doctor has access to this patient
        $hasAccess = Pet::find($validated['pet_id'])
            ->appointments()
            ->where('doctor_id', $doctor->id)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'You do not have access to create prescriptions for this patient.');
        }

        $prescription = Prescription::create([
            'doctor_id' => $doctor->id,
            'pet_id' => $validated['pet_id'],
            'notes' => $validated['notes'],
        ]);

        foreach ($validated['products'] as $product) {
            $prescription->products()->attach($product['id'], [
                'quantity' => $product['quantity'],
                'instructions' => $product['instructions'],
            ]);
        }

        return redirect()->route('doctor.prescriptions.show', $prescription)
            ->with('success', 'Prescription created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Prescription $prescription)
    {
        $this->authorize('view', $prescription);
        
        $prescription->load(['pet', 'pet.owner', 'doctor', 'products']);
        
        return view('doctor.prescriptions.show', compact('prescription'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Prescription $prescription)
    {
        $this->authorize('update', $prescription);
        
        $prescription->load(['pet', 'pet.owner', 'products']);
        $products = Product::where('type', 'medicine')->get();
        
        return view('doctor.prescriptions.edit', compact('prescription', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prescription $prescription)
    {
        $this->authorize('update', $prescription);

        $validated = $request->validate([
            'notes' => 'required|string',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.instructions' => 'required|string',
        ]);

        $prescription->update([
            'notes' => $validated['notes'],
        ]);

        // Sync products
        $syncData = [];
        foreach ($validated['products'] as $product) {
            $syncData[$product['id']] = [
                'quantity' => $product['quantity'],
                'instructions' => $product['instructions'],
            ];
        }
        $prescription->products()->sync($syncData);

        return redirect()->route('doctor.prescriptions.show', $prescription)
            ->with('success', 'Prescription updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prescription $prescription)
    {
        $this->authorize('delete', $prescription);
        
        $prescription->products()->detach();
        $prescription->delete();

        return redirect()->route('doctor.prescriptions.index')
            ->with('success', 'Prescription deleted successfully.');
    }
} 