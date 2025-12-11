<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Property;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Traits\apiresponse;

class BookingController extends Controller
{
    use apiresponse;



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after:start_date',
            'adults'      => 'required|integer|min:1',
            'children'    => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return $this->error(
                $validator->errors(),
                'Validation failed',
                422
            );
        }

        $property = Property::findOrFail($request->property_id);

        // Calculate nights
        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);
        $nights    = $startDate->diffInDays($endDate);

        if ($nights < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum 1 night required.'
            ], 422);
        }

        // Price calculations
        $pricePerNight  = $property->price;         // e.g., 350
        $priceTotal     = $pricePerNight * $nights; // e.g., 1050
        $cleaning_fee   = $property->cleaning_fee;  // e.g., 200
        $booking_fee    = $priceTotal * 0.045;      // 4.5%
        $total          = $priceTotal + $cleaning_fee + $booking_fee;

        /** STORE THE BOOKING */
        $booking = Booking::create([
            'property_id' => $request->property_id,
            'user_id'     => auth()->id(),
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'adults'      => $request->adults,
            'children'    => $request->children ?? 0,
        ]);

        // JSON response matching your screenshot format
        $priceKey = 'price_' . $nights . '_nights';

        return response()->json([
            'success' => true,
            'data' => [
                'booking_id'     => $booking->id,
                $priceKey        => 'A$ ' . number_format($pricePerNight, 2) . ' × ' . $nights . ' night',
                'cleaning_fee'   => 'A$ ' . number_format($cleaning_fee, 2),
                'booking_fee'    => 'A$ ' . number_format($booking_fee, 2),
                'total'          => 'A$ ' . number_format($total, 2),
            ]
        ], 201);
    }




    /**
     * Get all bookings for admin
     */
    public function getAll()
    {
        $bookings = Booking::with('property:id,title')->get();

        return response()->json([
            'success' => true,
            'data'    => $bookings
        ]);
    }


    /**
     * Booking Summary (for logged-in user)
     */
    public function summary()
    {
        // Check login
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = auth()->user();

        // Fetch bookings for only this user
        $bookings = Booking::where('user_id', $user->id)
            ->with('property:id,title,price,image')
            ->get();

        $totalSpent = $bookings->sum('total_price');

        return response()->json([
            'success' => true,
            'data' => [
                'total_bookings' => $bookings->count(),
                'total_spent'    => $totalSpent,
                'bookings'       => $bookings
            ]
        ]);
    }
}
