<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class Logger
{
    public static function serverChange($info, $useremail, $action)
    {
        $logString = "[By_ $useremail] - [Action_ $action] => \n$info";

        Log::channel('server')->info($logString);
    }

    public static function cronError($info, $notification_id, $error = true)
    {
        $action = $error ? 'ERROR' : 'CHANGE';
        $logString = "[Notification_id_ $notification_id] - [$action] =>\n $info";
        Log::channel('cron')->info($logString);
    }

    public static function newCron($info, $command)
    {
        $logString = "[COMMAND_ $command] =>\n $info";
        Log::channel('cron')->info($logString);
    }
}
