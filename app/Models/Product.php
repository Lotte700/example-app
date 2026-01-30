<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category_id',
        'image',
        'description'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
    public function productUnits()
{
    return $this->hasMany(ProductUnits::class, 'product_id');
}

}

