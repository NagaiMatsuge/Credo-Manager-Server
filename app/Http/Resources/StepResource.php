<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StepResource extends JsonResource
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
            'title' => $this->title,
            'project_id' => $this->project_id,
            'price' => $this->price,
            'debt' => $this->debt,
            'currency_id' => [
                'id' => $this->currency_id,
                'name' => config("params.currencies.$this->currency_id")
            ],
            'payment_type' => [
                'id' => $this->payment_type,
                'name' => config("params.payment_types.$this->payment_type")
            ],
        ];
    }
}
