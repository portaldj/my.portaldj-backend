<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function acceptBooking(Booking $booking): Booking
    {
        return DB::transaction(function () use ($booking) {
            $booking->update(['status' => 'accepted']);

            $booking->dj->schedules()->create([
                'event_name' => "Booking: {$booking->event_details}",
                'club_id' => 1,
                'start_time' => $booking->requested_date,
                'end_time' => $booking->requested_date->addHours(4),
            ]);

            return $booking;
        });
    }
}
