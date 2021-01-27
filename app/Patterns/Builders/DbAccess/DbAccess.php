<?php

namespace App\Patterns\Builders\DbAccess;

use App\Helpers\Logger;

class DbAccess
{
    private $username;
    private $password;
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
        if ((!$this->database) || (!$this->username) || (!$this->password)) return ["success" => false, "message" => "Database or username is not set!"];
        $shellCommand = "mysql_create $this->username $this->database $this->password 2>&1";
        $result = shell_exec($shellCommand);
        Logger::serverChange($result, $email, "Creating Database");
        $error_message = "ERROR";
        if (strpos($result, $error_message) == false) {
            return ["success" => false, 'message' => $result];
        } else {
            return ["success" => true];
        }
    }

    public function delete($email)
    {
        if ((!$this->database) && (!$this->username)) return ["success" => false, "message" => "Database or username is not set!"];
        $result = null;
        $error_message = "ERROR";
        if ($this->database) {
            $shellCommand = "mysql_delete_db $this->database 2>&1";
            $result = shell_exec($shellCommand);
            Logger::serverChange($result, $email, "Deleting Database");
            if (strpos($result, $error_message) == false) {
                return ["success" => false, 'message' => $result];
            } else {
                return ["success" => true];
            }
        }
        if ($this->username) {
            $shellCommand = "mysql_delete_user $this->username 2>&1";
            $result = shell_exec($shellCommand);
            Logger::serverChange($result, $email, "Deleting Database User");
            if (strpos($result, $error_message) == false) {
                return ["success" => false, 'message' => $result];
            } else {
                return ["success" => true];
            }
        }
    }

    public function update($email)
    {
        if ((!$this->password) && (!$this->username)) return ["success" => false, "message" => "Password or username is not set!"];
        $shellCommand = "mysql_update $this->username $this->password 2>&1";
        $result = shell_exec($shellCommand);
        Logger::serverChange($result, $email, "Updating Database User");
        $error_message = "ERROR";
        if (strpos($result, $error_message) == false) {
            return ["success" => false, 'message' => $result];
        } else {
            return ["success" => true];
        }
    }
}
