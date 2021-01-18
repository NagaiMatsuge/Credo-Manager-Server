<?php

namespace App\Patterns\Builders\Server;

class Server
{
    private $username;
    private $dir;

    public function setUser($username)
    {
        $this->username = $username;
        return $this;
    }

    public function setDir($dirname)
    {
        $this->dir = $dirname;
        return $this;
    }

    public function create()
    {
        if ((!$this->dir) || (!$this->username)) return ["success" => false, "message" => "You have to provide username and directory"];
        $shellScript = "virtualhost create $this->username $this->dir";
        $result = shell_exec($shellScript);
        $success_message = "Compelte!";
        if (strpos($result, $success_message) !== false) {
            return ["success" => true];
        } else {
            return ["sucess" => false, "message" => $result];
        }
    }
}
