<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
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
            'appointment_no' => $this->appointment_no,
            'total_price' => $this->total_price,
            'th_total_price' => $this->th_total_price,
            'desc' => $this->desc,
            'balance' => $this->balance,
            'th_balance' => $this->th_balance,
            'discount_amt' => $this->discount_amt,
            'transaction_image' => $this->transaction_image ? asset('storage/' . $this->transaction_image) : null,
            'transaction_image' => $this->transaction_image,
            'transaction_no' => $this->transaction_no,
            'is_paid' => $this->is_paid == 0 ? '-' : 'Paid',
            'user_id' => $this->user_id,
            'status_id' => $this->status_id,
            'paymentype_id' => $this->paymentype_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'status' => $this->status ? $this->status->name : null,
            'payment' => $this->whenLoaded('payment'),
            'appointment_packages' => AppointmentPackageResource::collection($this->whenLoaded('appointment_packages')),
            'packageNames' => $this->getPackageNames(),
            'date' => $this->created_at->format('d M, Y H:i A'),
            'appointment_date' => $this->appointment_date
        ];
    }
}
