<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileTypeResource;
use App\Models\ProfileType;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ProfileTypeController extends Controller
{
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return ProfileTypeResource::collection(ProfileType::all());
    }

    public function store(StoreProfileTypeRequest $request): ProfileTypeResource
    {
        $profileType = ProfileType::create($request->validated());
        return new ProfileTypeResource($profileType);
    }

    public function show(ProfileType $profileType): ProfileTypeResource
    {
        return new ProfileTypeResource($profileType);
    }

    public function update(UpdateProfileTypeRequest $request, ProfileType $profileType): ProfileTypeResource
    {
        $profileType->update($request->validated());
        return new ProfileTypeResource($profileType);
    }

    public function destroy(ProfileType $profileType)
    {
        $profileType->delete();
        return response()->json(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
