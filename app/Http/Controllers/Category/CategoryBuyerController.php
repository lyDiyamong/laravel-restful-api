<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryBuyerController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Category $category)

    {
        $buyers = $category->products()
        ->whereHas("transactions")
        ->with("transactions.buyer")
        ->get()
        ->pluck("transactions")
        ->collapse()
        ->pluck('buyer')
        ->unique("user_id");
        
        //

        return $this->showAll($buyers);
    }

    
}
