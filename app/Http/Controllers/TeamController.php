<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $teams = Team::latest()->get();

            return DataTables::of($teams)
                ->addIndexColumn()
                ->addColumn('image', function ($team) {
                    return '<img src="' . asset($team->image) . '" width="50" height="50" style="object-fit:cover;border-radius:5px;">';
                })
                ->addColumn('status', function ($team) {
                    return $team->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function ($team) {
                    return '
                        <a href="' . route('admin.team.edit', $team->id) . '" class="btn btn-warning btn-sm">Edit</a>

                        <form action="' . route('admin.team.delete', $team->id) . '" method="POST" style="display:inline;">
                            '.csrf_field().method_field("DELETE").'
                            <button type="submit" class="btn btn-danger btn-sm delete-button">Delete</button>
                        </form>
                    ';
                })
                ->rawColumns(['image', 'status', 'action'])
                ->make(true);
        }

        return view('admin.team.index');
    }

    public function create()
    {
        return view('admin.team.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'image'       => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'bio'         => 'required|string',
            'is_active'   => 'required',
        ]);

        // ensure folder exists
        if (!file_exists(public_path('images/teams'))) {
            mkdir(public_path('images/teams'), 0777, true);
        }

        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('images/teams'), $imageName);

        Team::create([
            'name'        => $request->name,
            'designation' => $request->designation,
            'bio'         => $request->bio,
            'image'       => 'images/teams/' . $imageName,
            'is_active'   => $request->is_active,
        ]);

        return redirect()->route('admin.team.index')->with('success', 'Team member added successfully.');
    }

    public function edit($id)
    {
        $team = Team::findOrFail($id);
        return view('admin.team.edit', compact('team'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'bio'         => 'required|string',
            'is_active'   => 'required',
        ]);

        $team = Team::findOrFail($id);

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images/teams'), $imageName);
            $team->image = 'images/teams/' . $imageName;
        }

        $team->name = $request->name;
        $team->designation = $request->designation;
        $team->bio = $request->bio;
        $team->is_active = $request->is_active;
        $team->save();

        return redirect()->route('admin.team.index')->with('success', 'Team member updated successfully.');
    }

    public function delete($id)
    {
        Team::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Team member deleted successfully.');
    }
}

