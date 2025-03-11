<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class SellerController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $seller_ids = Product::select('seller_id')->distinct()->get()->pluck('seller_id');
        $sellers = User::whereIn('user_id', $seller_ids)->get();
        return $this->showAll($sellers, 200);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $seller_id = Product::where('seller_id', $id)->first()->seller_id;
        $sellers = User::where('user_id', $seller_id)->first();
        if (!$sellers) {
            return response()->json(['message' => 'Seller not found'], 404);
        }
        return $this->showOne($sellers, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
