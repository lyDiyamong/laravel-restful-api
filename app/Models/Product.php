<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Product extends Model
{
    use HasFactory;
    //fillable
    protected $fillable = ['name', 'description', 'price', 'stock', 'image'];
}
