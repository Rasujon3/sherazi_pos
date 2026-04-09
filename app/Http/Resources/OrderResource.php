<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'status'       => $this->status,
            'total_amount' => $this->total_amount,
            'customer'     => [
                'id'    => $this->whenLoaded('customer', fn() => $this->customer->id),
                'name'  => $this->whenLoaded('customer', fn() => $this->customer->name),
                'email' => $this->whenLoaded('customer', fn() => $this->customer->email),
                'phone' => $this->whenLoaded('customer', fn() => $this->customer->phone),
            ],
            'items'        => OrderItemResource::collection($this->whenLoaded('items')),
            'items_count'  => $this->whenLoaded('items', fn() => $this->items->count()),
            'created_at'   => $this->created_at->toDateTimeString(),
        ];
    }
}
