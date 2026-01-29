<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Inertia\Inertia;

class EventController extends Controller
{
    /**
     * Display the authenticated user's calendar.
     */
    public function index(Request $request)
    {
        return Inertia::render('Calendar/Index', [
            'events' => $request->user()->events()->orderBy('start')->get(),
        ]);
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start' => 'required|date',
            'end' => 'nullable|date|after_or_equal:start',
            'url' => 'nullable|url',
            'is_public' => 'boolean',
            'promote' => 'boolean',
            'press_release' => 'nullable|string|required_if:promote,true',
        ]);

        $event = $request->user()->events()->create($validated);

        if ($request->boolean('promote') && $request->filled('press_release')) {
            $event->bookingPromotion()->create([
                'status' => 'pending',
                'press_release' => $request->press_release,
            ]);

            // Notify Admin
            \Illuminate\Support\Facades\Mail::to('pro@portaldj.cl')->send(
                new \App\Mail\BookingPromotionRequested($event, $request->user(), $request->press_release)
            );
        }

        return redirect()->back()->with('success', __('Event created successfully.'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, Event $event)
    {
        if ($request->user()->id !== $event->user_id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start' => 'required|date',
            'end' => 'nullable|date|after_or_equal:start',
            'url' => 'nullable|url',
            'is_public' => 'boolean',
            'promote' => 'boolean',
            'press_release' => 'nullable|string|required_if:promote,true',
        ]);

        $event->update($validated);

        if ($request->boolean('promote') && $request->filled('press_release')) {
            // Check if already promoted to avoid duplicates or re-submission logic
            if (!$event->bookingPromotion) {
                $event->bookingPromotion()->create([
                    'status' => 'pending',
                    'press_release' => $request->press_release,
                ]);

                // Notify Admin
                \Illuminate\Support\Facades\Mail::to('pro@portaldj.cl')->send(
                    new \App\Mail\BookingPromotionRequested($event, $request->user(), $request->press_release)
                );
            }
        }

        return redirect()->back()->with('success', __('Event updated successfully.'));
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event)
    {
        if (request()->user()->id !== $event->user_id) {
            abort(403);
        }

        $event->delete();

        return redirect()->back()->with('success', __('Event deleted successfully.'));
    }

    /**
     * API: Get public events for a specific user.
     * Used for the public profile calendar.
     */
    public function publicEvents($username)
    {
        $user = \App\Models\User::whereHas('profile', function ($q) use ($username) {
            $q->where('username', $username);
        })->firstOrFail();

        return response()->json(
            $user->events()
                ->where('is_public', true)
                ->get()
                ->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'title' => $event->title, // Or "Occupied" if private? For now public is title.
                        'start' => $event->start,
                        'end' => $event->end,
                        'description' => $event->description,
                        'url' => $event->url,
                        'className' => 'bg-brand-primary border-brand-primary text-white',
                    ];
                })
        );
    }
}
