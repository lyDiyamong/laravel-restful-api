<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\ApiController;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $transactions = Transaction::all();
        return $this->showAll($transactions);
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
        $rules = [
            'quantity' => 'required|integer|min:1',
            'product_id' => 'required|exists:products,id',
        ];
        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return $this->errorResponse("Input invalid", 400);
        }

        $data['buyer_id'] = $request->buyer_id;
        $transactions = Transaction::create($data);

        return $this->showOne($transactions, 201);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

        $transactions= Transaction::find($id);

        if (!$transactions) {
            return $this->errorResponse($transactions, 404);

       }

       return $this->showOne($transactions, 200);
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
        $transactions = Transaction::find($id);

        if (!$transactions) {
            return $this->errorResponse("Not found", 404);

        }
        $transactions->delete();

        return $this->showMessage("Delete success");
    }
}
