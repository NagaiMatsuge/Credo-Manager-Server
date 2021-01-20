<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Validator;

/*
 * At is another way of using cron tabs
 * At gives ability to schedule commands at exact date once
 * Source https://linuxize.com/post/at-command-in-linux/
 *         -----Commands------
 *     sudo apt-get install at
 *     systemctl status atd
 *     atq —- to list all the commands in queue
 *     atrm {job_number} — to remove the job
 *         -----Commands------
 */

class At
{
    public static function newAtCommand($command, $date)
    {
        if (!$date) $date = now();
        $validator = Validator::make(['date' => $date], [
            'date' => 'date_format:Y-m-d H:i:s'
        ]);
        if ($validator->fails()) throw new Exception("Date format is not valid");
        $symbols_to_be_removed = ['.', ':', ' ', '-'];
        foreach ($symbols_to_be_removed as $sym) {
            $date = str_replace($sym, '', $date);
        }
        $date = trim($date);
        $shellCommand = "echo '$command' | at -t $date";
        $result = shell_exec($shellCommand);
        Logger::newCron($result, $shellCommand);
    }
}
