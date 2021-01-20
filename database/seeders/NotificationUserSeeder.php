<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $notificationUser = [];
        $admin = DB::table('users as t1')->leftJoin('model_has_roles as t2', 't1.id', '=', 't2.model_uuid')->leftJoin('roles as t3', 't2.role_id', '=', 't3.id')->where('t3.name', 'Admin')->select('t1.id')->first();
        $notificationIds = Notification::get()->pluck('id');
        $users = User::where('id', '<>', $admin->id)->get()->pluck('id');
        $user_count = count($users);
        $notificationCount = count($notificationIds);
        $notificationUser[] = [
            'to_user' => $users[rand(0, $user_count - 1)],
            'notification_id' => $notificationIds[rand(0, $notificationCount - 1)]
        ];
        $notificationUser[] = [
            'to_user' => $users[rand(0, $user_count - 1)],
            'notification_id' => $notificationIds[rand(0, $notificationCount - 1)]
        ];
        $notificationUser[] = [
            'to_user' => $users[rand(0, $user_count - 1)],
            'notification_id' => $notificationIds[rand(0, $notificationCount - 1)]
        ];
        $notificationUser[] = [
            'to_user' => $users[rand(0, $user_count - 1)],
            'notification_id' => $notificationIds[rand(0, $notificationCount - 1)]
        ];
        $notificationUser[] = [
            'to_user' => $users[rand(0, $user_count - 1)],
            'notification_id' => $notificationIds[rand(0, $notificationCount - 1)]
        ];
        DB::table('notification_user')->insert($notificationUser);
    }
}
