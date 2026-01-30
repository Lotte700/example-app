<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUnits extends Model
{
    use HasFactory;

    protected $table = 'product_units';

    protected $fillable = ['product_id', 'name', 'ml', 'price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'product_unit_id');
    }
}
