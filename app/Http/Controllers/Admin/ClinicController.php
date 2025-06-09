<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\ClinicUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;

class ClinicController extends Controller
{
    public function profile()
    {
        $clinic = auth()->user()->ownedClinic()->firstOrFail();
        return view('admin.clinic.profile', compact('clinic'));
    }

    public function update(Request $request)
    {
        $clinic = auth()->user()->ownedClinic()->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:1000',
            'phone_number' => ['required', 'string', 'max:20', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'email' => 'required|email|max:255',
            'description' => 'nullable|string|max:5000',
            'logo' => [
                'nullable',
                File::image()
                    ->max(2048)
                    ->dimensions(
                        minWidth: 100,
                        minHeight: 100,
                        maxWidth: 2000,
                        maxHeight: 2000
                    )
                    ->types(['jpg', 'jpeg', 'png', 'gif'])
            ]
        ]);

        DB::beginTransaction();

        try {
            // Store old values for history
            $oldValues = $clinic->only([
                'name', 'address', 'phone_number', 'email', 'description', 'logo_path'
            ]);

            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                
                // Generate safe filename
                $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
                
                // Delete old logo if exists
                if ($clinic->logo_path) {
                    Storage::disk('public')->delete($clinic->logo_path);
                }

                // Store new logo with sanitized filename
                $path = $file->storeAs('clinic-logos', $filename, 'public');
                $validated['logo_path'] = $path;
            }

            // Update clinic
            $clinic->update($validated);

            // Get new values
            $newValues = $clinic->only([
                'name', 'address', 'phone_number', 'email', 'description', 'logo_path'
            ]);

            // Create update history entry
            $changes = $this->generateChangesDescription($oldValues, $newValues);
            
            ClinicUpdate::create([
                'clinic_id' => $clinic->id,
                'user_id' => auth()->id(),
                'changes' => $changes,
                'old_values' => $oldValues,
                'new_values' => $newValues,
            ]);

            DB::commit();

            return redirect()->route('admin.clinic.profile')
                ->with('success', 'Clinic profile updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Clinic update error: ' . $e->getMessage());
            
            return redirect()->route('admin.clinic.profile')
                ->with('error', 'Failed to update clinic profile. Please try again.');
        }
    }

    public function history()
    {
        try {
            $updates = ClinicUpdate::where('clinic_id', auth()->user()->ownedClinic()->value('id'))
                ->with('user:id,name')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'status' => 'success',
                'data' => $updates
            ]);
        } catch (\Exception $e) {
            \Log::error('Clinic history error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load clinic history'
            ], 500);
        }
    }

    private function generateChangesDescription($oldValues, $newValues): string
    {
        $changes = [];
        $fieldLabels = [
            'name' => 'Clinic Name',
            'address' => 'Address',
            'phone_number' => 'Phone Number',
            'email' => 'Email',
            'description' => 'Description',
            'logo_path' => 'Logo'
        ];

        foreach ($oldValues as $key => $oldValue) {
            if ($key === 'logo_path') {
                if (($oldValue === null && $newValues[$key] !== null) ||
                    ($oldValue !== null && $newValues[$key] !== null && $oldValue !== $newValues[$key])) {
                    $changes[] = 'Updated clinic logo';
                }
                continue;
            }

            if ($oldValue !== $newValues[$key]) {
                $fieldName = $fieldLabels[$key] ?? str_replace('_', ' ', ucfirst($key));
                $changes[] = "Updated {$fieldName}";
            }
        }

        return empty($changes) ? 'No significant changes' : implode(', ', $changes);
    }

    public function statistics()
    {
        $clinic = auth()->user()->clinic;

        $stats = [
            'total_revenue' => $clinic->consultations()
                ->where('status', 'completed')
                ->sum('fee'),
            'total_products_sold' => $clinic->products()
                ->sum('stock'),
            'total_consultations' => $clinic->consultations()->count(),
            'active_doctors' => $clinic->doctors()->where('is_active', true)->count(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }
} 