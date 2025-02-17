<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'dob' => $this->dob,
            'social_link' => $this->social_link,
            'other_social' => $this->other_social,
            'weekday_id' => $this->weekday_id,
            'weekday_name' => $this->weekday ? $this->weekday->name : "",
            'gender_id' => $this->gender_id,
            'gender_name' => $this->gender ? $this->gender->name : "",
            'email_verified_at' => $this->email_verified_at,
            'profile' => $this->profile_photo_path ? asset('storage/' . $this->profile_photo_path) : null,
            'role_id' => $this->role_id,
            'role' => $this->role->name,
            'created_at' => $this->created_at->format('d M, Y'),
        ];
    }
}
