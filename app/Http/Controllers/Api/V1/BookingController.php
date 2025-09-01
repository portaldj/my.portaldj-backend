<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $bookings = Booking::where('guest_user_id', $request->user()->id)
            ->orWhere('dj_user_id', $request->user()->id)
            ->with(['guest', 'dj'])
            ->latest()
            ->paginate();

        return BookingResource::collection($bookings);
    }

    public function store(StoreBookingRequest $request): BookingResource
    {
        $booking = Booking::create(array_merge(
            $request->validated(),
            ['guest_user_id' => $request->user()->id]
        ));
        return new BookingResource($booking);
    }

    public function show(Booking $booking): BookingResource
    {
        Gate::authorize('view', $booking);
        return new BookingResource($booking->load(['guest', 'dj']));
    }

    public function update(UpdateBookingRequest $request, Booking $booking): BookingResource
    {
        Gate::authorize('update', $booking);

        if ($request->input('status') === Booking::STATUS_ACCEPTED) {
            $this->bookingService->acceptBooking($booking);
        } else {
            $booking->update($request->validated());
        }

        return new BookingResource($booking);
    }

    public function destroy(Booking $booking): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('delete', $booking);
        $booking->delete();
        return response()->json(null, 204);
    }
}
