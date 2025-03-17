<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buyer extends User
{
    protected $table = 'users';
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
