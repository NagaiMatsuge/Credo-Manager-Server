<?php

namespace App\Http\Controllers;

use App\Helpers\At;
use App\Models\Note;
use App\Models\Notification;
use App\Models\User;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    use ResponseTrait;

    //* Get all notifications with pagination
    public function index(Request $request)
    {
        $notes = Note::where('user_id', $request->user()->id)->paginate(30);
        if ($request->user()->hasRole(['Admin', 'Manager'])) {
            return $this->showToAdmin($request, $notes);
        } else {
            return $this->showToUser($notes);
        }
    }

    //* Get methods for Admins or Managers only
    public function showToAdmin(Request $request, $notes)
    {
        $notifs = Notification::where('user_id', $request->user()->id)->paginate(30);
        return $this->successResponse(['notifications' => $notifs, 'notes' => $notes]);
    }

    //* Get method for Users
    public function showToUser($notes)
    {
        return $this->successResponse(['notes' => $notes]);
    }


    //* Show Notification Log
    public function showNotificationLog(Request $request)
    {
        if ($request->user()->hasRole(['Admin', 'Manager'])) {
            return $this->showNotificationLogToAdmin($request);
        } else {
            return $this->showNotificationLogToUser($request);
        }
    }
    //* Show notifations history for user
    private function showNotificationLogToUser(Request $request)
    {
        $notifs = DB::table('notification_user as t1')->leftJoin('notifications as t2', 't1.notification_id', '=', 't2.id')->leftJoin('users as t3', 't3.id', '=', 't2.user_id')->leftJoin('roles as t5', 't5.id', '=', 't3.role_id')->select('t2.text', 't2.publish_date', 't3.name', 't3.photo', 't3.color', 't5.name as role')->where('t1.to_user', $request->user()->id)->where('t2.publish_date', '<', now())->paginate(30);
        return $this->successResponse(['notifications' => $notifs]);
    }

    //* Show notification history for admin
    private function showNotificationLogToAdmin(Request $request)
    {
        $notifs = Notification::where('user_id', $request->user()->id)->where('publish_date', '<', now())->paginate(30);
        return $this->successResponse(['notifications' => $notifs]);
    }

    //* Get notification by its id
    public function show(Request $request, $id)
    {
        return $this->successResponse(DB::table('notifications')->where('id', $id)->get());
    }

    //* Create notification
    public function store(Request $request)
    {
        if (!$request->user()->hasRole(['Admin', 'Manager'])) {
            return $this->notAllowed();
        }
        $this->makeValidation($request);
        $auth_user_id = $request->user()->id;
        DB::transaction(function () use ($auth_user_id, $request) {
            $create = Notification::create(array_merge(['user_id' => $auth_user_id], $request->input()));
            $userIds = User::get()->pluck('id');
            $notificationUser = [];
            foreach ($userIds as $userId) {
                $notificationUser[] = [
                    'to_user' => $userId,
                    'notification_id' => $create->id
                ];
            }
            DB::table('notification_user')->insert($notificationUser);
            $command = "php " . base_path() . "/artisan send:notification $create->id";
            $res = At::newAtCommand($command, $create->publish_date);
            if ($res['success']) {
                $job_number = $res['job'];
                DB::table('notifications')->where('id', $create->id)->update(['job_number' => $job_number]);
            } else {
                throw new Exception($res['message']);
            }
        });
        return $this->successResponse(true);
    }

    //* Update notification by its id
    public function update(Request $request, $id)
    {
        if (!$request->user()->hasRole(['Admin', 'Manager'])) {
            return $this->notAllowed();
        }
        $request->validate([
            'text' => 'required|string|min:3',
            'publish_date' => 'nullable|date|date_format:Y-m-d H:i:s'
        ]);
        $job = DB::table('notifications')->where('id', $id)->first();
        if ($request->has('publish_date') && $job->publish_date > date('Y-m-d H:i:s')) {
            DB::transaction(function () use ($id, $request, $job) {
                At::deleteAtCommand($job->job_number);
                $command = "php " . base_path() . "/artisan send:notification $id";
                $res = At::newAtCommand($command, $request->publish_date);
                if (!$res['success']) {
                    throw new Exception($res['message']);
                }
                DB::table('notifications')->where('id', $id)->update([
                    'text' => $request->text,
                    'publish_date' => $request->publish_date,
                    'job_number' => $res['job']
                ]);
            });
        } else {
            DB::table('notifications')->where('id', $id)->update([
                'text' => $request->text,
            ]);
        }


        return $this->successResponse(true);
    }

    //* Delete notification by its id
    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasRole(['Admin', 'Manager'])) {
            return $this->notAllowed();
        }
        DB::transaction(function () use ($id) {
            $notification = DB::table('notifications')->where('id', $id)->first();
            if ($notification->job_number)
                At::deleteAtCommand($notification->job_number);
            DB::table('notifications')->where('id', $id)->delete();
        });
        return $this->successResponse(true);
    }

    //* Validation function
    public function makeValidation(Request $request)
    {
        return $request->validate([
            'text' => 'required|string|min:3',
            'publish_date' => 'required|date|date_format:Y-m-d H:i:s'
        ]);
    }
}
