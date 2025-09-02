<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSocialNetworkRequest;
use App\Http\Requests\Admin\UpdateSocialNetworkRequest;
use App\Http\Resources\SocialNetworkResource;
use App\Models\SocialNetwork;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class SocialNetworkController extends Controller
{
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return SocialNetworkResource::collection(SocialNetwork::all());
    }

    public function store(StoreSocialNetworkRequest $request): SocialNetworkResource
    {
        return new SocialNetworkResource(SocialNetwork::create($request->validated()));
    }

    public function show(SocialNetwork $socialNetwork): SocialNetworkResource
    {
        return new SocialNetworkResource($socialNetwork);
    }

    public function update(UpdateSocialNetworkRequest $request, SocialNetwork $socialNetwork): SocialNetworkResource
    {
        $socialNetwork->update($request->validated());
        return new SocialNetworkResource($socialNetwork);
    }

    public function destroy(SocialNetwork $socialNetwork): \Illuminate\Http\JsonResponse
    {
        $socialNetwork->delete();
        return response()->json(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
