<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $table = 'exchangerates';

    protected $guarded = [];

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    //helper functions here

    //scope functions here
    public function scopeFilterOn($query)
    {
        if (request('search')) {
            $query->whereHas('currency', function ($q) {
                $q->where('name', 'like', '%' . request('search') . '%');
            });
        }
    }
}
