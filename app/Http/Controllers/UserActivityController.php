<?php

namespace App\Http\Controllers;

use App\Services\ActivityService;
use Illuminate\Http\Request;

class UserActivityController extends Controller
{
    public function index(Request $request, ActivityService $activityService)
    {
        return \Inertia\Inertia::render('Profile/Activity', [
            'activities' => $activityService->getUserActivity($request->user()),
        ]);
    }
}
