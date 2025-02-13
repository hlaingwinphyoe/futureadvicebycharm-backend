<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentPackageResource extends JsonResource
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
            'appointment_id' => $this->appointment_id,
            'package_id' => $this->package_id,
            'package' => new PackageResource($this->package),
            'price' => $this->price,
            'th_price' => $this->th_price,
            'balance' => $this->balance,
            'th_balance' => $this->th_balance,
            'discount_amt' => $this->discount_amt,
            'discountype_id' => $this->discountype_id,
            'currency_id' => $this->currency_id,
            'currency' => $this->currency ? $this->currency->name : null,
            'th_currency_id' => $this->th_currency ? $this->th_currency->name : null,
            'status_id' => $this->status_id,
            'status' => $this->status ? $this->status->name : null,
        ];
    }
}
