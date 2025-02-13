<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'desc' => $this->desc,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'author_id' => $this->user_id,
            'author' => $this->owner,
            'published_at' => $this->created_at->diffForHumans(),
            'priority' => $this->priority,
            'read_time' => $this->read_time,
            'poster_url' =>  $this->poster ? asset('storage/' . $this->poster) : null,
        ];
    }
}
