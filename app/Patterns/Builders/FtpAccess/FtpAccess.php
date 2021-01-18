<?php

namespace App\Patterns\Builders\FtpAccess;

class FtpAccess
{
    private $username;
    private $password;

    public function setUser($username)
    {
        $this->username = $username;
        return $this;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function create()
    {
        if ((!$this->username) || (!$this->password)) return ["success" => false, "message" => "You have to provide username and password"];
        $shellScript = "create_sft_user $this->username $this->password";
        $success_message = "The account is setup";
        $result = shell_exec($shellScript);
        if (strpos($result, $success_message) !== false) {
            return ["success" => true];
        } else {
            return ["sucess" => false, "message" => $result];
        }
    }

    public function delete()
    {
        $result = shell_exec("deluser --remove-home $this->username");
    }
}
