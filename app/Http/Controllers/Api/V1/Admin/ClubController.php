<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClubResource;
use App\Models\Club;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ClubController extends Controller
{
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return ClubResource::collection(Club::with('city')->get());
    }

    public function store(StoreClubRequest $request): ClubResource
    {
        $club = Club::create($request->validated());
        return new ClubResource($club->load('city'));
    }

    public function show(Club $club): ClubResource
    {
        return new ClubResource($club->load('city'));
    }

    public function update(UpdateClubRequest $request, Club $club): ClubResource
    {
        $club->update($request->validated());
        return new ClubResource($club->load('city'));
    }

    public function destroy(Club $club): \Illuminate\Http\JsonResponse
    {
        $club->delete();
        return response()->json(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
