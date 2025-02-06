<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostService
{
    public function store() {}

    public function update(object $post, array $paramData = [])
    {
        $post->update([
            'title' => $paramData['title'],
            'desc' => $paramData['desc'],
            'category_id' => $paramData['category'],
            'excerpt' => Str::words($paramData['desc'], 50),
            'user_id' => Auth::id(),
        ]);

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
