<?php

// Authentication Routes

use App\Models\Message;
use App\Models\Notification;
use App\Models\Server;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

require_once __DIR__ . "/Auth/auth.php";
require_once __DIR__ . "/MicroApiRoutes/users.php";
require_once __DIR__ . "/MicroApiRoutes/projects.php";
require_once __DIR__ . "/MicroApiRoutes/tasks.php";
require_once __DIR__ . "/MicroApiRoutes/servers.php";
require_once __DIR__ . "/MicroApiRoutes/params.php";
require_once __DIR__ . "/MicroApiRoutes/steps.php";
require_once __DIR__ . "/MicroApiRoutes/payments.php";
require_once __DIR__ . "/MicroApiRoutes/messages.php";
require_once __DIR__ . "/MicroApiRoutes/notifications.php";
require_once __DIR__ . "/MicroApiRoutes/notes.php";
require_once __DIR__ . "/MicroApiRoutes/main.php";


use Illuminate\Http\Request;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* 
        '(select t4.task_id from task_user as t4 where t4.active=1 and t4.user_id=t1.id limit 1) as active_task_id',
        '(select t5.step_id from tasks t5 where t4.id=(select t4.task_id from task_user as t4 where t4.active=1 and t4.user_id=t1.id limit 1))',
        '(select t6.project_id from steps t6 where t6.id=(select t5.step_id from tasks t5 where t4.id=(select t4.task_id from task_user as t4 where t4.active=1 and t4.user_id=t1.id limit 1)))',
*/

Route::get('/test', function (Request $request) {
    $time = new DateTime();
    $time2 = new DateTime('2021-02-02 12:30:00');

    // $res = DB::table('task_user as t1')
    //     ->leftJoin('tasks as t2', 't2.id', '=', 't1.task_id')
    //     ->leftJoin('steps as t3', 't3.id', '=', 't2.step_id')
    //     ->leftJoin('projects as t4', 't4.id', '=', 't3.project_id')
    //     ->select(
    //         't2.id',
    //         't4.id as project_id',
    //         't4.title as project_title',
    //         't2.title as task_title',
    //         't2.time',
    //         't2.type',
    //         't2.deadline',
    //         DB::raw('(select count(t5.id) from unread_messages as t5 where t5.user_id=t1.user_id) as unread_count'),
    //         DB::raw('(select SUM(TIMESTAMPDIFF(MINUTE, t6.created_at, t6.stopped_at)) from task_watchers as t6 where t6.task_user_id=t1.id) as time_spent'),
    //         DB::raw('(select TIMESTAMPDIFF(MINUTE, (select max(t7.created_at) from task_watchers as t7 where t7.task_user_id=t1.id and t7.stopped_at IS NULL), CURRENT_TIMESTAMP)) as additional_time')
    //     )->get();
    // $user_id = null;
    // $res = DB::table('users as t1')
    //     ->leftJoin('roles as t2', 't2.id', '=', 't1.role_id')
    //     ->select(
    //         't1.id as user_id',
    //         't1.name as user_name',
    //         't1.photo as user_photo',
    //         't1.color as user_color',
    //         't2.name as user_role',
    //         't1.active_task_id',
    //         DB::raw('(1) as worked')
    //     )
    //     ->whereNotIn('t2.name', ['Admin', 'Manager'])
    //     ->when($user_id, function ($query) use ($user_id) {
    //         return $query->where('t1.id', $user_id);
    //     })
    //     ->paginate(4)
    //     ->toArray();
    // $user_id = "1a010639-e3bd-4c49-8672-abc4181f9d4c";
    // $project_id = null;
    // $res = DB::table('task_user as t1')
    //     ->leftJoin('tasks as t2', 't2.id', '=', 't1.task_id')
    //     ->leftJoin('steps as t3', 't3.id', '=', 't2.step_id')
    //     ->leftJoin('projects as t4', 't4.id', '=', 't3.project_id')
    //     ->leftJoin('users as t8', 't8.id', '=', 't1.user_id')
    //     ->select(
    //         't2.id',
    //         't4.id as project_id',
    //         't4.title as project_title',
    //         't2.title as task_title',
    //         't2.time',
    //         't2.type',
    //         't2.deadline',
    //         't8.active_task_id',
    //         DB::raw('(select count(t5.id) from unread_messages as t5 where t5.user_id=?) as unread_count'),
    //         DB::raw('(select SUM(TIMESTAMPDIFF(MINUTE, t6.created_at, t6.stopped_at)) from task_watchers as t6 where t6.task_user_id=t1.id) as time_spent'),
    //         DB::raw('(select TIMESTAMPDIFF(MINUTE, (select max(t7.created_at) from task_watchers as t7 where t7.task_user_id=t1.id and t7.stopped_at IS NULL), CURRENT_TIMESTAMP)) as additional_time')
    //     )
    //     ->setBindings([$user_id])
    //     ->where('t1.user_id', $user_id)
    //     ->when($project_id, function ($q) use ($project_id) {
    //         return $q->where('t4.id', $project_id);
    //     })
    //     ->get();
    // $user_id = "2bdf2f24-82b7-413b-aac7-3a7922a0b741";
    // $res = Message::from('messages as t1')->rightJoin('unread_messages as t2', 't1.id', '=', 't2.message_id')->leftJoin('users as t3', 't1.user_id', '=', 't3.id')->select(
    //     't1.text',
    //     't1.created_at as sent_at',
    //     't3.photo as user_photo',
    //     't3.color as user_color',
    //     't3.name as user_name',
    //     DB::raw('(select t5.title from projects as t5 where t5.id=(select t6.project_id from steps as t6 where t6.id=(select t7.step_id from tasks as t7 where t7.id=t1.task_id))) as project_title'),
    //     DB::raw('(select t4.title from tasks t4 where t4.id=t1.task_id) as task_title')
    // )->where('t2.user_id', $user_id)->with('files')->get();
    // $res = $request->only(['server.id', 'server.name']);
    // unset($res['server']['id']);
    // $command = "sudo su -c "echo 'username:1234' | chpasswd" root"
    // $command1 = "echo 'username:password' | sudo chpasswd2>&1";
    // $res = shell_exec($command1);
    // $res = date('Y-m-d H:i:s');
    // sleep(2);
    // $res2 = date('Y-m-d H:i:s');
    // return response()->json($res < $res2);
    // $format = 'Y-m-d H-i-s';
    // $res = new DateTime('2021-02-15 12:12:12');
    // $res2 = new DateTime();
    // return response()->json([
    //     'hours' => $res->diff($res2)
    // ]);
    //-------------------------------------------------------------------------------------
    // $project_id = null;
    // $ress = DB::table('task_user as t1')
    //     ->leftJoin('tasks as t2', 't2.id', '=', 't1.task_id')
    //     ->leftJoin('steps as t3', 't3.id', '=', 't2.step_id')
    //     ->leftJoin('projects as t4', 't4.id', '=', 't3.project_id')
    //     ->leftJoin('users as t8', 't8.id', '=', 't1.user_id')
    //     ->leftJoin('task_user as c1', 'c1.id', '=', 't8.active_task_id')
    //     ->select(
    //         't2.id',
    //         't4.id as project_id',
    //         't4.title as project_title',
    //         't2.title as task_title',
    //         't2.time',
    //         't2.type',
    //         't2.deadline',
    //         'c1.task_id as active_task_id',
    //         DB::raw('(select count(t5.id) from unread_messages as t5 where t5.user_id=? and t5.message_id in (select n1.id from messages as n1 where n1.task_id=t1.task_id)) as unread_count'),
    //         DB::raw('(select SUM(TIMESTAMPDIFF(MINUTE, t6.created_at, t6.stopped_at)) from task_watchers as t6 where t6.task_user_id=t1.id) as time_spent'),
    //         DB::raw('(select TIMESTAMPDIFF(MINUTE, (select max(t7.created_at) from task_watchers as t7 where t7.task_user_id=t1.id and t7.stopped_at IS NULL), CURRENT_TIMESTAMP)) as additional_time'),
    //         //New Columns
    //         DB::raw('(select min(h1.created_at) from task_watchers as h1 where h1.task_user_id=t1.id) as minimum_date'),
    //         DB::raw('(select max(h1.stopped_at) from task_watchers as h1 where h1.task_user_id=t1.id) as maximum_date'),
    //         't8.working_days',
    //         't8.work_start_time',
    //         't8.work_end_time',
    //         't8.pause_start_time',
    //         't8.pause_end_time',
    //         DB::raw('(case when exists(select j1.stopped_at from task_watchers as j1 where j1.created_at=(select max(j2.created_at) from task_watchers as j2 where j2.task_user_id=t1.task_user_id))) as has_last_stop')
    //     )
    //     ->setBindings(["451995dd-bbcd-4d50-b52e-d8d6ebf09c92"])
    //     ->where('t1.user_id', '451995dd-bbcd-4d50-b52e-d8d6ebf09c92')
    //     ->when($project_id, function ($q) use ($project_id) {
    //         return $q->where('t4.id', $project_id);
    //     })
    //     //Dont forget to remove this
    //     ->orderBy('t1.id', 'desc')
    //     ->get();

    // $tasks = [];
    // foreach ($ress as $task_key => $task) {
    //     $curr_task = [
    //         'id' => $task->id,
    //         'project_id' => $task->project_id,
    //         'task_title' => $task->task_title,
    //         'time' => $task->time, //Given time by admin
    //         'type' => $task->type,
    //         'deadline' => $task->deadline,
    //         'active_task_id' => $task->active_task_id,
    //         'time_spent' => $task->time_spent,
    //         'unread_count' => $task->unread_count,
    //         'additional_time' => $task->additional_time
    //     ];

    //     //If there is no minimum_date then task Has never been active before
    //     if (is_null($task->minimum_date)) continue;



    //     //--------------- Declaration -----------//
    //     $curr_date_time = new DateTime();
    //     //--------------- Declaration -----------//



    //     //--------------- Pause time & Working time in minutes Per day -----------//
    //     //--------------- Pause time & Working time in minutes Per day -----------//



    //     //--------------- Subtracting Pause times ---------------//
    //     $begin = new DateTime($task->minimum_date);
    //     $end = new DateTime($task->maximum_date);
    //     if ($begin->diff($end)->d == 0) {
    //         //Its the first and the last day
    //         continue;
    //     }

    //     for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
    //         //--------------- Declaration -----------//
    //         $minimum_date = new DateTime($task->minimum_date);
    //         $maximum_date = new DateTime($task->maximum_date);
    //         $working_days = json_decode($task->working_days);
    //         $Date = explode(' ', $i->format('Y-m-d H:i:s'))[0];
    //         $work_start_time = new DateTime($Date . ' ' . $task->work_start_time);
    //         $work_end_time =  new DateTime($Date . ' ' . $task->work_end_time);
    //         $pause_start_time =  new DateTime($Date . ' ' . $task->pause_start_time);
    //         $pause_end_time =  new DateTime($Date . ' ' . $task->pause_end_time);
    //         $work_time_in_minutes = 0;
    //         $diff_in_wt = $work_start_time->diff($work_end_time);
    //         $work_time_in_minutes += $diff_in_wt->h * 60 + $diff_in_wt->i;
    //         $diff_in_pt = $pause_start_time->diff($pause_end_time);
    //         $work_time_in_minutes -= $diff_in_pt->h * 60 + $diff_in_pt->i;
    //         $pause_time_in_minutes = 1440 - $work_time_in_minutes;
    //         $pause_time_diff = $pause_start_time->diff($pause_end_time);
    //         $pause_time_diff = $pause_time_diff->h * 60 + $pause_time_diff->i;
    //         $start_day = new DateTime($Date);
    //         $start_day_to_work_start = $start_day->diff($work_start_time);
    //         $start_day_to_work_start = $start_day_to_work_start->h * 60 + $start_day_to_work_start->i;
    //         $end_day = clone $start_day;
    //         $end_day->modify('+1 day');
    //         $work_end_to_end_day = $work_end_time->diff($end_day);
    //         $work_end_to_end_day = $work_end_to_end_day->h * 60 + $work_end_to_end_day->i;
    //         $weekDay = $i->format('N');
    //         //--------------- Declaration -----------//

    //         if ($i->diff($begin)->d == 0) {
    //             //Its the first day iteration
    //             $given_time = $minimum_date;
    //             if ($given_time < $work_start_time) {
    //                 //Start-day -> Work-start
    //                 $diff = $given_time->diff($work_start_time);
    //                 $curr_task['time_spent'] -= $diff->h * 60 + $diff->i;
    //                 $curr_task['time_spent'] -= $pause_time_in_minutes;
    //                 $curr_task['time_spent'] -= $work_end_to_end_day;
    //             } else if ($given_time >= $work_start_time && $given_time < $pause_start_time) {
    //                 //Work->start -> Pause->start
    //                 $curr_task['time_spent'] -= $pause_time_diff;
    //                 $curr_task['time_spent'] -= $work_end_to_end_day;
    //             } else if ($given_time >= $pause_start_time && $given_time <= $pause_end_time) {
    //                 //Between Pauses
    //                 $diff = $given_time->diff($pause_end_time);
    //                 $curr_task['time_spent'] -= $diff->h * 60 + $diff->i;
    //                 $curr_task['time_spent'] -= $work_end_to_end_day;
    //             } else if ($given_time > $pause_end_time && $given_time < $work_end_time) {
    //                 //Pause->end -> Work-end
    //                 $curr_task['time_spent'] -= $work_end_to_end_day;
    //             } else if ($given_time >= $work_end_time) {
    //                 //Work->end -> End-day
    //                 $diff = $given_time->diff($end_day);
    //                 $curr_task['time_spent'] -= $diff->h * 60 + $diff->i;
    //             }
    //         } else if ($i->diff($end)->d == 0) {
    //             //The last day
    //             $t = new DateTime();
    //             $_time = $t->format('H:i:m');
    //             $_date = $i->format('Y-m-d');
    //             $given_time = new DateTime($_date . ' ' . $_time);
    //             if ($curr_date_time->diff($i)->d !== 0) {
    //                 if ($given_time < $work_start_time) {
    //                     //Start-day -> Work-start
    //                     $diff = $given_time->diff($work_start_time);
    //                     $curr_task['time_spent'] -= $diff->h * 60 + $diff->i;
    //                     $curr_task['time_spent'] -= $pause_time_in_minutes;
    //                     $curr_task['time_spent'] -= $work_end_to_end_day;
    //                 } else if ($given_time >= $work_start_time && $given_time < $pause_start_time) {
    //                     //Work->start -> Pause->start
    //                     $curr_task['time_spent'] -= $pause_time_diff;
    //                     $curr_task['time_spent'] -= $work_end_to_end_day;
    //                 } else if ($given_time >= $pause_start_time && $given_time <= $pause_end_time) {
    //                     //Between Pauses
    //                     $diff = $given_time->diff($pause_end_time);
    //                     $curr_task['time_spent'] -= $diff->h * 60 + $diff->i;
    //                     $curr_task['time_spent'] -= $work_end_to_end_day;
    //                 } else if ($given_time > $pause_end_time && $given_time < $work_end_time) {
    //                     //Pause->end -> Work-end
    //                     $curr_task['time_spent'] -= $work_end_to_end_day;
    //                 } else if ($given_time >= $work_end_time) {
    //                     //Work->end -> End-day
    //                     $diff = $given_time->diff($end_day);
    //                     $curr_task['time_spent'] -= $diff->h * 60 + $diff->i;
    //                 }
    //             } else {
    //                 //Today is last day
    //             }
    //         } else {
    //             //Between last and first days
    //         }
    //     }
    //     //--------------- Subtracting Pause times ---------------//




    //     //---------------Append Current Task To Task List---------------//
    //     $tasks[] = $curr_task;
    //     //---------------Append Current Task To Task List---------------//
    // }

    // return response()->json([
    //     'tasks' => $tasks,
    //     'test' => null - 1,
    // ]);


    // $ress = DB::table("task_watchers as t1")->leftJoin('task_user as t2', 't2.id', '=', 't1.task_user_id')
    //     ->leftJoin('users as t3', 't3.id', '=', 't2.user_id')
    //     ->where('t1.task_user_id', 1)->select(
    //         DB::raw('(select min(a1.created_at) from task_watchers as a1 where a1.task_user_id=t1.task_user_id) as minimum_date'),
    //         DB::raw('(select max(a1.stopped_at) from task_watchers as a1 where a1.task_user_id=t1.task_user_id) as maximum_date'),
    //         't3.working_days',
    //         't3.pause_end_time',
    //         't3.pause_start_time',
    //         't3.work_start_time',
    //         't3.work_end_time',
    //         DB::raw('(select sum(TIMESTAMPDIFF(MINUTE, b1.created_at, b1.stopped_at)) from task_watchers as b1 where b1.task_user_id=t1.task_user_id) as time_spent'),
    //         DB::raw("(select sum(TIMESTAMPDIFF(MINUTE, c1.created_at, CURRENT_TIMESTAMP)) from task_watchers as c1 where c1.task_user_id=t1.task_user_id) as additional_time")
    //     )->first();
    // $res = [
    //     'minimum_date' => new DateTime($ress->minimum_date),
    //     'maximum_date' => new DateTime($ress->maximum_date),
    //     'working_days' => json_decode($ress->working_days),
    //     'pause_end_time' => $ress->pause_end_time,
    //     'pause_start_time' => $ress->pause_start_time,
    //     'work_start_time' => $ress->work_start_time,
    //     'work_end_time' => $ress->work_end_time,
    //     'time_spent' => $ress->time_spent,
    //     "additional_time" => $ress->additional_time
    // ];

    // $work_time_in_minutes = 0;
    // $wst = new DateTime($res['work_start_time']);
    // $wet = new DateTime($res['work_end_time']);
    // $diff_in_wt = $wst->diff($wet);
    // $work_time_in_minutes += $diff_in_wt->h * 60 + $diff_in_wt->i;
    // $pst = new DateTime($res['pause_start_time']);
    // $pet =  new DateTime($res['pause_end_time']);
    // $diff_in_pt = $pst->diff($pet);
    // $work_time_in_minutes -= $diff_in_pt->h * 60 + $diff_in_pt->i;
    // $pause_time_in_minutes = 1440 - $work_time_in_minutes;

    // $begin = $res['minimum_date'];
    // $end   = $res['maximum_date'];
    // $curr_time = new DateTime();
    // $work_st = new DateTime($res['work_start_time']);
    // $work_et = new DateTime($res['work_end_time']);
    // $pause_st = new DateTime($res['pause_start_time']);
    // $pause_et = new DateTime($res['pause_end_time']);
    // for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
    //     $weekDay = $i->format('N');
    //     if ($i == $begin) {
    //         if ($i->diff($curr_time)->d > 0) {
    //             $curr_time = $res['minimum_date'];
    //             $tm = clone $curr_time;
    //             $tm->modify('tomorrow');
    //             $till_tomorrow = $work_et->diff($tm);
    //             if ($curr_time <= $work_st) {
    //                 $diff = $pause_st->diff($pause_et);
    //                 $res['time_spent'] -= $diff->h * 60 + $diff->i;
    //                 $diff = $work_st->diff($curr_time);
    //                 $res['time_spent'] -= $diff->h * 60 + $diff->i;
    //                 $res['time_spent'] -= $till_tomorrow->h * 60 + $till_tomorrow->i;
    //             } else if ($curr_time >= $work_et) {
    //                 $diff = $curr_time->diff($tm);
    //                 $res['time_spent'] -= $diff->h * 60 + $diff->i;
    //             } else if ($curr_time > $work_st && $curr_time < $pause_st) {
    //                 $diff = $pause_st->diff($pause_et);
    //                 $res['time_spent'] -= $diff->h * 60 + $diff->i;
    //                 $res['time_spent'] -= $till_tomorrow->h * 60 + $till_tomorrow->i;
    //             } else if ($curr_time > $pause_et && $curr_time < $work_et) {
    //                 $res['time_spent'] -= $till_tomorrow->h * 60 + $till_tomorrow->i;
    //             } else if ($curr_time >= $pause_st && $curr_time <= $pause_et) {
    //                 $diff = $pause_et->diff($curr_time);
    //                 $res['time_spent'] -= $diff->h * 60 + $diff->i;
    //                 $res['time_spent'] -= $till_tomorrow->h * 60 + $till_tomorrow->i;
    //             }
    //         } else {
    //             $given_time = $res['minimum_date'];
    //             if ($given_time >= $work_et) {
    //                 $res['time_spent'] = 0;
    //                 $res['additional_time'] = 0;
    //             } else if ($given_time < $work_et && $given_time > $pause_et) {
    //                 $diff = $given_time
    //                 $res['time_spent'] -= $till_tomorrow->h * 60 + $till_tomorrow->i;
    //             } else if ($curr_time > $work_st && $curr_time < $pause_st) {
    //                 $diff = $pause_st->diff($pause_et);
    //                 $res['time_spent'] -= $diff->h * 60 + $diff->i;
    //                 $res['time_spent'] -= $till_tomorrow->h * 60 + $till_tomorrow->i;
    //             } else if ($curr_time > $pause_et && $curr_time < $work_et) {
    //                 $res['time_spent'] -= $till_tomorrow->h * 60 + $till_tomorrow->i;
    //             } else if ($curr_time >= $pause_st && $curr_time <= $pause_et) {
    //                 $diff = $pause_et->diff($curr_time);
    //                 $res['time_spent'] -= $diff->h * 60 + $diff->i;
    //                 $res['time_spent'] -= $till_tomorrow->h * 60 + $till_tomorrow->i;
    //             }
    //         }
    //     } else if ($i == $end) {
    //     } else {
    //     }
    // }
    // $diff = $res['minimum_date']->diff($res['maximum_date']);
    // $startDate = explode(" ", $ress->minimum_date)[0];
    // $endDate = explode(" ", $ress->maximum_date)[0];
    // $week__days = [
    //     'Monday',
    //     'Tuesday',
    //     'Wednesday',
    //     'Thursday',
    //     'Friday',
    //     'Saturday',
    //     'Sunday'
    // ];
    // $week_days_in_numbers = [0, 1, 2, 3, 4, 5, 6];
    // $resultDays = array(
    //     'Monday' => 0,
    //     'Tuesday' => 0,
    //     'Wednesday' => 0,
    //     'Thursday' => 0,
    //     'Friday' => 0,
    //     'Saturday' => 0,
    //     'Sunday' => 0
    // );
    // $startDate = new DateTime($startDate);
    // $endDate = new DateTime($endDate);
    // while ($startDate <= $endDate) {
    //     $timestamp = strtotime($startDate->format('d-m-Y'));
    //     $weekDay = date('l', $timestamp);
    //     $resultDays[$weekDay] = $resultDays[$weekDay] + 1;
    //     $startDate->modify('+1 day');
    // }

    // $not_working_days = array_diff($week_days_in_numbers, $res['working_days']);

    // $curr_date_time = new DateTime();


    // if ($res['minimum_date']->diff($curr_date_time)->d > 0) {
    //     $work_st = new DateTime($res['work_start_time']);
    //     $work_et = new DateTime($res['work_end_time']);
    //     $pause_st = new DateTime($res['pause_start_time']);
    //     $pause_et = new DateTime($res['pause_end_time']);
    //     $curr_time = $res['minimum_date'];
    //     if ($curr_time >= $work_et) {
    //         $res['time_spent'] += $pause_time_in_minutes;
    //     } else if ($curr_time > $work_st && $curr_time < $pause_st) {
    //         $diff = $curr_time->diff($pause_st);
    //         $res['time_spent'] += $diff->h * 60 + $diff->i;
    //         $diff = $pause_et->diff($work_et);
    //         $res['time_spent'] += $diff->h * 60 + $diff->i;
    //     } else if ($curr_time > $pause_et && $curr_time < $work_et) {
    //         $d = $curr_time->diff($work_et);
    //         $diff = $d->h * 60 + $d->i;
    //         $res['time_spent'] += $diff;
    //     } else if ($curr_time >= $pause_st && $curr_time <= $pause_et) {
    //         $diff = $pause_et->diff($work_et);
    //         $res['time_spent'] += $diff->h * 60 + $diff->i;
    //     }
    // }
    // $date = date('Y-m-d');
    // $unixTimestamp = strtotime($date);
    // $CurrentDayOfWeek = date("l", $unixTimestamp);
    // foreach ($resultDays as $k => $rd) {
    //     if (in_array(array_search($k, $week__days), $res['working_days'])) {
    //         if ($CurrentDayOfWeek == $k) {
    //             if ($rd == 0) continue;
    //             $res['time_spent'] -= ($rd - 1) * $pause_time_in_minutes;
    //             $work_st = new DateTime($res['work_start_time']);
    //             $work_et = new DateTime($res['work_end_time']);
    //             $pause_st = new DateTime($res['pause_start_time']);
    //             $pause_et = new DateTime($res['pause_end_time']);
    //             $curr_time = new DateTime();
    //             $start_of_the_day = new DateTime('0:0:0');
    //             if ($curr_time <= $work_st)
    //                 continue;
    //             else if ($curr_time >= $work_et) {
    //                 $res['time_spent'] -= $pause_time_in_minutes;
    //                 continue;
    //             } else if ($curr_time > $work_st && $curr_time < $pause_st) {
    //                 $diff = $work_st->diff($start_of_the_day);
    //                 $res['time_spent'] -= $diff->h * 60 + $diff->i;
    //             } else if ($curr_time > $pause_et && $curr_time < $work_et) {
    //                 $d = $work_st->diff($start_of_the_day);
    //                 $diff = $d->h * 60 + $d->i;
    //                 $h = $pause_st->diff($pause_et);
    //                 $diff += $h->h * 60 + $h->i;
    //                 $res['time_spent'] -= $diff;
    //             } else if ($curr_time >= $pause_st && $curr_time <= $pause_et) {
    //                 $diff = $work_st->diff($start_of_the_day);
    //                 $d = $curr_time->diff($pause_st);
    //                 $diff += $d->h * 60 + $d->i;
    //                 $res['time_spent'] -= $diff;
    //             }
    //         } else {
    //             $res['time_spent'] -= $rd * $pause_time_in_minutes;
    //         }
    //     } else {
    //         $res['time_spent'] -= $rd * 1440;
    //     }
    // }
    // foreach ($res['working_days'] as $wd) {
    //     if ($CurrentDayOfWeek == $week__days[$wd]) {
    //         $work_st = new DateTime($res['work_start_time']);
    //         $work_et = new DateTime($res['work_end_time']);
    //         $pause_st = new DateTime($res['pause_start_time']);
    //         $pause_et = new DateTime($res['pause_end_time']);
    //         $curr_time = new DateTime();
    //         $start_of_the_day = new DateTime('0:0:0');
    //         if ($curr_time <= $work_st)
    //             continue;
    //         if ($curr_time >= $work_et) {
    //             $res['time_spent'] -= $resultDays[$week__days[$wd]] * $pause_time_in_minutes;
    //             continue;
    //         }
    //         if ($curr_time > $work_st && $curr_time < $pause_st) {
    //             $diff = $curr_time->diff($start_of_the_day);
    //             $res['time_spent'] -= $diff->h * 60 + $diff->i;
    //         } else if ($curr_time > $pause_et && $curr_time < $work_et) {
    //             $d = $curr_time->diff($start_of_the_day);
    //             $diff = $d->h * 60 + $d->i;
    //             $h = $pause_st->diff($pause_et);
    //             $diff += $h->h * 60 + $h->i;
    //             $res['time_spent'] -= $diff;
    //         } else if ($curr_time >= $pause_st && $curr_time <= $pause_et) {
    //             $diff = $curr_time->diff($start_of_the_day);
    //             $d = $curr_time->diff($pause_st);
    //             $diff += $d->h * 60 + $d->i;
    //             $res['time_spent'] -= $diff;
    //         }
    //     } else {
    //         $res['time_spent'] -= $resultDays[$week__days[$wd]] * $pause_time_in_minutes;
    //     }
    // }
    // $date = "2002-12-02";

    //Convert the date string into a unix timestamp.
    // $unixTimestamp = strtotime($date);
    // return response()->json([
    //     'res' => $res,
    //     'difference' => $diff,
    //     'sfi' => $resultDays,
    //     'nwd' => $not_working_days,
    //     'ss' => $work_time_in_minutes,
    //     'll' => $unixTimestamp
    // ]);
});

/*

$ress = DB::table("task_watchers as t1")->leftJoin('task_user as t2', 't2.id', '=', 't1.task_user_id')
        ->leftJoin('users as t3', 't3.id', '=', 't2.user_id')
        ->where('t1.task_user_id', 1)->select(
            DB::raw('(select min(a1.created_at) from task_watchers as a1 where a1.task_user_id=t1.task_user_id) as minimum_date'),
            DB::raw('(select max(a1.stopped_at) from task_watchers as a1 where a1.task_user_id=t1.task_user_id) as maximum_date'),
            't3.working_days',
            't3.pause_end_time',
            't3.pause_start_time',
            't3.work_start_time',
            't3.work_end_time',
            DB::raw('(select sum(TIMESTAMPDIFF(MINUTE, b1.created_at, b1.stopped_at)) from task_watchers as b1 where b1.task_user_id=t1.task_user_id) as time_spent'),
            DB::raw("(select sum(TIMESTAMPDIFF(MINUTE, c1.created_at, CURRENT_TIMESTAMP)) from task_watchers as c1 where c1.task_user_id=t1.task_user_id) as additional_time")
        )->first();
    $res = [
        'minimum_date' => new DateTime($ress->minimum_date),
        'maximum_date' => new DateTime($ress->maximum_date),
        'working_days' => json_decode($ress->working_days),
        'pause_end_time' => $ress->pause_end_time,
        'pause_start_time' => $ress->pause_start_time,
        'work_start_time' => $ress->work_start_time,
        'work_end_time' => $ress->work_end_time,
        'time_spent' => $ress->time_spent,
        "additional_time" => $ress->additional_time
    ];
    $diff = $res['minimum_date']->diff($res['maximum_date']);
    $startDate = explode(" ", $ress->minimum_date)[0];
    $endDate = explode(" ", $ress->maximum_date)[0];
    $week__days = [
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday'
    ];
    $week_days_in_numbers = [0, 1, 2, 3, 4, 5, 6];
    $resultDays = array(
        'Monday' => 0,
        'Tuesday' => 0,
        'Wednesday' => 0,
        'Thursday' => 0,
        'Friday' => 0,
        'Saturday' => 0,
        'Sunday' => 0
    );

    // change string to date time object 
    $startDate = new DateTime($startDate);
    $endDate = new DateTime($endDate);

    // iterate over start to end date 
    while ($startDate <= $endDate) {
        // find the timestamp value of start date 
        $timestamp = strtotime($startDate->format('d-m-Y'));

        // find out the day for timestamp and increase particular day 
        $weekDay = date('l', $timestamp);
        $resultDays[$weekDay] = $resultDays[$weekDay] + 1;

        // increase startDate by 1 
        $startDate->modify('+1 day');
    }

    $not_working_days = array_diff($week_days_in_numbers, $res['working_days']);

    foreach ($not_working_days as $nwd) {
        $res['time_spent'] -= $resultDays[$week__days[$nwd]] * 1440;
    }
    $work_time_in_minutes = 0;
    $wst = new DateTime($res['work_start_time']);
    $wet = new DateTime($res['work_end_time']);
    $diff_in_wt = $wst->diff($wet);
    $work_time_in_minutes += $diff_in_wt->h * 60 + $diff_in_wt->i;
    $pst = new DateTime($res['pause_start_time']);
    $pet =  new DateTime($res['pause_end_time']);
    $diff_in_pt = $pst->diff($pet);
    $work_time_in_minutes -= $diff_in_pt->h * 60 + $diff_in_pt->i;
    $pause_time_in_minutes = 1440 - $work_time_in_minutes;
    foreach ($res['working_days'] as $wd) {
        $res['time_spent'] -= $resultDays[$week__days[$wd]] * $pause_time_in_minutes;
        $res['additional_time'] += $resultDays[$week__days[$wd]] * $pause_time_in_minutes;
    }
    return response()->json([
        'res' => $res,
        'difference' => $diff,
        'sfi' => $resultDays,
        'nwd' => $not_working_days,
        'ss' => $work_time_in_minutes
    ]);

*/

//-------------If else structure-------------//
/*
if ($given_time < $work_start_time) {
    //Start-day -> Work-start
} else if ($given_time >= $work_start_time && $given_time < $pause_start_time) {
    //Work->start -> Pause->start
} else if ($given_time >= $pause_start_time && $given_time <= $pause_end_time) {
    //Between Pauses
} else if ($given_time > $pause_end_time && $given_time < $work_end_time) {
    //Pause->end -> Work-end
} else if ($given_time >= $work_end_time) {
    //Work->end -> End-day
}
*/
//-------------If else structure-------------//
