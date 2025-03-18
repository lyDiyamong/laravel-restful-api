<?php

namespace App\Models;

class Seller extends User
{
    protected $table = 'users';

    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id', 'user_id');
    }
}
