<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $table = 'contact_messages';
    protected $guarded = [];

    public function scopeFilterOn($q)
    {
        if (request('search')) {
            $q->where('name', 'like', '%' . request('search') . '%')
                ->orWhere('email', 'like', '%' . request('search') . '%')
                ->orWhere('message', 'like', '%' . request('search') . '%');
        }
    }
}
