<?php

namespace App\Http\Controllers;

use App\Models\testimoni;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class TestimoniController extends Controller
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
        return testimoni::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'rating' => 'required',
            'testimoni' => 'required',
            'image' => 'nullable|image|mimes:png,jpg,jpeg'
        ]);

        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('images'), $imageName);
            $fields['image'] = $imageName;
        }

        $testimoni = $request->user()->testimoni()->create($fields);
        return ['testimoni' => $testimoni];
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $testimoni = testimoni::find($id);

        if ($testimoni) {
            return response()->json($testimoni);
        } else {
            return response()->json(['message' => 'Testimoni not found.'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $testimoni = testimoni::find($id);

        if (Gate::allows('modify', $testimoni)) {
            if ($testimoni) {
                $fields = $request->validate([
                    'rating' => 'required',
                    'testimoni' => 'required',
                    'image' => 'nullable|image|mimes:png,jpg,jpeg'
                ]);

                if ($request->hasFile('image')) {
                    $imageName = time().'.'.$request->image->getClientOriginalExtension();
                    $request->image->move(public_path('images'), $imageName);
                    $fields['image'] = $imageName;
                }

                $testimoni->update($fields);
                return response()->json(['message' => 'Testimoni updated.', 'testimoni' => $testimoni]);
            } else {
                return response()->json(['message' => 'Testimoni not found.'], 404);
            }
        } else {
            return response()->json(['message' => 'You do not own this testimoni.'], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(testimoni $testimoni)
    {
        $product = products::find($id);

        if (Gate::allows('modify', $testimoni)) {
            $testimoni->delete();
            return response()->json(['message' => 'Testimoni deleted.']);
        } else {
            return response()->json(['message' => 'You do not own this testimoni.'], 403);
        }
    }
}
