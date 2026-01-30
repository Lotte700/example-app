<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
    public function user()
{
    return $this->belongsTo(User::class);
}
}
