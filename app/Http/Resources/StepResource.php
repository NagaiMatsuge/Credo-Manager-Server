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
            'currency_id' => config("params.currencies.$this->currency_id"),
            'payment_type' => config("params.payment_types.$this->payment_type"),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
