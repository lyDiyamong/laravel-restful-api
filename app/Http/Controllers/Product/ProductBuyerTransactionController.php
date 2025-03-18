<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductBuyerTransactionController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Product $product, User $buyer)
    {
        //

        
        $rules = [
            "quantity" => "required|integer|min:1"
        ];

        $this->validate($request, $rules);

        if ($buyer->user_id == $product->seller_id){
            return $this->errorResponse("The Buyer must be different from Seller", 400);
        };

        if (!$buyer->isVerified()) {
            return $this->errorResponse("Buyer must verified to buy the item", 400);
        }

        if (!$product->seller->verified) {
            return $this->errorResponse("Seller must verified to buy the item", 400);
        }

        if (!$product->isAvailable()){
            return $this->errorResponse("The product is not available", 400);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->errorResponse("Input invalid", 400);
        }


        if ($product->quantity < $request->quantity){
            return $this->errorResponse("The product does not have enough unit for this transactions", 400);
        }


        return DB::transaction(function () use ($request, $product, $buyer) {
            $product->quantity -= $request->quantity;
            $product->save();

            $transaction = Transaction::create(
                [
                    "quantity" => $request->quantity,
                    'buyer_id' => $buyer->user_id,
                    'product_id' => $product->product_id
                ]
            );

            return $this->showOne($transaction, 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
