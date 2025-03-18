<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\ApiController;
use App\Models\Category;

class CategoryTransactionController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Category $category)
    {
        //
        $transactions = $category->products()
        // whereHas method: showing at least 1 item 
        ->whereHas('transactions')
        ->with("transactions")
        ->get()
        ->pluck('transactions')
        ->collapse();
        // ->values(); 


        return $this->showAll($transactions, 200);
    }   

    
}
