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
        $date = substr_replace($date, '', -2, 2);
        $date .= ".00";
        $shellCommand = "echo '$command' | at -t $date";
        $result = shell_exec($shellCommand);

        Logger::newCron($result, $shellCommand);
        if (strpos($result, "job") !== false) {
            $word = 'job';
            $end_word = 'at';
            //Position of the job number in at
            $pos = strpos($result, $word) + strlen($word) + 1;
            $pos_end = strpos($result, $end_word) - 1;
            $res = substr($result, $pos, $pos_end - $pos);
            return ["success" => true, 'job' => $res];
        } else {
            return ["success" => false, "message" => $result];
        }
    }

    public static function deleteAtCommand($job)
    {
        $validator = Validator::make(['job_number' => $job], [
            'job_number' => 'required|integer'
        ]);
        if ($validator->fails()) throw new Exception("Invalid job number");
        $shellCommand = "atrm $job";
        $res = shell_exec($shellCommand);
        Logger::newCron($res, $shellCommand);
    }
}
