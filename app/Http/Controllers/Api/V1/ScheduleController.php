<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     * Muestra la agenda del usuario autenticado.
     */
    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $schedules = $request->user()->schedules()->with('club')->get();
        return ScheduleResource::collection($schedules);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScheduleRequest $request): ScheduleResource
    {
        Gate::authorize('create', Schedule::class);

        $schedule = $request->user()->schedules()->create($request->validated());

        return new ScheduleResource($schedule->load('club'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule): ScheduleResource
    {
        Gate::authorize('view', $schedule);
        return new ScheduleResource($schedule->load('club'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScheduleRequest $request, Schedule $schedule): ScheduleResource
    {
        Gate::authorize('update', $schedule);

        $schedule->update($request->validated());

        return new ScheduleResource($schedule->load('club'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('delete', $schedule);

        $schedule->delete();

        return response()->json(null, 204);
    }
}
