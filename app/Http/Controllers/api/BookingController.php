<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Property;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BookingController extends Controller
{
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'property_id' => 'required|exists:properties,id',
    //         'user_id'     => 'required|exists:users,id',
    //         'start_date'  => 'required|date',
    //         'end_date'    => 'required|date|after:start_date',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'errors'  => $validator->errors()
    //         ], 422);
    //     }

    //     // Get property
    //     $property = Property::findOrFail($request->property_id);

    //     // Calculate days
    //     $startDate = Carbon::parse($request->start_date);
    //     $endDate   = Carbon::parse($request->end_date);
    //     $days      = $startDate->diffInDays($endDate);

    //     // 🔥 Minimum 3 days required
    //     if ($days < 3) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Minimum booking duration is 3 days.'
    //         ], 422);
    //     }

    //     // booking fee logic can be added here in future
    //     $bookingFee = ($property->price * $days) * 0.045; // 4.5% booking fee
    //     // Calculate total price
    //     $totalPrice = ($days * $property->price) + $property->cleaning_fee + $bookingFee;

    //     // Create booking
    //     $booking = Booking::create([
    //         'property_id' => $request->property_id,
    //         'user_id'     => $request->user_id,
    //         'start_date'  => $request->start_date,
    //         'end_date'    => $request->end_date,
    //         'total_price' => $totalPrice,
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'data'    => $booking,
    //         'message' => 'Booking created successfully'
    //     ], 201);
    // }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $property = Property::findOrFail($request->property_id);

        // Calculate days
        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);
        $days      = $startDate->diffInDays($endDate);

        if ($days < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum 1 night required.'
            ], 422);
        }

        // Price calculations
        $pricePerNight   = $property->price;
        $price_x_nights  = $pricePerNight * $days;
        $cleaning_fee    = $property->cleaning_fee;
        $booking_fee     = $price_x_nights * 0.045;  // 4.5%
        $total           = $price_x_nights + $cleaning_fee + $booking_fee;

        // Create booking
        Booking::create([
            'property_id' => $request->property_id,
            'user_id'     => $request->user_id ?? null,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'total_price' => $total,
        ]);

        // Return minimal fields only
        return response()->json([
            'success' => true,
            'data' => [
                'price_x_nights' => $price_x_nights,
                'cleaning_fee'   => $cleaning_fee,
                'booking_fee'    => $booking_fee,
                'total'          => $total,
            ]
        ], 201);
    }



    public function getAll()
    {
        $bookings = Booking::with('property:id')->get();

        return response()->json([
            'success' => true,
            'data'    => $bookings,
            'message' => 'Bookings retrieved successfully'
        ]);
    }
}
