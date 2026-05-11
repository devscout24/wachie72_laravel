<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Property;
use App\Traits\apiresponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

use function PHPSTORM_META\map;

class PropertyController extends Controller
{
    use apiresponse;

    public function index()
    {


        $token = config('services.beds24.token');

        if (!$token) {
            return response()->json([
                'error' => 'Beds24 token not found'
            ]);
        }

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'token'  => $token,
        ])->withQueryParameters([
            'includeLanguages'    => 'all',
            'includeTexts'        => 'all',
            'includePictures'     => true,
            'includeOffers'       => true,
            'includePriceRules'   => true,
            'includeUpsellItems'  => true,
            'includeAllRooms'     => true,
            'includeUnitDetails'  => true,
        ])->get('https://beds24.com/api/v2/properties');

        // dd($response->json());

        // Convert response to collection and map it
        $properties = collect($response->json()['data'])->map(function ($item) {
            // Get property level texts (English)
            $propertyTexts = isset($item['texts'][0]) ? $item['texts'][0] : null;

            // Get the first room type for price
            $firstRoom = isset($item['roomTypes'][0]) ? $item['roomTypes'][0] : null;

            return [
                'id'            => $item['id'] ?? null,
                'name'          => $item['name'] ?? null,
                'address'       => $item['address'] ?? null,
                'price'         => $firstRoom['minPrice'] ?? null,
            ];
        });

        return response()->json([
            'status' => $response->status(),
            'data'   => $properties,
            'message' => 'Property retrieved successfully'
        ]);
    }

    public function getone($id)
    {
        $token = config('services.beds24.token');

        if (!$token) {
            return response()->json([
                'error' => 'Beds24 token not found'
            ]);
        }

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'token'  => $token,
        ])->withQueryParameters([
            'id'                  => 313566,
            'includeLanguages'    => 'all',
            'includeTexts'        => 'all',
            'includePictures'     => true,
            'includeOffers'       => true,
            'includePriceRules'   => true,
            'includeUpsellItems'  => true,
            'includeAllRooms'     => true,
            'includeUnitDetails'  => true,
            'roomId'              => 653037,
        ])->get('https://beds24.com/api/v2/properties');
        // Convert response to collection and map it
        $properties = collect($response->json()['data'])->map(function ($item) {
            // Get property level texts (English)
            $propertyTexts = isset($item['texts'][0]) ? $item['texts'][0] : null;

            // Get the first room type for price
            $firstRoom = isset($item['roomTypes'][0]) ? $item['roomTypes'][0] : null;

            // Get property level upsell items
            $propertyUpsells = $item['upsellItems'] ?? [];
            $obligatoryUpsells = collect($propertyUpsells)
                ->filter(function ($upsell) {
                    return ($upsell['type'] ?? '') === 'obligatory';
                })
                ->map(function ($upsell) use ($propertyTexts) {
                    $index = $upsell['index'] ?? 0;
                    return [
                        'name' => $propertyTexts['upsellItemName' . $index] ?? null,
                        'amount' => $upsell['amount'] ?? null,
                        'per' => $upsell['per'] ?? null,
                        'period' => $upsell['period'] ?? null,
                        // 'vat' => $upsell['vat'] ?? null
                    ];
                })
                ->values()
                ->toArray();

            $firstObligatoryUpsell = collect($propertyUpsells)->firstWhere('type', 'obligatory');

            return [
                'id'            => $item['id'] ?? null,
                'name'          => $item['name'] ?? null,
                'address'       => $item['address'] ?? null,
                'latitude'      => isset($item['latitude']) ? number_format((float)$item['latitude'], 8, '.', '') : null,
                'longitude'     => isset($item['longitude']) ? number_format((float)$item['longitude'], 8, '.', '') : null,
                'booking_fee_percentage' => $item['bookingRules']['vatRatePercentage'] ?? null,
                'price'         => $firstRoom['minPrice'] ?? null,
                'maxPeople'         => $firstRoom['maxPeople'] ?? null,
                'obligatory_upsells' => $obligatoryUpsells,
                'amenities'     => $item['featureCodes'] ?? null,
                'property_info'         => $propertyTexts['propertyDescription1'] ?? null,  // Property description 1
                'local_area'         => $propertyTexts['propertyDescription2'] ?? null,  // Property description 2
            ];
        });

        return response()->json([
            'status' => $response->status(),
            'data'   => $properties,
        ]);
    }

    public function booking(Request $request)
    {
        $token = config('services.beds24.token');

        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'Beds24 token not found'
            ], 400);
        }

        // ✅ Get full booking array from request
        $payload = $request->all();

        if (empty($payload)) {
            return response()->json([
                'status' => false,
                'message' => 'Booking data required'
            ], 422);
        }

        // ✅ Send booking to Beds24
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'Content-Type' => 'application/json',
            'token' => $token,
        ])->post(
            'https://beds24.com/api/v2/bookings',
            $payload
        );

        $result = $response->json();

        // ✅ Safe response handling
        if (!$response->successful()) {
            return response()->json([
                'status' => false,
                'message' => $result['message'] ?? 'Booking failed',
                'response' => $result
            ], 400);
        }

        return response()->json([
            'status' => true,
            'message' => 'Booking created successfully',
            'data' => $result
        ]);
    }


    public function getBookings()
    {
        $token = config('services.beds24.token');

        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'Beds24 token not found'
            ], 400);
        }

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'token'  => $token,
        ])->get('https://beds24.com/api/v2/bookings');

        $result = $response->json();

        // ✅ Safe check
        $bookings = data_get($result, 'data', []);

        $formattedBookings = collect($bookings)->map(function ($booking) {
            return [
                'booking_id' => $booking['id'] ?? null,
                'roomId'     => $booking['roomId'] ?? null,
                'name'       => ($booking['firstName'] ?? '') . ' ' . ($booking['lastName'] ?? ''),
                'email'      => $booking['email'] ?? null,
                'arrival'    => $booking['arrival'] ?? null,
                'departure'  => $booking['departure'] ?? null,
                'status'     => $booking['status'] ?? null,
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Bookings retrieved successfully',
            'data'    => $formattedBookings
        ]);
    }
}
