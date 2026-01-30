<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'code',
        'name',
        'outlet',
        'phone',
        'position'
    ];
    function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
    public function user()
{
    return $this->belongsTo(User::class);
}
public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
