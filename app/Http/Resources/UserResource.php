<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'work_start_time' => $this->work_start_time,
            'work_end_time' => $this->work_end_time,
            'pause_start_time' => $this->pause_start_time,
            'pause_end_time' => $this->pause_end_time,
            'working_days' => json_decode($this->working_days),
            'photo' => $this->photo
        ];
    }
}
