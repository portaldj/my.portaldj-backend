<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Announcement;
use Inertia\Inertia;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of global announcements.
     */
    public function index()
    {
        return Inertia::render('Announcements/Index', [
            'announcements' => Announcement::whereNull('target_user_id')
                ->latest()
                ->paginate(10)
        ]);
    }
}
