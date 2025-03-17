<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';
    public $incrementing = false;
    protected $keyType = 'string';


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->product_id)) {
                $product->product_id = (string) Str::uuid();
            }
        });
    }

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
        return $this->belongsToMany(
            Category::class,
            'product_category',     // pivot table name
            'product_id',           // foreign key on pivot pointing to Product
            'category_id'           // foreign key on pivot pointing to Category
        );
    }

}
