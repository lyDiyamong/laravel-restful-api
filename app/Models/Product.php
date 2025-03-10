<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Product extends Model
{
    use HasFactory;

    const AVAILABLE_PRODUCT = 'available';
    const UNAVAILABLE_PRODUCT = 'unavailable';

    //fillable
    protected $fillable = ['name', 'description', 'price', 'quantity', 'image', 'status', 'seller_id'];


    public function isAvailable() 
    {
        return $this->status == Product::AVAILABLE_PRODUCT;
    }
    // Relationships

    public function seller() 
    {
        return $this->belongsTo(Seller::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
