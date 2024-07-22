<?php

namespace App\Http\Controllers;

use App\Models\products;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller implements HasMiddleware
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
        return products::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) // Using a Form Request for validation
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $fields = $request->validate([
            'title' => 'required',
            'desc' => 'required',
            'image' => 'nullable|image|mimes:png,jpg,jpeg|file',
            'rating' => 'nullable|numeric',
            'category' => 'required',
            'release_date' => 'nullable|date'
        ]);

        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->storeAs('public/images', $imageName);
            $fields['image'] = $imageName; // Store only the image name
        }

        $product = $user->products()->create($fields);
        return ['product' => $product];
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = products::find($id); // Renamed from 'products' to 'Product'

        if ($product) {
            return response()->json($product);
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) // Removed 'Product $product' parameter
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $product = products::find($id); // Renamed from 'products' to 'Product'

        if ($product) {
            if (Gate::allows('modify', $product)) {
                $fields = $request->validate([
                    'title' => 'sometimes|required',
                    'desc' => 'sometimes|required',
                    'image' => 'nullable|image|mimes:png,jpg,jpeg|file',
                    'rating' => 'nullable|numeric',
                    'category' => 'sometimes|required',
                    'release_date' => 'nullable|date'
                ]);

                if ($request->hasFile('image')) {
                    $imageName = time().'.'.$request->file('image')->getClientOriginalExtension();
                    $request->file('image')->storeAs('public/images', $imageName);
                    $fields['image'] = $imageName; // Store only the image name
                }

                $product->update($fields);
                return response()->json(['message' => 'Product updated.']);
            } else {
                return response()->json(['message' => 'You do not own this product.'], 403);
            }
        } else {
            return response()->json(['message' => 'Product not found.'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $product = products::find($id); // Renamed from 'products' to 'Product'

        if ($product) {
            if (Gate::allows('modify', $product)) {
                $product->delete();
                return response()->json(['message' => 'Product deleted']);
            } else {
                return response()->json(['message' => 'You do not own this product.'], 403);
            }
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }
}
