<?php

namespace App\Traits\Tasks;

use Illuminate\Support\Facades\DB;

trait Query
{
    public static function getUserTasks($user_id, $project_id = null)
    {
        return DB::table('task_user as t1')
            ->leftJoin('tasks as t2', 't2.id', '=', 't1.task_id')
            ->leftJoin('steps as t3', 't3.id', '=', 't2.step_id')
            ->leftJoin('projects as t4', 't4.id', '=', 't3.project_id')
            ->leftJoin('users as t8', 't8.id', '=', 't1.user_id')
            ->leftJoin('task_user as c1', 'c1.id', '=', 't8.active_task_id')
            ->select(
                't2.id',
                't4.id as project_id',
                't4.title as project_title',
                't2.title as task_title',
                't2.time',
                't2.type',
                't2.deadline',
                'c1.task_id as active_task_id',
                't8.back_up_active_task_id',
                DB::raw('(select count(t5.id) from unread_messages as t5 where t5.user_id=? and t5.message_id in (select n1.id from messages as n1 where n1.task_id=t1.task_id)) as unread_count'),
                DB::raw('(select SUM(TIMESTAMPDIFF(MINUTE, t6.created_at, t6.stopped_at)) from task_watchers as t6 where t6.task_user_id=t1.id) as time_spent'),
                DB::raw('(select TIMESTAMPDIFF(MINUTE, (select max(t7.created_at) from task_watchers as t7 where t7.task_user_id=t1.id and t7.stopped_at IS NULL), CURRENT_TIMESTAMP)) as additional_time')
            )
            ->setBindings([$user_id])
            ->where('t1.user_id', $user_id)
            ->when($project_id, function ($q) use ($project_id) {
                return $q->where('t4.id', $project_id);
            })
            ->get();
    }

    //* Get all user tasks for Admin
    public static function getAllUserTasks($user_id, $project_id = null)
    {
        return DB::table('task_user as t1')
            ->leftJoin('tasks as t2', 't2.id', '=', 't1.task_id')
            ->leftJoin('steps as t3', 't3.id', '=', 't2.step_id')
            ->leftJoin('projects as t4', 't4.id', '=', 't3.project_id')
            ->select(
                't2.id',
                't4.id as project_id',
                't4.title as project_title',
                't2.title as task_title',
                't2.time',
                't2.type',
                't2.deadline',
                't1.user_id',
                't1.id as task_user_id',
                DB::raw('(select count(t5.id) from unread_messages as t5 where t5.user_id=? and t5.message_id in (select a1.id from messages as a1 where a1.task_id=t1.task_id)) as unread_count'),
                DB::raw('(select SUM(TIMESTAMPDIFF(MINUTE, t6.created_at, t6.stopped_at)) from task_watchers as t6 where t6.task_user_id=t1.id) as time_spent'),
                DB::raw('(select TIMESTAMPDIFF(MINUTE, (select max(t7.created_at) from task_watchers as t7 where t7.task_user_id=t1.id and t7.stopped_at IS NULL), CURRENT_TIMESTAMP)) as additional_time')
            )
            ->setBindings([$user_id])
            ->when($project_id, function ($q) use ($project_id) {
                return $q->where('t4.id', $project_id);
            })
            ->get();
    }

    //* Get all users list for admin in tasks view route with pagination
    public static function getUserListForAdmin($user_id = null)
    {
        return DB::table('users as t1')
            ->leftJoin('roles as t2', 't2.id', '=', 't1.role_id')
            ->select(
                't1.id as user_id',
                't1.name as user_name',
                't1.photo as user_photo',
                't1.color as user_color',
                't2.name as user_role',
                't1.active_task_id',
                't1.back_up_active_task_id',
                DB::raw('(1) as worked')
            )
            ->whereNotIn('t2.name', ['Admin', 'Manager'])
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('t1.id', $user_id);
            })
            ->paginate(4)
            ->toArray();
    }
}
