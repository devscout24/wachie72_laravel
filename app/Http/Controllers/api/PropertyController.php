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
        $properties = Property::with(['amenities:id,name', 'images'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($item) {

                // ✅ MULTIPLE IMAGE FROM RELATION
                $item->multiple_image = $item->images->map(function ($img) {
                    return url($img->image);
                })->values();

                // ✅ STRIP TAG DESCRIPTION
                $item->description = strip_tags($item->description);

                // optional: hide relation
                unset($item->images);

                return $item;
            });

        return response()->json([
            'success' => true,
            'data'    => $properties,
            'message' => 'Properties retrieved successfully'
        ], 200);
    }




    public function getone($id)
    {
        $property = Property::with(['amenities:id,name', 'images'])->find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        }

        // ✅ MULTIPLE IMAGE FROM RELATION
        $property->multiple_image = $property->images->map(function ($img) {
            return url($img->image);
        })->values();

        // ✅ STRIP TAG DESCRIPTION
        $property->description = strip_tags($property->description);

        // optional: remove images relation from response
        unset($property->images);

        return response()->json([
            'success' => true,
            'data'    => $property,
            'message' => 'Property retrieved successfully'
        ], 200);
    }
}
