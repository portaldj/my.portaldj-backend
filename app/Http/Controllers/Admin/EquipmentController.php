<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Brand;
use App\Models\EquipmentType;
use App\Models\EquipmentModel;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class EquipmentController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Equipment/Index', [
            'brands' => Brand::orderBy('name')->get(),
            'types' => EquipmentType::orderBy('name')->get(),
            'models' => EquipmentModel::with('brand', 'type')
                ->latest()
                ->paginate(20)
                ->appends(['tab' => 'models']),
        ]);
    }

    public function storeBrand(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);

        Brand::create($validated);

        return Redirect::back()->with('success', 'Brand created.');
    }

    public function storeType(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:equipment_types',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);

        EquipmentType::create($validated);

        return Redirect::back()->with('success', 'Type created.');
    }

    public function storeModel(Request $request)
    {
        $validated = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'equipment_type_id' => 'required|exists:equipment_types,id',
            'name' => 'required|string|max:255',
            'is_verified' => 'boolean',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        // $validated['is_verified'] is already set from request, default to true if missing?
        // Actually validating request as boolean ensures it's present or null.
        // Let's ensure it's set.
        $validated['is_verified'] = $request->boolean('is_verified', true);

        // Check duplicate
        if (EquipmentModel::where('brand_id', $validated['brand_id'])->where('name', $validated['name'])->exists()) {
            return Redirect::back()->withErrors(['name' => 'This model already exists for this brand.']);
        }

        EquipmentModel::create($validated);

        return Redirect::back()->with('success', 'Model created.');
    }

    public function updateBrand(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('brands')->ignore($brand->id)],
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);

        $brand->update($validated);

        return Redirect::back()->with('success', 'Brand updated.');
    }

    public function updateType(Request $request, EquipmentType $type)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('equipment_types')->ignore($type->id)],
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);

        $type->update($validated);

        return Redirect::back()->with('success', 'Type updated.');
    }

    public function updateModel(Request $request, EquipmentModel $model)
    {
        $validated = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'equipment_type_id' => 'required|exists:equipment_types,id',
            'name' => 'required|string|max:255',
            'is_verified' => 'boolean',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);

        // Custom check for duplicate excluding self
        if (
            EquipmentModel::where('brand_id', $validated['brand_id'])
                ->where('name', $validated['name'])
                ->where('id', '!=', $model->id)
                ->exists()
        ) {
            return Redirect::back()->withErrors(['name' => 'This model already exists for this brand.']);
        }

        $model->update($validated);

        return Redirect::back()->with('success', 'Model updated.');
    }

    public function destroyBrand(Brand $brand)
    {
        $brand->delete();
        return Redirect::back()->with('success', 'Brand deleted.');
    }

    public function destroyType(EquipmentType $type)
    {
        $type->delete();
        return Redirect::back()->with('success', 'Type deleted.');
    }

    public function destroyModel(EquipmentModel $model)
    {
        $model->delete();
        return Redirect::back()->with('success', 'Model deleted.');
    }
}
