<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BookingController extends Controller
{
    public function __construct(protected BookingService $bookingService)
    {
    }
    public function accept(Request $request, Booking $booking): BookingResource
    {
        Gate::authorize('update', $booking);

        $this->bookingService->acceptBooking($booking);

        return new BookingResource($booking->fresh());
    }
}
