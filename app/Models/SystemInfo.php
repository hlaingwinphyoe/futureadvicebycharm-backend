<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SystemInfo extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function phones(): BelongsToMany
    {
        return $this->belongsToMany(Phone::class, 'system_phones', 'system_id', 'phone_id');
    }
}
