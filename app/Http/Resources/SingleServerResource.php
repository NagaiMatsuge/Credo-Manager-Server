<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SingleServerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $type = config('params.server_types.' . $this->type);
        //For now ftp accesss can only be one but for future reference there could be more
        $ftp = $this->ftp_access[0];
        $db = $this->db_access[0];
        $res = [
            'server' => [
                'id' => $this->id,
                'project_id' => $this->project_id,
                'title' => $this->title,
                'host' => $this->host,
                'created_at' => $this->created_at,
                'type' => [
                    'id' => $this->type,
                    'title' => $type
                ],
            ],
            'ftp_access' => [
                'port' => $ftp->port,
                'host' => $ftp->host,
                'login' => $ftp->login,
                'password' => $ftp->password,
                'description' => $ftp->description
            ],
            'db_access' => [
                'host' => $db->host,
                'db_name' => $db->db_name,
                'login' => $db->login,
                'password' => $db->password,
                'description' => $db->description
            ]
        ];

        return $res;
    }
}
