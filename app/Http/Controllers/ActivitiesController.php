<?php

namespace App\Http\Controllers;

use App\Models\activities;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

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
        $fields = $request->validate([
            'title' => 'required',
            'desc' => 'required',
            'image' => 'nullable|image|mimes:png,jpg,jpeg',
            'date' => 'nullable|date'
        ]);

        // Check if the request has an image
        if ($request->hasFile('image')) {
            // Generate a unique name for the image with its extension
            $imageName = time().'.'.$request->image->getClientOriginalExtension();
            // Move the image to the public/images directory
            $request->image->move(public_path('images'), $imageName);
        }

        //$activities = activities::create($fields);
        $activities = $request->user()->activities()->create($fields);

        return ['activities' => $activities];
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
        $activity = activities::find($id);

        Gate::authorize('modify', $activity);
        // Step 1: Validate the incoming request data
        if (!$activity) {
            return response()->json(['message' => 'Activity not found'], 404);
        }

        $validatedData = $request->validate([
            'title' => 'required',
            'desc' => 'required',
            'category' => 'nullable',
            'image' => 'nullable|image|mimes:png,jpg,jpeg',
            'date' => 'nullable|date'
        ]);

        // Step 2: Find the model instance

        // Step 3: Update the model
        $activity->update($validatedData);

        // Step 4: Return the updated model
        return response()->json($activity);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $activity = activities::find($id);

        Gate::authorize('modify', $activity);

        if (!$activity) {
            return response()->json(['message' => 'Activity not found'], 404);
        }

        $activity->delete();

        // Return a 200 OK status with a message
        return response()->json(['message' => 'Activity deleted successfully']);
    }
}
