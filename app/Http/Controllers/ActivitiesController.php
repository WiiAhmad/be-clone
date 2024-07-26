<?php

namespace App\Http\Controllers;

use App\Models\activities;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ActivitiesController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', ['except' => ['index', 'show']])
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return activities::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $fields = $request->validate([
            'title' => 'required',
            'desc' => 'required',
            'image' => 'nullable|image|mimes:png,jpg,jpeg',
            'date' => 'nullable|date'
        ]);

        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->storeAs('public/images', $imageName);
            $fields['image'] = $imageName; // Store only the image name
        }

        $activity = $user->activities()->create($fields);
        return ['activity' => $activity];
    }

    /**
     * Display the specified resource.
     */
    public function show($id) // Change activities $activities to $id if route model binding is not working as expected
    {
        // If using route model binding, ensure the route parameter and the variable name in the method signature match.
        // If not, manually retrieve the activity.
        $activity = activities::find($id); // Manually find the activity if route model binding is not working

        if ($activity) {
            return response()->json($activity);
        } else {
            return response()->json(['message' => 'Activity not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $activity = activities::find($id);

        if ($activity) {
            if (Gate::allows('modify', $activity)) {
                $fields = $request->validate([
                    'title' => 'required',
                    'desc' => 'required',
                    'image' => 'nullable|image|mimes:png,jpg,jpeg',
                    'date' => 'nullable|date'
                ]);

                if ($request->hasFile('image')) {
                    $imageName = time().'.'.$request->file('image')->getClientOriginalExtension();
                    $request->file('image')->storeAs('public/images', $imageName);
                    $fields['image'] = $imageName; // Store only the image name
                }

                $activity->update($fields);
                return response()->json(['message' => 'Activity updated']);
            } else {
                return response()->json(['message' => 'You do not own this activity.'], 403);
            }
        } else {
            return response()->json(['message' => 'Activity not found'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $activity = activities::find($id);

        if ($activity) {
            if (Gate::allows('modify', $activity)) {
                $activity->delete();
                return response()->json(['message' => 'Activity deleted']);
            } else {
                return response()->json(['message' => 'You do not own this activity.'], 403);
            }
        } else {
            return response()->json(['message' => 'Activity not found'], 404);
        }
    }
}
