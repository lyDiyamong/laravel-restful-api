<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\ApiController;
use App\Models\Buyer;

class BuyerTransactionController extends ApiController
{
    //

    public function index(Buyer $buyer) {
        
        // dump($buyer);
        $transactions = $buyer->transactions;


        return $this->showAll($transactions, 200);
        }
}
