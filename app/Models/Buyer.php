<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buyer extends User
{
    protected $table = 'users';
    // protected $primaryKey = 'user_id';
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'buyer_id', 'user_id');
    }
}
