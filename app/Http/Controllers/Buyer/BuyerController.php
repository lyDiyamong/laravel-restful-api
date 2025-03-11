<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class BuyerController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $buyer_ids = Transaction::select('buyer_id')->distinct()->get()->pluck('buyer_id');
        $buyers = User::whereIn('user_id', $buyer_ids)->get();
        return $this->showAll($buyers, 200);
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
        $buyer_id = Transaction::where('buyer_id', $id)->first()->buyer_id;
        $buyers = User::where('user_id', $buyer_id)->first();
        if (!$buyers) {
            return $this->errorResponse('The buyer with the specified ID does not exist', 404);
        }
        return $this->showOne($buyers, 200);
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
