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
        $shellScript = "sudo create_sft_user create $this->username $this->password";
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
        if (!$this->username) return ["success" => false, "message" => "You have to provide username"];
        $result = shell_exec("sudo create_sft_user delete $this->username");
        $success_message = "Deleted user";
        if (strpos($result, $success_message) !== false) {
            return ["success" => true];
        } else {
            return ["sucess" => false, "message" => $result];
        }
    }
}
