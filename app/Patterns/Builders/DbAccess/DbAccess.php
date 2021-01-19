<?php

namespace App\Patterns\Builders\DbAccess;


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

    public function create()
    {
        if ((!$this->database) || (!$this->username)) return ["success" => false, "message" => "Database or username is not set!"];
        $shellCommand = "mysql_create_db_user -d=$this->database -u=$this->username ";
        if ($this->host) $shellCommand . "-h=$this->host ";
        if ($this->password) $shellCommand . "-p=$this->password";
        $result = shell_exec($shellCommand);
        $success_message = "User creation completed!";
        if (strpos($result, $success_message) !== false) {
            return ["success" => true];
        } else {
            return ["sucess" => false, "message" => $result];
        }
    }

    public function delete()
    {
        if ((!$this->database) || (!$this->username)) return ["success" => false, "message" => "Database or username is not set!"];
    }
}
