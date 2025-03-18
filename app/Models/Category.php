<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $primaryKey = 'category_id';
    public $incrementing = false;
    protected $keyType = 'string';


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->category_id)) {
                $category->category_id = (string) Str::uuid();
            }
        });
    }
    //
    protected $fillable = ['name', 'description'];

    protected $hidden = [
        'pivot'
    ];


    public function products()
    {
        return $this->belongsToMany(
            Product::class, 
            'product_category', 
            'category_id', 
            'product_id');
    }


}
