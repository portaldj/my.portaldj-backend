<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\MusicGenre;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class MusicGenreController extends Controller
{
    public function index()
    {
        return MusicGenreResource::collection(MusicGenre::all());
    }

    public function store(StoreMusicGenreRequest $request): MusicGenreResource
    {
        $genre = MusicGenre::create($request->validated());
        return new MusicGenreResource($genre);
    }

    public function show(MusicGenre $musicGenre): MusicGenreResource
    {
        return new MusicGenreResource($musicGenre);
    }

    public function update(UpdateMusicGenreRequest $request, MusicGenre $musicGenre): MusicGenreResource
    {
        $musicGenre->update($request->validated());
        return new MusicGenreResource($musicGenre);
    }

    public function destroy(MusicGenre $musicGenre): \Illuminate\Http\JsonResponse
    {
        $musicGenre->delete();
        return response()->json(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
