<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\S3FileService;

class ProductController extends ApiController
{

    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $products = Product::all();
        return $this->showAll($products, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return $this->showMessage('Product created successfully', 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, S3FileService $s3)
    {
        //

        $rules = [
            'name' => 'required|max:100',
            'description' => 'max:255',
            'price' => 'numeric|required',
            'quantity' => 'numeric|required',
            'image' => 'sometimes|image'
            

        ];
        $this->validate($request, $rules);



        $data = $request->all();

        if ($request->hasFile('image')) {
            // dump($request->files('image'));
            $url = $s3->upload($request->file('image'), 'products');
            $data['image'] = $url;
        }

        

        // $url = $s3->upload($request->file('image'), 'products');
        // $data['image'] = $url;
        // dump( $request->all());
        $product = Product::create($data);
        return $this->showOne($product, 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
        // $product = Product::find($id);
        // if (!$product) {
        //     return $this->errorResponse('Product not found', 404);
        // }
        return $this->showOne($product, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json($product, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, S3FileService $s3)
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }
    
        // Validation rules (optional image)
        $rules = [
            'name' => 'sometimes|max:100',
            'description' => 'sometimes|max:255',
            'price' => 'sometimes|numeric',
            'quantity' => 'sometimes|numeric',
            'image' => 'sometimes|image' // 'sometimes' makes it optional
        ];
        $request->validate($rules);

    
        $data = $request->all();
        $oldImage = $product->image;
    
        // Only update the image if a new file is provided
        if ($request->hasFile('image')) {
            $url = $s3->update($request->file('image'), $oldImage, 'products');
            $data['image'] = $url;
        }
    
        $product->update($data);
        return $this->showOne($product, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, S3FileService $s3)
    {
        //
        $product = Product::find($id);

        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }
        $s3->delete($product->image);
        $product->delete();
        return $this->showOne($product, 200);
    }
}
