<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\ApiController;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionCategoryController extends ApiController
{
    //

    public function index(Transaction $transaction) {
        $product = $transaction->product;
    
        if (!$product) {
            return $this->errorResponse('Product not found for this transaction.', 404);
        }
    
        $categories = $product->categories;
        return $this->showAll($categories, 200);
    }
    
}
