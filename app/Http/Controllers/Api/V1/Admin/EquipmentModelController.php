<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEquipmentModelRequest;
use App\Http\Requests\Admin\UpdateEquipmentModelRequest;
use App\Http\Resources\EquipmentModelResource;
use App\Models\EquipmentModel;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class EquipmentModelController extends Controller
{
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return EquipmentModelResource::collection(EquipmentModel::with('equipmentBrand.equipmentType')->get());
    }

    public function store(StoreEquipmentModelRequest $request): EquipmentModelResource
    {
        return new EquipmentModelResource(EquipmentModel::create($request->validated()));
    }

    public function show(EquipmentModel $equipmentModel): EquipmentModelResource
    {
        return new EquipmentModelResource($equipmentModel->load('equipmentBrand.equipmentType'));
    }

    public function update(UpdateEquipmentModelRequest $request, EquipmentModel $equipmentModel): EquipmentModelResource
    {
        $equipmentModel->update($request->validated());
        return new EquipmentModelResource($equipmentModel);
    }

    public function destroy(EquipmentModel $equipmentModel): \Illuminate\Http\JsonResponse
    {
        $equipmentModel->delete();
        return response()->json(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
