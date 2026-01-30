<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Notifications\GeneralNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

use App\Models\Announcement;

class NotificationController extends Controller
{
    /**
     * Show the form for creating a new notification and list history.
     */
    public function create()
    {
        return Inertia::render('Admin/Notifications/Create', [
            'announcements' => Announcement::with('targetUser:id,name')
                ->latest()
                ->paginate(10)
        ]);
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

        // Create Announcement Record
        $announcement = Announcement::create([
            'user_id' => auth()->id(),
            'message' => $request->message,
            'target_user_id' => $request->user_id,
        ]);

        $this->dispatchNotification($announcement);

        return redirect()->route('admin.notifications.create')->with('success', 'Notification sent successfully.');
    }

    /**
     * Resend an existing notification.
     */
    public function resend(Announcement $announcement)
    {
        $this->dispatchNotification($announcement);

        // Update timestamp or create new record? 
        // For simple "Resend", we just dispatch again. 
        // If we want to track it as a new event in history, we should replicate it.
        // Let's replicate to keep history clear.

        Announcement::create([
            'user_id' => auth()->id(),
            'message' => $announcement->message,
            'target_user_id' => $announcement->target_user_id,
        ]);

        return back()->with('success', 'Notification resent successfully.');
    }

    /**
     * Helper to dispatch the notification.
     */
    protected function dispatchNotification(Announcement $announcement)
    {
        if ($announcement->target_user_id) {
            $user = User::findOrFail($announcement->target_user_id);
            $user->notify(new GeneralNotification($announcement->message));
        } else {
            $users = User::all();
            Notification::send($users, new GeneralNotification($announcement->message));
        }
    }
}
