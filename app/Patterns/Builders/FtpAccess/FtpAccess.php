<?php

namespace App\Patterns\Builders\FtpAccess;

use App\Helpers\Logger;

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

    public function create($email)
    {
        if ((!$this->username) || (!$this->password)) return ["success" => false, "message" => "You have to provide username and password"];
        $shellScript = "sudo create_sftp_user create $this->username $this->password";
        $success_message = "The account is setup";
        $result = shell_exec($shellScript);
        Logger::serverChange($result, $email, "Creating Ftp");
        if (strpos($result, $success_message) !== false) {
            return ["success" => true];
        } else {
            return ["success" => false, "message" => $result];
        }
    }

    public function delete($email)
    {
        if (!$this->username) return ["success" => false, "message" => "You have to provide username"];
        $result = shell_exec("sudo create_sftp_user delete $this->username");
        Logger::serverChange($result, $email, "Deleting Ftp");
        $success_message = "Deleted user";
        if (strpos($result, $success_message) !== false) {
            return ["success" => true];
        } else {
            return ["success" => false, "message" => $result];
        }
    }

    public function update($email)
    {
        if ((!$this->username) || (!$this->password)) return ["success" => false, "message" => "You have to provide username and password"];
        $shellCommand = "sudo /usr/local/bin/update_user_password $this->username $this->password 2>&1";
        $res = shell_exec($shellCommand);
        Logger::serverChange($res, $email, "Updating ftp user password");
        if ($res == null) {
            return ['success' => true];
        } else {
            return ['success' => false, 'message' => $res];
        }
    }
}
