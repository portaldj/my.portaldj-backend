<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingPromotion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookingPromotionController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Promotions/Index', [
            'promotions' => BookingPromotion::with(['event.user'])
                ->where('status', 'pending')
                ->latest()
                ->paginate(10)
                ->through(fn($promotion) => [
                    'id' => $promotion->id,
                    'dj_name' => $promotion->event->user->name,
                    'event_title' => $promotion->event->title,
                    'event_date' => $promotion->event->start->format('Y-m-d H:i'),
                    'press_release' => $promotion->press_release,
                    'created_at' => $promotion->created_at->format('Y-m-d'),
                ]),
        ]);
    }

    public function approve(Request $request, BookingPromotion $bookingPromotion)
    {
        $validated = $request->validate([
            'blog_url' => 'required|url',
        ]);

        $bookingPromotion->update([
            'status' => 'approved',
            'blog_url' => $validated['blog_url'],
        ]);

        return redirect()->back()->with('success', 'Promotion approved successfully.');
    }

    public function reject(Request $request, BookingPromotion $bookingPromotion)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $bookingPromotion->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()->back()->with('success', 'Promotion rejected.');
    }
}
