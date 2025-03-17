<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\ApiController;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionSellerController extends ApiController
{
    //
    public function index(Transaction $transaction) {
        $product = $transaction->product;
    
        if (!$product) {
            return $this->errorResponse('Product not found for this transaction.', 404);
        }

        // dump($product);
    
        $seller = $product->seller;
        // dump($seller);
        return $this->showOne($seller, 200);
    }
}
