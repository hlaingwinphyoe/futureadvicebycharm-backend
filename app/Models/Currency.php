<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function exchangerates()
    {
        return $this->hasMany(ExchangeRate::class, 'currency_id', 'id');
    }

    public function exchangerate()
    {
        return $this->exchangerates()->latest()->first();
    }

    //helper functions here

    //scope functions here
    public function scopeFilterOn($query)
    {
        if (request('search')) {
            $query->where('name', 'like', '%' . request('search') . '%');
        }
    }
}
