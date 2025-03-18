<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\ApiController;
use App\Models\Category;

class CategoryProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Category $category)
    {
        //

        $products = $category->with("products")
        ->get()
        ->pluck('products')
        ->collapse()
        ->unique("product_id")
        ->values();

        return $this->showAll($products, 200);
    }

}
