<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategorySellerController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Category $category)   
    {
        //
        $seller = $category->products()->with('seller')->get()
        ->pluck('seller')
        ->values();

        return $this->showAll($seller);

    }

    
}
