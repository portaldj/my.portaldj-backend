<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Http\Resources\CityResource;
use App\Models\City;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CityController extends Controller
{
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return CityResource::collection(City::with('country')->get());
    }

    public function store(StoreCityRequest $request): CityResource
    {
        $city = City::create($request->validated());
        return new CityResource($city->load('country'));
    }

    public function show(City $city): CityResource
    {
        return new CityResource($city->load('country'));
    }

    public function update(UpdateCityRequest $request, City $city): CityResource
    {
        $city->update($request->validated());
        return new CityResource($city->load('country'));
    }

    public function destroy(City $city): \Illuminate\Http\JsonResponse
    {
        $city->delete();
        return response()->json(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
