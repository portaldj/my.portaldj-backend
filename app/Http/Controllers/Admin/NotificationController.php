<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Notifications\GeneralNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Show the form for creating a new notification.
     */
    public function create()
    {
        return Inertia::render('Admin/Notifications/Create');
    }

    /**
     * Store a newly created notification in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id', // Optional: send to specific user
        ]);

        if ($request->user_id) {
            $user = User::findOrFail($request->user_id);
            $user->notify(new GeneralNotification($request->message));
        } else {
            // Send to ALL users (Dispatch via Job recommended for production, but direct for now)
            $users = User::all();
            Notification::send($users, new GeneralNotification($request->message));
        }

        return redirect()->route('admin.notifications.create')->with('success', 'Notification sent successfully.');
    }
}
