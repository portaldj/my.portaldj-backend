<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\DjEquipment;
use App\Models\EquipmentModel;
use Illuminate\Support\Facades\Redirect;

class DjEquipmentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'equipment_type_id' => 'required|exists:equipment_types,id',
            'model_name' => 'required|string|max:255',
            'equipment_model_id' => 'nullable|exists:equipment_models,id',
            'is_public' => 'boolean',
        ]);

        $modelId = $validated['equipment_model_id'] ?? null;

        if (!$modelId) {
            // Check if model exists by name/brand to avoid dupe unverified ones
            $existingModel = EquipmentModel::where('brand_id', $validated['brand_id'])
                ->where('name', $validated['model_name'])
                ->first();

            if ($existingModel) {
                $modelId = $existingModel->id;
            } else {
                // Create new unverified model
                $newModel = EquipmentModel::create([
                    'brand_id' => $validated['brand_id'],
                    'equipment_type_id' => $validated['equipment_type_id'],
                    'name' => $validated['model_name'],
                    'slug' => Str::slug($validated['model_name']) . '-' . uniqid(), // Ensure uniqueness
                    'is_verified' => false,
                ]);
                $modelId = $newModel->id;
            }
        }

        // Create User Equipment linked to the Model
        $request->user()->equipment()->create([
            'equipment_model_id' => $modelId,
            'is_public' => $validated['is_public'] ?? true,
            // We can store copies of other fields if we want legacy support, but for now we rely on relation
        ]);

        return Redirect::back()->with('success', 'Equipment added successfully.');
    }

    public function update(Request $request, DjEquipment $djEquipment)
    {
        if ($request->user()->id !== $djEquipment->user_id) {
            abort(403);
        }

        $validated = $request->validate([
            'is_public' => 'boolean',
        ]);

        $djEquipment->update($validated);

        return Redirect::back()->with('success', 'Equipment updated.');
    }

    public function destroy(Request $request, DjEquipment $djEquipment)
    {
        if ($request->user()->id !== $djEquipment->user_id) {
            abort(403);
        }

        $djEquipment->delete();

        return Redirect::back()->with('success', 'Equipment removed.');
    }
}
