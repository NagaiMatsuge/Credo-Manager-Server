<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateUserTimer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:timer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle user Timer';

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
        $users = DB::table('users as t1')
            ->leftJoin('roles as t2', 't2.id', '=', 't1.role_id')
            ->select('t1.*', 't2.name as role')
            ->whereNotIn('t2.name', ['Admin', "Manager"])
            ->get();

        foreach ($users as $user) {
            $curr_time = new DateTime();
            $date_only = $curr_time->format('Y-m-d');
            $working_days = json_decode($user->working_days);
            $pause_start_time = new DateTime($date_only . ' ' . $user->pause_start_time);
            $pause_end_time = new DateTime($date_only . ' ' . $user->pause_end_time);
            $work_start_time = new DateTime($date_only . ' ' . $user->work_start_time);
            $work_end_time = new DateTime($date_only . ' ' . $user->work_end_time);
            if (in_array($curr_time->format('N'), $working_days)) {
                if ($curr_time < $work_start_time) {
                    //Pause the user
                    echo 'Pausign User';
                    if (!is_null($user->active_task_id)) {
                        $this->pauseUser($user);
                    }
                } else if ($curr_time >= $work_start_time && $curr_time < $pause_start_time) {
                    //Unpause the user
                    echo 'Unpausing User';
                    if (!is_null($user->back_up_active_task_id)) {
                        $this->unPauseUser($user);
                    }
                } else if ($curr_time >= $pause_start_time && $curr_time <= $pause_end_time) {
                    //Pause the user
                    echo 'Pausign User';
                    if (!is_null($user->active_task_id)) {
                        $this->pauseUser($user);
                    }
                } else if ($curr_time > $pause_end_time && $curr_time < $work_end_time) {
                    //Unpause the user
                    echo 'Unpausing User';
                    if (!is_null($user->back_up_active_task_id)) {
                        $this->unPauseUser($user);
                    }
                } else if ($curr_time >= $work_end_time) {
                    //Pause the user
                    echo 'Pausign User';
                    if (!is_null($user->active_task_id)) {
                        $this->pauseUser($user);
                    }
                }
            }
        }
    }

    private function pauseUser($user)
    {
        DB::table('task_watchers')
            ->where('task_user_id', $user->active_task_id)
            ->whereNull('stopped_at')
            ->update([
                'stopped_at' => date('Y-m-d H:i:s')
            ]);
        DB::table('users')->where('id', $user->id)->update([
            'active_task_id' => null,
            'back_up_active_task_id' => $user->active_task_id
        ]);
    }

    private function unPauseUser($user)
    {
        DB::table('task_watchers')
            ->insert([
                'task_user_id' => $user->back_up_active_task_id,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'active_task_id' => $user->back_up_active_task_id,
                'back_up_active_task_id' => null
            ]);
    }
}
