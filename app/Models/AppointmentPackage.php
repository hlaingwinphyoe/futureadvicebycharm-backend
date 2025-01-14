<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentPackage extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function th_currency()
    {
        return $this->belongsTo(Currency::class, 'th_currency_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
