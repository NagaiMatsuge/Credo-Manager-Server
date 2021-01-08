<?php

namespace App\Traits\Tasks;

use Illuminate\Support\Facades\DB;

trait Query
{
    public static function getUserTasks($user_id)
    {
        return DB::select('select t30.* from (select
        (select t4.project_id from (select t5.* from steps as t5 where t5.id=(select t6.step_id from tasks as t6 where t6.id=t1.task_id)) as t4) as project_id,
        (select t10.title from projects as t10 where t10.id=(select t7.project_id from (select t8.* from steps as t8 where t8.id=(select t9.step_id from tasks as t9 where t9.id=t1.task_id)) as t7)) as project_title,
        t1.task_id as task_id,
        t1.active as active,
        (select t3.title from tasks as t3 where t3.id=t1.task_id) as task_title,
        (select t10.finished from tasks as t10 where t10.id=t1.task_id) as task_finished,
        (select t17.approved from tasks as t17 where t17.id=t1.task_id) as task_approved,
        t1.time,
        t1.type,
        t1.deadline,
        t1.tick,
        (select count(t12.id) from unread_messages as t12 where t12.user_id=t1.user_id and t12.message_id in (select t13.id from messages as t13 where t13.task_id=t1.task_id)) as unread_count,
        (select sum(t16.tt)/60 from (select t15.stopped_at - t15.created_at as tt from task_watchers as t15 where t15.user_id=t1.user_id and t15.task_id=t1.task_id and t15.stopped_at is not null) as t16) as time_spent
        from task_user as t1 where t1.user_id=?) as t30 where t30.task_approved=0', [$user_id]);
    }

    //* Get all user tasks for Admin
    public static function getAllUserTasks($user_id)
    {
        return DB::select('select t25.* from (select (select t2.id from tasks as t2 where t2.id=t20.task_id) as task_id, t20.active as status, (select t3.title from tasks as t3 where t3.id=t20.task_id) as title,
        (select t4.project_id from (select t5.* from steps as t5 where t5.id=(select t6.step_id from tasks as t6 where t6.id=t20.task_id)) as t4) as project_id,
        (select t10.title from projects as t10 where t10.id=(select t7.project_id from (select t8.* from steps as t8 where t8.id=(select t9.step_id from tasks as t9 where t9.id=t20.task_id)) as t7)) as project_title,
        t20.user_id,
        t20.type as type,
        t20.tick,
        t20.time,
        t20.deadline,
        (select sum(t24.tt)/60 from (select t25.stopped_at - t25.created_at as tt from task_watchers as t25 where t25.user_id=t20.user_id and t25.task_id=t20.task_id) as t24 ) as time_spent,
        (select count(t22.id) from unread_messages as t22 where t22.user_id=? and t22.message_id in (select t23.id from messages as t23 where t23.task_id=t20.task_id)) as unread_count,
        (select t26.approved from tasks as t26 where t26.id=t20.task_id) as task_approved
        from task_user as t20) as t25 where t25.task_approved=0', [$user_id]);
    }

    //* Get all users list for admin in tasks view route
    public static function getUserListForAdmin()
    {
        return DB::table('users as t19')->select(DB::raw('(select t11.name from roles as t11 where t11.id=(select t12.role_id from model_has_roles as t12 where t12.model_uuid=t19.id))as user_role'), 't19.id as user_id', 't19.name as user_name', 't19.photo as user_photo', DB::raw('(1) as worked'), 't19.color as user_color')->paginate(4)->toArray();
    }
}
