<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeController extends Controller
{

    public function checkout($bookingId)
    {
         User::where('id', 1)->first();
        Property::where('id', 1)->first();
        // Booking::where('id', 1)->first();

        $booking = Booking::findOrFail($bookingId);

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'usd',
                    'product_data' => [
                        'name' => 'Hotel Booking #' . $booking->id,
                    ],
                    'unit_amount'  => $booking->total_price * 100, // Convert to cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',

            // User will return back after payment
            'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => url('/booking-failed'),
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        return view('payment.success');
    }

    public function paymentBooking()
    {
        User::where('id', 1)->first();
        Property::where('id', 1)->first();
        Booking::where('id', 1)->first();

        return response()->json(['message' => 'Stripe payment booking endpoint'], 200);
    }


    public function payment(Request $request)
    {
        return response()->json(['message' => 'Stripe payment endpoint'], 200);
    }
}
