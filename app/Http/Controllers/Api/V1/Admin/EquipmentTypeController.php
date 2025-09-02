<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEquipmentTypeRequest;
use App\Http\Requests\Admin\UpdateEquipmentTypeRequest;
use App\Http\Resources\EquipmentTypeResource;
use App\Models\EquipmentType;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class EquipmentTypeController extends Controller
{
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return EquipmentTypeResource::collection(EquipmentType::all());
    }

    public function store(StoreEquipmentTypeRequest $request): EquipmentTypeResource
    {
        return new EquipmentTypeResource(EquipmentType::create($request->validated()));
    }

    public function show(EquipmentType $equipmentType): EquipmentTypeResource
    {
        return new EquipmentTypeResource($equipmentType);
    }

    public function update(UpdateEquipmentTypeRequest $request, EquipmentType $equipmentType): EquipmentTypeResource
    {
        $equipmentType->update($request->validated());
        return new EquipmentTypeResource($equipmentType);
    }

    public function destroy(EquipmentType $equipmentType): \Illuminate\Http\JsonResponse
    {
        $equipmentType->delete();
        return response()->json(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
