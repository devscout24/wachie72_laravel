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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'user_id'     => 'required|exists:users,id',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // Get property
        $property = Property::findOrFail($request->property_id);

        // Calculate days using Carbon
        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);
        $days      = $startDate->diffInDays($endDate);

        // Calculate total price
        $totalPrice = ($days * $property->price) + $property->cleaning_fee;

        // Create booking
        $booking = Booking::create([
            'property_id' => $request->property_id,
            'user_id'     => $request->user_id,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'total_price' => $totalPrice,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $booking,
            'message' => 'Booking created successfully'
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
