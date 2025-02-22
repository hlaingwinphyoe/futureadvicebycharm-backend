<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpvoteDownVote extends Model
{
    protected $fillable = ['user_id', 'post_id', 'is_upvote'];
}
