<?php

namespace App\Console\Commands;

use App\Events\NotificationSent;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendNotification extends Command
{
    /**
     * The name and signature of the command.
     *
     * @var string
     */
    protected $signature = 'SendNofication {--i|notification_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending notifications at planned date';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {

            $notification = DB::table('notification_user as t1')->leftJoin('notifications as t2', 't2.id', '=', 't1.notification_id')->leftJoin('users as t3', 't3.id', '=', 't2.user_id')->where('notification_id', '1')->select('t3.photo', 't3.id as from_user', 't3.color', 't3.email', 't3.name', 't1.to_user', 't2.text', 't2.publish_date')->get()->toArray();
            $user_ids = array_column($notification, 'to_user');
            $notification = $notification[0];
            $from_user = [
                'id' => $notification->from_user,
                'photo' => $notification->photo,
                'color' => $notification->color,
                'email' => $notification->email,
                'name' => $notification->name,
            ];
            foreach ($user_ids as $user_id) {
                broadcast(new NotificationSent($user_id, $notification->text, $from_user, $notification->publish_date));
            }
        } catch (Exception $e) {
            // To be continued...
        }


        return 0;
    }
}
