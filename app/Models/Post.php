<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Post extends Model
{
    use HasFactory, HasSlug, SoftDeletes;

    protected $guarded = [];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // scope function

    public function scopePublished($query)
    {
        $query->where('is_published', 0);
    }

    public function scopeFilterOn($query)
    {
        if (request('search')) {
            $query->where('title', 'like', '%' . request('search') . '%')
                ->orWhere('desc', 'like', '%' . request('search') . '%');
        }
    }

    // helper function
    protected function media(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $attributes['poster'] !== null ? '/storage/' . $attributes['poster'] : '/imgs/4.jpg';
            }
        );
    }

    protected function owner(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $attributes['user_id'] === Auth::id() ? "Me" : $this->user->name ?? '-';
            }
        );
    }
}
