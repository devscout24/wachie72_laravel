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

                // Convert main_image to full URLs
                $item->main_image = collect($item->main_image)->map(function ($img) {
                    return asset($img);
                });

                // Convert multiple_image to full URLs
                $item->multiple_image = collect($item->multiple_image)->map(function ($img) {
                    return asset($img);
                });

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

        // --- FIX: Decode JSON correctly ---
        $mainImages = is_string($property->main_image)
            ? json_decode($property->main_image, true)
            : ($property->main_image ?? []);

        $multipleImages = is_string($property->multiple_image)
            ? json_decode($property->multiple_image, true)
            : ($property->multiple_image ?? []);

        // Convert to full URL
        $property->main_image = collect($mainImages)->map(fn($img) => asset($img));

        $property->multiple_image = collect($multipleImages)->map(fn($img) => asset($img));

        return response()->json([
            'success' => true,
            'data' => $property,
            'message' => 'Property retrieved successfully'
        ]);
    }



}
