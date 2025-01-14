<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $guarded = [];

    // scope function
    public function scopeNotDisabled($query)
    {
        $query->where('disabled', 0);
    }

    public function scopeFilterOn($query)
    {
        if (request('search')) {
            $query->where('acc_name', 'like', '%' . request('search') . '%')
                ->orWhere('acc_no', 'like', '%' . request('search') . '%');
        }
    }

    // helper function
    protected function media(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $attributes['image'] !== null ? '/storage/' . $attributes['image'] : '';
            }
        );
    }
}
