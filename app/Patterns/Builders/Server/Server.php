<?php

namespace App\Patterns\Builders\Server;

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

    public function create()
    {
        if (!$this->host) return ["success" => false, "message" => "You have to provide hostname"];
        $shellScript = "sudo virtualhost create $this->host ";
        if ($this->dir) $shellScript .= $this->dir;
        $result = shell_exec($shellScript);
        info($result);
        $success_message = "Complete!";
        if (strpos($result, $success_message) !== false) {
            return ["success" => true];
        } else {
            return ["sucess" => false, "message" => $result];
        }
    }

    public function delete()
    {
        if (!$this->host) return ["success" => false, "message" => "You have to provide hostname"];
        $shellScript = "sudo virtualhost delete $this->host";
        $success_message = "Complete!";
        $result = shell_exec($shellScript);
        info($result);
        if (strpos($result, $success_message) !== false) {
            return ["success" => true];
        } else {
            return ["sucess" => false, "message" => $result];
        }
    }
}
