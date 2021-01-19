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
}
