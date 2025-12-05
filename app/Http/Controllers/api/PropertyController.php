<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Property;
use App\Traits\apiresponse;
use Illuminate\Support\Facades\Validator;


class PropertyController extends Controller
{
    use apiresponse;

    public function index()
    {
        $properties = Property::with('amenities:id,name')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($item) {

                // --- FIX: Decode if stored as JSON string ---
                $multipleImages = is_string($item->multiple_image)
                    ? json_decode($item->multiple_image, true)
                    : ($item->multiple_image ?? []);

                // --- Convert to full URL ---
                $item->multiple_image = collect($multipleImages)
                    ->map(fn($img) => asset('uploads/properties/' . basename($img)))
                    ->values();

                return $item;
            });

        return response()->json([
            'success' => true,
            'data' => $properties,
            'message' => 'Properties retrieved successfully'
        ]);
    }


    public function getone($id)
    {
        $property = Property::with('amenities:id,name')->find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        }

        // --- FIX JSON IMAGE FIELDS ---
        // Always decode if saved as string
        $multipleImages = is_string($property->multiple_image)
            ? json_decode($property->multiple_image, true)
            : ($property->multiple_image ?? []);

        // Convert each image path to full URL
        $property->multiple_image = collect($multipleImages)
            ->map(fn($img) => asset($img))
            ->values(); // reset index (optional)

        return response()->json([
            'success' => true,
            'data' => $property,
            'message' => 'Property retrieved successfully'
        ]);
    }
}
