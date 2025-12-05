<?php

namespace App\Http\Controllers\Web\backend\abdullah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Property;
use App\Models\User;
use App\Traits\apiresponse;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use App\Models\Amenity;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Property::query()->orderBy('id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()


                ->addColumn('id', function ($row) {
                    return $row->id;
                })

                ->addColumn('amenity_id', function ($row) {
                    return $row->amenities->pluck('name')->implode(', ');
                })

                ->addColumn('title', function ($row) {
                    return e($row->title);
                })

                ->addColumn('location', function ($row) {
                    return e($row->location);
                })

                ->addColumn('price', function ($row) {
                    return '$' . number_format($row->price, 2);
                })

                ->addColumn('cleaning_fee', function ($row) {
                    return '$' . number_format($row->cleaning_fee, 2);
                })

                ->addColumn('status', function ($row) {
                    return $row->status == 1 ? 'Active' : 'Inactive';
                })


                ->addColumn('multiple_image', function ($row) {
                    $multiImages = $row->multiple_image;
                    if (is_string($multiImages)) $multiImages = json_decode($multiImages, true);

                    if ($multiImages && is_array($multiImages) && count($multiImages) > 0) {
                        // Only the first image
                        return '<img src="' . asset($multiImages[0]) . '" style="width:80px;height:40px;object-fit:cover;">';
                    }
                    return 'No Image';
                })

                ->addColumn('description', function ($row) {
                    // Remove HTML tags and then limit to 50 characters
                    return Str::limit(strip_tags($row->description), 30);
                })

                ->addColumn('action', function ($row) {
                    return '
                    <a href="' . route('admin.property.edit', $row->id) . '" class="btn btn-sm btn-primary">Edit</a>
                    <a href="' . route('admin.property.show', $row->id) . '" class="btn btn-sm btn-info">Show</a>
                    <button data-id="' . $row->id . '" class="btn btn-sm btn-danger btn-delete">Delete</button>
                ';
                })

                ->rawColumns(['image', 'multiple_image', 'action'])
                ->make(true);
        }

        return view('admin.property.index');
    }




    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric',
            'amenity_id' => 'nullable|array', // optional
            'amenity_id.*' => 'exists:amenities,id', // validate each ID
            'cleaning_fee' => 'nullable|numeric',
            'multiple_image.*' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp',
        ]);

        $data = $request->except( 'multiple_image', 'amenity_id');
        $data['user_id'] = auth()->id() ?? 1;



        // Handle MULTIPLE IMAGES
        $multiImages = [];
        if ($request->hasFile('multiple_image')) {
            foreach ($request->file('multiple_image') as $file) {
                $filename = 'uploads/properties/' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/properties'), basename($filename));
                $multiImages[] = $filename;
            }
        }
        $data['multiple_image'] = $multiImages;

        // Create property
        $property = Property::create($data);

        if ($request->amenity_id) {
            $property->amenities()->sync($request->amenity_id);
        }


        return redirect()->route('admin.property.index')->with('success', 'Property Created');
    }



    public function create()
    {
        $amenities = Amenity::where('status', 1)->orderBy('name')->get();
        return view('admin.property.create', compact('amenities'));
    }


    public function show($id)
    {
        // Eager load amenities
        $property = Property::with('amenities')->findOrFail($id);


        $property->multiple_image = is_string($property->multiple_image)
            ? json_decode($property->multiple_image, true)
            : ($property->multiple_image ?? []);

        return view('admin.property.show', compact('property'));
    }


    public function edit($id)
    {
        $property  = Property::findOrFail($id);
        $amenities = Amenity::where('status', 1)->orderBy('name')->get();
        return view('admin.property.edit', compact('property', 'amenities'));
    }


    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric',
            'cleaning_fee' => 'nullable|numeric',
            'multiple_image.*' => 'nullable|image',
            'amenity_id' => 'nullable|array',
            'amenity_id.*' => 'integer|exists:amenities,id',
        ]);

        $data = $request->except(['main_image', 'multiple_image', 'amenity_id']);
        $property->update($data);


        // Update multiple images
        if ($request->hasFile('multiple_image')) {
            $multiArr = $property->multiple_image ?? [];
            foreach ($request->file('multiple_image') as $file) {
                $name = 'uploads/properties/' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/properties'), basename($name));
                $multiArr[] = $name;
            }
            $property->multiple_image = $multiArr;
        }

        // Sync amenities
        $property->amenities()->sync($request->amenity_id ?? []);

        $property->save();

        return redirect()->route('admin.property.index')->with('success', 'Property updated successfully');
    }



    public function destroy($id)
    {
        Property::findOrFail($id)->delete();
        return response()->json(['message' => "Deleted Successfully"]);
    }

    //     public function toggleStatus($id)
    // {
    //     $news = News::find($id);
    //     if (!$news) {
    //         return response()->json(['success' => false]);
    //     }

    //     $news->status = $news->status == 1 ? 0 : 1;
    //     $news->save();

    //     return response()->json([
    //         'success' => true,
    //         'status'  => $news->status
    //     ]);
    // }
}
