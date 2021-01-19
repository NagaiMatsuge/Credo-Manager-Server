<?php

namespace App\Patterns\Builders\DbAccess;

use App\Helpers\Logger;

class DbAccess
{
    private $username;
    private $password;
    private $host;
    private $database;

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

    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    public function setDatabaseName($database)
    {
        $this->database = $database;
        return $this;
    }

    public function create($email)
    {
        if ((!$this->database) || (!$this->username)) return ["success" => false, "message" => "Database or username is not set!"];
        $shellCommand = "sudo mysql_create_db_user -t=create -d=$this->database -u=$this->username ";
        if ($this->host) $shellCommand .= $shellCommand . "-h=$this->host ";
        if ($this->password) $shellCommand .= $shellCommand . "-p=$this->password";
        $result = shell_exec($shellCommand);
        Logger::serverChange($result, $email, "Creating Database");
        $success_message = "User creation completed!";
        if (strpos($result, $success_message) !== false) {
            return ["success" => true];
        } else {
            return ["sucess" => false, "message" => $result];
        }
    }

    public function delete($email)
    {
        if ((!$this->database) && (!$this->username)) return ["success" => false, "message" => "Database or username is not set!"];
        $shellCommand = "sudo mysql_create_db_user -t=delete ";
        if ($this->database) $shellCommand .= "-d=$this->database";
        if ($this->username) $shellCommand .= "-u=$this->username";
        $result = shell_exec($shellCommand);
        Logger::serverChange($result, $email, "Deleting Database");
        $success_message = "Complete!";
        if (strpos($result, $success_message) !== false) {
            return ["success" => true];
        } else {
            return ["sucess" => false, "message" => $result];
        }
    }
}
