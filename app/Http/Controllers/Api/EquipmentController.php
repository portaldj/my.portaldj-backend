<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EquipmentModel;
use App\Models\Brand;
use App\Models\EquipmentType;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = EquipmentModel::query();

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('equipment_type_id')) {
            $query->where('equipment_type_id', $request->equipment_type_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        return response()->json($query->limit(20)->get());
    }
}
