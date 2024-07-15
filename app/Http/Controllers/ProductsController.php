<?php

namespace App\Http\Controllers;

use App\Models\products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

class ProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return products::all(); // Renamed from 'products' to 'Product'
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) // Using a Form Request for validation
    {
        $fields = $request->validate([
            'title' => 'required',
            'desc' => 'required',
            'image' => 'nullable|image|mimes:png,jpg,jpeg',
            'rating' => 'nullable|numeric',
            'category' => 'required',
            'release_date' => 'nullable|date'
        ]);

        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('images'), $imageName);
            $fields['image'] = $imageName;
        }

        //$product = products::create($fields);
        $product = $request->user()->products()->create($fields); // Renamed from 'products' to 'Product'

        return ['product' => $product]; // Renamed from 'products' to 'product'
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = products::find($id);

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
        Gate::authorize('modify', $product);

        $product = products::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $fields = $request->validate([
            'title' => 'required',
            'desc' => 'required',
            'image' => 'nullable|image|mimes:png,jpg,jpeg',
            'rating' => 'nullable|numeric',
            'category' => 'required',
            'release_date' => 'nullable|date'
        ]);

        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('images'), $imageName);
            $fields['image'] = $imageName;
        }

        $product->update($fields);

        return ['product' => $product]; // Renamed from 'products' to 'product'
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Gate::authorize('modify', $product);

        $product = products::find($id);

        if ($product) {
            $product->delete();
            return response()->json(['message' => 'Product deleted']);
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }
}
