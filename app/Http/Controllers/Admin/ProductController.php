<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index()
    {
        $products = auth()->user()->clinic->products;
        return view('admin.products.index', compact('products'));
    }

    public function show(Product $product)
    {
        $this->authorize('view', $product);
        return response()->json($product);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $clinic = auth()->user()->clinic;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('product-images', 'public');
            $validated['image_path'] = $path;
        }

        $product = $clinic->products()->create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Product created successfully',
            'data' => $product
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            // Store new image
            $path = $request->file('image')->store('product-images', 'public');
            $validated['image_path'] = $path;
        }

        $product->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully'
        ]);
    }

    public function updateStock(Request $request, Product $product): JsonResponse
    {
        $clinic = Clinic::where('owner_user_id', Auth::id())->firstOrFail();

        if ($product->clinic_id !== $clinic->id) {
            throw ValidationException::withMessages(['product' => 'Unauthorized access']);
        }

        $validated = $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $product->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Stock updated successfully',
            'data' => $product
        ]);
    }
} 