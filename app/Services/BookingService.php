<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function acceptBooking(Booking $booking): bool
    {
        if ($booking->status !== Booking::STATUS_PENDING) {
            return false;
        }

        return DB::transaction(function () use ($booking) {
            $booking->update(['status' => Booking::STATUS_ACCEPTED]);

            Schedule::create([
                'user_id' => $booking->dj_user_id,
                'club_id' => 1, // Assuming a generic 'Private Event' club, this should be improved.
                'event_name' => "Booking: " . $booking->event_details,
                'start_time' => $booking->requested_date,
                'end_time' => $booking->requested_date->addHours(4), // Assuming a 4-hour event
            ]);

            return true;
        });
    }
}
