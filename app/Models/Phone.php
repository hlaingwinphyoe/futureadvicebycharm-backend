<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Phone extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function system_infos(): BelongsToMany
    {
        return $this->belongsToMany(SystemInfo::class, 'system_phones', 'phone_id', 'system_id');
    }
}
