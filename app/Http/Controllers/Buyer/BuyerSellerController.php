<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\ApiController;
use App\Models\Buyer;
use Illuminate\Http\Request;

class BuyerSellerController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Buyer $buyer)
    {
        //

        $seller = $buyer->transactions()->with('product.seller')
        ->get()
        // if we want only seller of the buyer
        ->pluck('product.seller');

        return $this->showAll($seller, 200);
    }

    
}
