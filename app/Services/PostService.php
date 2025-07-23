<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostService
{
    public function store(array $paramData = [])
    {
        $post = Post::create([
            'title' => $paramData['title'],
            'slug' => Str::slug($paramData['slug']),
            'desc' => $paramData['desc'],
            'category_id' => $paramData['category'],
            'excerpt' => Str::words($paramData['desc'], 100),
            'user_id' => Auth::id(),
        ]);

        $readTime = $post->human_read_time;

        $post->update(['read_time' => $readTime]);

        $tagsArr = explode(',', $paramData['tags']);
        $tagIds = [];

        foreach ($tagsArr as $tag) {
            $newTag = Tag::updateOrCreate(['slug' => $tag], ['name' => $tag]);
            $tagIds[] = $newTag->id;
        }

        $post->tags()->sync($tagIds);

        return $post;
    }

    public function update(object $post, array $paramData = [])
    {
        $post->update([
            'title' => $paramData['title'],
            'slug' => Str::slug($paramData['slug']),
            'desc' => $paramData['desc'],
            'category_id' => $paramData['category'],
            'excerpt' => Str::words($paramData['desc'], 50),
            'user_id' => Auth::id(),
        ]);

        $readTime = $post->human_read_time;

        $post->update(['read_time' => $readTime]);

        $tagsArr = explode(',', $paramData['tags']);
        $tagIds = [];

        foreach ($tagsArr as $tag) {
            $newTag = Tag::updateOrCreate(['slug' => $tag], ['name' => $tag]);
            $tagIds[] = $newTag->id;
        }

        $post->tags()->sync($tagIds);

        return $post;
    }
}
