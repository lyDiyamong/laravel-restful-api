<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductTransactionController extends ApiController    
{
    /**
     * Display a listing of the resource.
     */
    public function index(Product $product)
    {
        //

        $transactions = $product->with('transactions')
        ->get()
        ->pluck('transactions')
        ->collapse()
        ->unique('id');

        return $this->showAll($transactions);
    }

    
}
