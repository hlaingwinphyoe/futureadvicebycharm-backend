<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
            'price' => $this->price,
            'th_price' => $this->th_price,
            'currency_id' => $this->currency_id,
            'currency' => $this->currency ? $this->currency->name : 'Ks',
            'th_currency_id' => $this->th_currency_id,
            'th_currency' => $this->th_currency ? $this->th_currency->name : '฿',
            'astrologer' => $this->astrologer ? $this->astrologer->name : '',
            'discount_percent' => $this->discount_percent,
            'final_price' => $this->final_price,
            'th_final_price' => $this->th_final_price,
            'astrologer_id' => $this->astrologer_id,
            'created_at' => $this->created_at->diffForHumans(),
            'disabled' => $this->disabled,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
        ];
    }
}
