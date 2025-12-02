<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'location',
        'rating',
        'total_reviews',
        'price',
        'bedrooms',
        'bathrooms',
        'max_guests',
        'amenity_id',
        'description',
        'main_image',
        'multiple_image'
    ];

    protected $casts = [
        'main_image' => 'array',
        'multiple_image' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'property_amenity', 'property_id', 'amenity_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
