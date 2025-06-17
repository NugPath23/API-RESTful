<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Resources\ProductResource; 
use Illuminate\Support\Facades\Validator;
class ProductController extends Controller 
{ 
    public function index() 
    { 
        $products = Product::all();
        return ProductResource::collection($products);
    } 
     
    public function store(Request $request) 
    { 
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.', 
                'errors' => $validator->errors() 
            ], 422); 
        }

        $validatedData = $validator->validated();

        $product = Product::create($validatedData);

        return response()->json([
            'message' => 'Product created successfully!',
            'product' => $product 
        ],201);
    }
    
    public function show($id) 
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Resource not found'
            ], 404); 
        }

        // If the product is found, return the ProductResource
        return new ProductResource($product);
    }
     
    public function update(Request $request, Product $product) 
    { 
        $validated = $request->validate([ 
            'name' => 'sometimes|string|max:255', 
            'description' => 'sometimes|string', 
            'price' => 'sometimes|numeric|min:0', 
            'stock' => 'sometimes|integer|min:0' 
        ]); 
         
        $product->update($validated); 
         
        return response()->json([ 
            'status' => 'success', 
            'message' => 'Product updated successfully', 
            'data' => $product 
        ]); 
    } 
     
    public function destroy(Product $product) 
    { 
        $product->delete(); 
         
        return response()->json([ 
            'status' => 'success', 
            'message' => 'Product deleted successfully' 
        ]); 
    } 
}