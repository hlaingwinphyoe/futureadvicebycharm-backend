<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    // public function getSlugOptions(): SlugOptions
    // {
    //     return SlugOptions::create()
    //         ->generateSlugsFrom('title')
    //         ->saveSlugsTo('slug');
    // }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tags');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(UpvoteDownVote::class);
    }

    public function post_views(): HasMany
    {
        return $this->hasMany(PostView::class);
    }

    public function user_posts(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_posts', 'post_id', 'user_id');
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

        if (request('category')) {
            if (request('category') !== 'all') {
                $query->whereHas('category', function ($q) {
                    $q->where('slug', request('category'));
                });
            }
        }

        if (request('sort')) {
            switch (request('sort')) {
                case 'latest':
                    $query->orderBy('id', 'desc');
                    break;

                case 'oldest':
                    $query->orderBy('id', 'asc');
                    break;

                case 'alphabet-asc':
                    $query->orderBy('title', 'asc');
                    break;

                case 'alphabet-desc':
                    $query->orderBy('title', 'desc');
                    break;

                default:
                    $query->orderBy('id', 'desc');
                    break;
            }
        }
    }

    // helper function
    protected function media(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $attributes['poster'] !== null ? '/storage/' . $attributes['poster'] : '/logo.png';
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

    public function getTags()
    {
        $name = '';

        foreach ($this->tags as $tag) {
            $name .= $name ? ', ' : '';
            $name .= $tag->name;
        }

        return $name;
    }

    public function formatNumber($number)
    {
        if ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        }
        if ($number >= 1000) {
            return round($number / 1000, 1) . 'K';
        }
        return $number;
    }

    public function humanReadTime(): Attribute
    {
        return new Attribute(
            get: function ($value, $attribute) {
                $words = Str::wordCount(strip_tags($attribute['desc']));
                $m = ceil($words / 200);
                return $m . ' ' . str('min')->plural($m) . ' read';
                // $words . " " . str('words')->plural($words);
            }
        );
    }
}
