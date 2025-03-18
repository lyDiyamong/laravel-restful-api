<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Models\Product;

class ProductBuyerController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Product $product)
    {
        //

        $buyer = $product
        ->whereHas("transactions")
        ->with("transactions.buyer")
        ->get()
        ->pluck("transactions")
        ->collapse()
        ->pluck("buyer")
        ->unique("user_id");

        return $this->showAll($buyer);

    }

    
}
