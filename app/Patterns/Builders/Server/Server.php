<?php

namespace App\Patterns\Builders\Server;

use App\Helpers\Logger;

class Server
{
    private $host;
    private $dir;

    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    public function setDir($dirname)
    {
        $this->dir = $dirname;
        return $this;
    }

    public function create($email)
    {
        if (!$this->host) return ["success" => false, "message" => "You have to provide hostname"];
        $shellScript = "sudo virtualhost create $this->host ";
        if ($this->dir) $shellScript .= $this->dir;
        $result = shell_exec($shellScript);
        Logger::serverChange($result, $email, "Server create");
        $success_message = "Complete!";
        if (strpos($result, $success_message) !== false) {
            return ["success" => true];
        } else {
            return ["sucess" => false, "message" => $result];
        }
    }

    public function delete($email)
    {
        if (!$this->host) return ["success" => false, "message" => "You have to provide hostname"];
        $shellScript = "sudo virtualhost delete $this->host";
        $success_message = "Complete!";
        $result = shell_exec($shellScript);
        Logger::serverChange($result, $email, "Server delete");
        if (strpos($result, $success_message) !== false) {
            return ["success" => true];
        } else {
            return ["sucess" => false, "message" => $result];
        }
    }
}
