<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class TeamController extends Controller
{
    use apiresponse;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Team::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('is_active', function ($row) {
                    return $row->is_active ? 'Active' : 'Inactive';
                })
                ->addColumn('image', function ($row) {
                    if ($row->image) {
                        $img = asset($row->image);
                        return '<img src="' . $img . '" style="width:80px;height:80px;object-fit:cover;border-radius:50%;">';
                    }
                    return 'No Image';
                })
                ->addColumn('designation', function ($row) {
                    return $row->designation;
                })


                ->addColumn('bio', function ($row) {
                    return Str::limit(strip_tags($row->bio), 80);
                })

                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-primary btn-sm edit">Edit</a>';
                    $btn = $btn . ' <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm delete-btn">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['is_active', 'image', 'action'])
                ->make(true);
        }
        return view('admin.team.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $team = new Team();
        $team->name = $request->name;
        $team->designation = $request->designation;
        $team->bio = $request->bio;

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . ($request->name) . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('uploads/teams'), $imageName);
            $team->image = 'uploads/teams/' . $imageName;
        }

        $team->is_active = $request->has('is_active') ? true : false;
        $team->save();

        return redirect()->route('admin.team.index')->with('success', 'Team Created');
    }

    public function create()
    {
        return view('admin.team.create');
    }


    public function edit($id)
    {
        $team = Team::find($id);
        if (!$team) {
            return $this->error(false, 'Team member not found', null, 404);
        }
        return $this->success(true, 'Team member retrieved successfully', $team, 200);
    }

    public function update(Request $request, $id)
    {
        $team = Team::find($id);
        if (!$team) {
            return $this->error(false, 'Team member not found', null, 404);
        }

        $team->name = $request->name;
        $team->role = $request->role;

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . ($request->name) . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('uploads/teams'), $imageName);
            $team->image = 'uploads/teams/' . $imageName;
        }
        $team->save();

        return $this->success(true, 'Team member updated successfully', $team, 200);
    }

    public function destroy($id)
    {
        try {
            $team = Team::find($id);

            if (!$team) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team member not found'
                ], 404);
            }

            // Safe image delete
            if (!empty($team->image)) {
                $imagePath = public_path($team->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $team->delete();

            return response()->json([
                'success' => true,
                'message' => 'Team member deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
