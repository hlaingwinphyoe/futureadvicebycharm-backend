<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostView extends Model
{
    protected $fillable = ['ip_address', 'user_agent', 'post_id', 'user_id'];
}
