<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'payment_date' => $this->payment_date,
            'step_id' => $this->step_id,
            'currency_id' => [
                'id' => $this->currency_id,
                'name' => config("params.currencies.$this->currency_id")
            ],
            'amount' => $this->amount,
            'payment_type' => [
                'id' => $this->payment_type,
                'name' => config("params.payment_types.$this->payment_type")
            ],
        ];
    }
}
