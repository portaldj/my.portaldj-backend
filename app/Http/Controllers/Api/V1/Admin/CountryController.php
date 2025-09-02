<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCountryRequest;
use App\Http\Requests\Admin\UpdateCountryRequest;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CountryController extends Controller
{
    public function index()
    {
        return CountryResource::collection(Country::all());
    }

    public function store(StoreCountryRequest $request): CountryResource
    {
        $country = Country::create($request->validated());
        return new CountryResource($country);
    }

    public function show(Country $country): CountryResource
    {
        return new CountryResource($country);
    }

    public function update(UpdateCountryRequest $request, Country $country): CountryResource
    {
        $country->update($request->validated());
        return new CountryResource($country);
    }

    public function destroy(Country $country): \Illuminate\Http\JsonResponse
    {
        $country->delete();
        return response()->json(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
