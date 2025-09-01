<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SkillResource;
use App\Models\Skill;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class SkillController extends Controller
{
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return SkillResource::collection(Skill::all());
    }

    public function store(StoreSkillRequest $request): SkillResource
    {
        $skill = Skill::create($request->validated());
        return new SkillResource($skill);
    }

    public function show(Skill $skill): SkillResource
    {
        return new SkillResource($skill);
    }

    public function update(UpdateSkillRequest $request, Skill $skill): SkillResource
    {
        $skill->update($request->validated());
        return new SkillResource($skill);
    }

    public function destroy(Skill $skill): \Illuminate\Http\JsonResponse
    {
        $skill->delete();
        return response()->json(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
