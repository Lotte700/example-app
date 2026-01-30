<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    protected $fillable = ['name'];

    function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
