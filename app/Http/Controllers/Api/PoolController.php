<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PoolService;
use Illuminate\Http\Request;

class PoolController extends Controller
{
    protected $poolService;

    public function __construct(PoolService $poolService)
    {
        $this->poolService = $poolService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['query', 'genre_id', 'bpm_min', 'bpm_max']);
        return $this->poolService->searchSongs($filters);
    }

    public function download(Request $request, $songId)
    {
        try {
            $path = $this->poolService->downloadSong($request->user(), $songId);
            return response()->download($path);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            abort($e->getStatusCode(), $e->getMessage());
        }
    }
}
