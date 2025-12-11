<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'property_id',
        'user_id',
        'start_date',
        'end_date',

        // Guests
        'adults',
        'children',

        // Customer Info
        'first_name',
        'last_name',
        'email',
        'phone',

        // Address
        'address',
        'city',
        'country',
        'postal_code',

        'comments',

        // Price
        'nightly_price',
        'cleaning_fee',
        'booking_fee',
        'total_price',

        // Payment
        'payment_gateway',
        'payment_status'
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
