<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function appointment_packages(): HasMany
    {
        return $this->hasMany(AppointmentPackage::class, 'appointment_id', 'id');
    }

    public function remarks()
    {
        return $this->morphToMany(Remark::class, 'remarkable');
    }

    public function getPackageNames()
    {
        $name = '';

        foreach ($this->appointment_packages as $app_pack) {
            $name .= $name ? ', ' : '';
            $name .= $app_pack->package ? $app_pack->package->name : '';
        }

        return $name;
    }

    // scope function
    public function scopeFilterOn() {}
}
