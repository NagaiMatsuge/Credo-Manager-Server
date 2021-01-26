<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SingleUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $res = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'work_start_time' => substr($this->work_start_time, 0, -3),
            'work_end_time' => substr($this->work_end_time, 0, -3),
            'pause_start_time' => substr($this->pause_start_time, 0, -3),
            'pause_end_time' => substr($this->pause_end_time, 0, -3),
            'manager_id' => $this->manager_id,
            'photo' => $this->photo,
            'color' => $this->color,
            'theme' => $this->theme,
            'role' => $this->role,
            'working_days' => json_decode($this->working_days)
        ];
        return $res;
    }
}
