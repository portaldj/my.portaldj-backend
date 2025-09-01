<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserController extends Controller
{
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return UserResource::collection(User::with('roles', 'city')->paginate(15));
    }

    public function store(StoreUserRequest $request): UserResource
    {
        $user = User::create($request->validated());
        $user->roles()->sync($request->input('roles', []));
        return new UserResource($user);
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user->load('roles', 'city', 'profileTypes', 'skills'));
    }

    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $user->update($request->validated());
        if ($request->has('roles')) {
            $user->roles()->sync($request->input('roles'));
        }
        return new UserResource($user);
    }

    public function destroy(User $user): \Illuminate\Http\JsonResponse
    {
        $user->delete();
        return response()->json(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
