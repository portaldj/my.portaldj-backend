<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEquipmentBrandRequest;
use App\Http\Requests\Admin\UpdateEquipmentBrandRequest;
use App\Http\Resources\EquipmentBrandResource;
use App\Models\EquipmentBrand;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class EquipmentBrandController extends Controller
{
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return EquipmentBrandResource::collection(EquipmentBrand::with('equipmentType')->get());
    }

    public function store(StoreEquipmentBrandRequest $request): EquipmentBrandResource
    {
        return new EquipmentBrandResource(EquipmentBrand::create($request->validated()));
    }

    public function show(EquipmentBrand $equipmentBrand): EquipmentBrandResource
    {
        return new EquipmentBrandResource($equipmentBrand->load('equipmentType'));
    }

    public function update(UpdateEquipmentBrandRequest $request, EquipmentBrand $equipmentBrand): EquipmentBrandResource
    {
        $equipmentBrand->update($request->validated());
        return new EquipmentBrandResource($equipmentBrand);
    }

    public function destroy(EquipmentBrand $equipmentBrand): \Illuminate\Http\JsonResponse
    {
        $equipmentBrand->delete();
        return response()->json(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
