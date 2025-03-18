<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductCategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Product $product) 
    {
        //
        $categories = $product->categories()->get();


        return $this->showAll($categories);
    }

    public function update(Request $request, Product $product, Category $category) {
        // sync, attach, syncWithoutDetaching

        $product->categories()->syncWithoutDetaching([$category->category_id]);

        return $this->showAll($product->categories);
    }

    public function destroy(Product $product, Category $category){
        $product->categories()->detach([$category->category_id]);

        return $this->showAll($product->categories);
    }


}
