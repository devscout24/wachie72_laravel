<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function getAll()
    {
        $teams = Team::all()->map(function ($team) {
            return [
                'id'          => $team->id,
                'name'        => $team->name,
                'designation' => $team->designation,
                'bio'         => $team->bio,
                'is_active'   => $team->is_active,
                'image'       => $team->image ? asset($team->image) : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $teams
        ], 200);
    }


    public function getOne($id)
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'Team member not found.'
            ], 404);
        }

        $team = [
            'id'          => $team->id,
            'name'        => $team->name,
            'designation' => $team->designation,
            'bio'         => $team->bio,
            'is_active'   => $team->is_active,
            'image'       => $team->image ? asset($team->image) : null,
        ];

        return response()->json([
            'success' => true,
            'data'    => $team
        ], 200);
    }
}
