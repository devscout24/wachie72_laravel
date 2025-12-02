<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function create()
    {
        return view('admin.team.create');
    }

    public function store(Request $request)
    {

        // dd($request->all());

        $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'bio' => 'nullable|string',
        ]);

        $team = new Team();
        $team->name = $request->name;
        $team->designation = $request->designation;
        $team->bio = $request->bio;
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images/teams'), $imageName);
            $team->image = 'images/teams/' . $imageName;
        }

        $team->is_active = $request->has('is_active') ? 1 : 0;

        $team->save();

        return redirect()->route('admin.team.index')->with('success', 'Team member added successfully.');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $teams = Team::latest()->get();

            return datatables()->of($teams)
                ->addIndexColumn()
                ->addColumn('image', function ($team) {
                    return '<img src="' . asset($team->image) . '" width="50" height="50">';
                })
                ->addColumn('status', function ($team) {
                    return $team->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function ($team) {
                    return '
                    <a href="' . route('admin.team.edit', $team->id) . '" 
                        class="btn btn-warning btn-sm">Edit</a>

                    <a href="' . route('admin.team.delete', $team->id) . '" 
                        class="btn btn-danger btn-sm">Delete</a>
                ';
                })
                ->rawColumns(['image', 'status', 'action'])
                ->make(true);
        }

        return view('admin.team.index');
    }

    public function edit($id)
    {
        $team = Team::findOrFail($id);
        return view('admin.team.edit', compact('team'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'bio' => 'nullable|string',
        ]);

        $team = Team::findOrFail($id);
        $team->name = $request->name;
        $team->designation = $request->designation;
        $team->bio = $request->bio;
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images/teams'), $imageName);
            $team->image = 'images/teams/' . $imageName;
        }

        $team->is_active = $request->has('is_active') ? 1 : 0;

        $team->save();

        return redirect()->route('admin.team.index')->with('success', 'Team member updated successfully.');
    }

    public function destroy($id)
    {
        $team = Team::findOrFail($id);
        $team->delete();

        return redirect()->route('admin.team.index')->with('success', 'Team member deleted successfully.');
    }
}
