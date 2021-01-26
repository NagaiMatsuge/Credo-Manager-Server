<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServerResource extends JsonResource
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
            'project_id' => $this->project_id,
            'title' => $this->title,
            'host' => $this->host,
            'created_at' => $this->created_at,
            'db_ftp' => []
        ];

        foreach ($this->ftp_access as $ftp) {
            $res['db_ftp'][] = [
                'tab' => 'ftp',
                'data' => $ftp
            ];
        }
        foreach ($this->db_access as $db) {
            $res['db_ftp'][] = [
                'tab' => 'db',
                'data' => $db
            ];
        }
        return $res;
    }
}
