<?php

namespace App\Http\Controllers;

use App\Events\TaskChange;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Traits\Tasks\TaskTrait;

class TaskController extends Controller
{
    use ResponseTrait;
    use TaskTrait;

    //* Fetch all tasks
    public function index(Request $request)
    {
        if ($request->user()->hasRole(['Admin', 'Manager'])) {
            return $this->showToAdmin($request);
        } else {
            return $this->showToUser($request);
        }
    }

    //* Show List of tasks to Admin
    public function showToAdmin(Request $request)
    {
        $request->validate([
            'project_id' => 'nullable|integer',
            'user_id' => 'nullable|string'
        ]);
        $users = Task::getUserListForAdmin($request->user_id ?? null);

        $tasks = Task::getAllUserTasks($request->user()->id, $request->project_id ?? null);

        $res = [];
        foreach ($users['data'] as $user) {
            $res[$user->user_id] = [
                'user' => [
                    'id' => $user->user_id,
                    'name' => $user->user_name,
                    'role' => $user->user_role,
                    'photo' => $user->user_photo,
                    'worked' => $user->worked,
                    'color' => $user->user_color,
                    'active' => $user->active_task_id,
                    'has_paused_task' => is_null($user->back_up_active_task_id) ? false : true
                ],
                'active' => true,
                'hide' => true,
                'tasks' => [
                    'active' => [],
                    'inactive' => []
                ]
            ];
            foreach ($tasks as $task) {
                if ($task->user_id == $user->user_id) {
                    $task_info = [
                        'id' => $task->id,
                        'project' => [
                            'id' => $task->project_id,
                            'title' => $task->project_title
                        ],
                        'title' => $task->task_title,
                        'time' => $task->time,
                        'type' => $task->type,
                        'unread_count' => $task->unread_count,
                        'deadline' => $task->deadline,
                        'time_spent' => (int)$task->time_spent,
                        'last_time' => (int)$task->additional_time,
                        'finished' => $task->finished,
                    ];
                    $res[$user->user_id]['tasks'][($task->task_user_id === $user->active_task_id) || ($task->task_user_id === $user->back_up_active_task_id) ? 'active' : 'inactive'][] = $task_info;
                }
            }
        }
        unset($users['data']);
        return $this->successResponse($res, 200, '', ['name' => 'links', 'data' => $users]);
    }

    //* Create task with validation
    public function store(Request $request)
    {
        $current_user = $request->user();
        if (!$current_user->hasRole(['Admin', 'Manager']))
            return $this->notAllowed();

        $this->makeValidation($request);
        DB::transaction(function () use ($request, $current_user) {
            $task = $request->only(['step_id', 'title', 'time', 'deadline', 'type']);
            $newTask = Task::create($task);
            $user_ids = $request->user_ids;
            $userTasks = [];
            $date_n =  date('Y-m-d H:i:s');
            $text = "У вас есть новая задача";
            $notif = Notification::create([
                'user_id' => $current_user->id,
                'text' => $text,
                'publish_date' => $date_n
            ]);
            $notification_user = [];
            foreach ($user_ids as $user_id) {
                $userTasks[] = [
                    'user_id' => $user_id,
                    'task_id' => $newTask->id,
                ];
                $notification_user[] = [
                    'to_user' => $user_id,
                    'notification_id' => $notif->id
                ];

                broadcast(new TaskChange($user_id, $text, $current_user, $date_n, $notif->id));
            }
            DB::table('task_user')->insert($userTasks);
            DB::table('notification_user')->insert($notification_user);
        });
        return $this->successResponse([], 201, "Successfully created");
    }

    //* Update task by its id As Admin or Manager
    public function update(Request $request, $id)
    {
        $curr_user = $request->user();
        if (!$curr_user->hasRole(['Admin', 'Manager']))
            return $this->nowAllowed();

        $user_with_role = User::userRole($curr_user->id);
        if ($user_with_role->role == "Manager") {
            $canManagerUpdateTaks = DB::table('task_user as t1')
                ->where('t1.task_id', $id)
                ->whereRaw('t1.user_id in (select t2.id from users as t2 where t2.manager_id=?)', [$user_with_role->id])->exists();
            if (!$canManagerUpdateTaks)
                return $this->notAllowed();
        }
        $this->makeValidation($request, true);
        DB::transaction(function () use ($request, $id) {
            $old_user_ids = DB::table("task_user")->where('task_id', $id)->get()->pluck('user_id')->toArray();
            $new_user_ids = $request->user_ids;
            $need_to_be_deleted = array_diff($old_user_ids, $new_user_ids);
            $need_to_be_added = array_diff($new_user_ids, $old_user_ids);
            if (count($need_to_be_added) > 0) {
                $newTaskUsers = [];
                foreach ($need_to_be_added as $new_user_id) {
                    $newTaskUsers[] = [
                        'user_id' => $new_user_id,
                        'task_id' => $id,
                    ];
                }
                DB::table('task_user')->insert($newTaskUsers);
            }
            if (count($need_to_be_deleted) > 0) {
                DB::table('task_user')->whereIn('user_id', $need_to_be_deleted)->delete();
            }
            $taskUpdate = $request->only(['time', 'type', 'deadline', 'title']);
            DB::table('tasks')->where('id', $id)->update($taskUpdate);
        });
        return $this->successResponse([], 200, 'Successfully updated');
    }

    //* Update task User
    public function updateTaskUser(Request $request)
    {
        $curr_user = $request->user();
        $authority = $curr_user->hasRole(['Admin', 'Manager']);
        $request->validate([
            'user_id' => [
                'string',
                Rule::requiredIf($authority)
            ],
            'task_id' => 'required|integer',
            'approved' => [
                'boolean',
                Rule::requiredIf($authority)
            ],
            'finished' => [
                'boolean',
                Rule::requiredIf(!$authority)
            ]
        ]);
        DB::transaction(function () use ($request, $curr_user, $authority) {
            $task_info = DB::table('tasks')->where('id', $request->task_id)->first();
            if ($authority) {
                $taskUserUpdate = $request->only(['approved']);
                $curr_user_with_role = $curr_user->withRole();
                if ($curr_user_with_role->role == 'Manager') {
                    $canManagerUpdateTask = DB::table('task_user as t1')
                        ->where('t1.task_id', $request->task_id)
                        ->whereRaw('t1.user_id in (select t2.id from users as t2 where t2.manager_id=?)', [$curr_user->id])->exists();
                    if (!$canManagerUpdateTask)
                        return $this->nowAllowed();
                }
                DB::table('task_user')
                    ->where('user_id', $request->user_id)
                    ->where('task_id', $request->task_id)
                    ->update($taskUserUpdate);
                $text = 'Ваша задача ' . $task_info->title . ' одобрена';
                $date = date('Y-m-d H:i:s');
                $notif = Notification::create([
                    'user_id' => $curr_user->id,
                    'text' => $text,
                    'publish_date' => $date,
                    'type' => 2
                ]);
                DB::table('notification_user')->insert([
                    'to_user' => $request->user_id,
                    'notification_id' => $notif->id
                ]);
                broadcast(new TaskChange($request->user_id, $text, $curr_user_with_role, $date, $notif->id));
            } else {
                $taskUserUpdate = $request->only(['finished']);
                DB::table('task_user')
                    ->where('user_id', $curr_user->id)
                    ->where('task_id', $request->task_id)
                    ->update($taskUserUpdate);
                $users = DB::table('users as t1')
                    ->leftJoin('roles as t2', 't2.id', '=', 't1.role_id')
                    ->select('t1.id')
                    ->where('t2.name', 'Admin')
                    ->orWhereRaw('t1.id=(select t3.manager_id from users as t3 where t3.id=?)', [$curr_user->id])->get();
                $text = 'Пользователь завершил задачу ' . $task_info->title;
                $date = date('Y-m-d H:i:m');
                $notif = Notification::create([
                    'user_id' => $curr_user->id,
                    'text' => $text,
                    'publish_date' => $date,
                    'type' => 3
                ]);
                $notif_users = [];
                foreach ($users as $user) {
                    $notif_users[] = [
                        'to_user' => $user->id,
                        'notification_id' => $notif->id
                    ];
                    broadcast(new TaskChange($user->id, $text, $curr_user->withRole(), $date, $notif->id));
                }
                DB::table('notification_user')->insert($notif_users);
            }
        });
        return $this->successResponse(true);
    }

    //* Delete task by its id
    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasRole(['Admin', 'Manager']))
            return $this->notAllowed();
        $delete = DB::table('tasks')->where('id', $id)->delete();
        return $this->successResponse($delete);
    }

    //* Validate the request for tasks
    public function makeValidation(Request $request, $for_update = false)
    {
        return $request->validate([
            'step_id' => [
                Rule::requiredIf($for_update == false),
                'integer'
            ],
            'user_ids' => [
                Rule::requiredIf($request->user()->hasRole(['Admin', 'Manager'])),
                'array'
            ],
            'user_ids.*' => 'string',
            'title' => [
                Rule::requiredIf($for_update == false),
                'string',
                'min:3',
                'max:255',
            ],
            'active' => 'nullable|boolean',
            'time' => [
                Rule::requiredIf($for_update == false),
                'integer',
                'max:1000000000'
            ],
            'type' => [
                Rule::requiredIf($for_update == false),
                'integer',
                Rule::in(array_keys(config('params.task_types')))
            ],
            'finished' => 'nullable|boolean',
            'approved' => 'nullable|boolean',
            'deadline' => [
                Rule::requiredIf($request->type == array_search('deadline', config('params.task_types'))),
            ]
        ]);
    }

    //* Get data for filtering
    public function getCredentials(Request $request)
    {
        $projects = DB::table('projects')->select('id', 'title');
        if ($request->title) {
            $projects = $projects->where('title', 'like', '%' . $request->title . '%')->where('archived', false);
        }

        $users = DB::table('users')->select('id', 'name');
        if ($request->name) {
            $users = $users->where('name', 'like', '%' . $request->name . '%');
        }
        return $this->successResponse(['users' => $users->get(), 'projects' => $projects->get()]);
    }

    //* Get Sorted users for creating tasks
    public function getUserListForCreatingTask(Request $request)
    {
        $res = $this->userList();
        return $this->successResponse($res);
    }

    private function userList()
    {
        $users = User::userRole();
        $res = [
            'developers' => [],
            'designers' => []
        ];
        foreach ($users as $user) {
            if (strstr(strtolower($user->role), 'designer')) {
                $res['designers'][] = $user;
            } else if ((strstr(strtolower($user->role), 'admin') == false) && (strstr(strtolower($user->role), 'manager') == false)) {
                $res['developers'][] = $user;
            }
        }
        return $res;
    }

    public function clock(Request $request)
    {
        $current_user = $request->user();
        if (!$current_user->hasRole(['Admin', 'Manager']))
            return $this->notAllowed();

        $request->validate([
            'task_id' => 'nullable|integer',
            'user_id' => 'required|string'
        ]);

        $user_id = $request->user_id;
        $user = DB::table('users')->where('id', $user_id)->first();
        $date_n =  date('Y-m-d H:i:s');
        if (!$request->has('task_id')) {
            DB::table('users')->where('id', $user_id)->update([
                'active_task_id' => null,
                'back_up_active_task_id' => null

            ]);
            DB::table('task_watchers')->where('task_user_id', $user->active_task_id)->where('stopped_at', null)->update([
                'stopped_at' => $date_n
            ]);
            $text = "У вас больше нет активных задач";

            $notif = Notification::create([
                'user_id' => $current_user->id,
                'text' => $text,
                'publish_date' => $date_n,
                'type' => 2
            ]);
            DB::table('notification_user')->insert([
                'to_user' => $user_id,
                'notification_id' => $notif->id
            ]);
            info('Task Change');
            broadcast(new TaskChange($user->id, $text, $current_user->withRole(), $date_n, $notif->id));
            return $this->successResponse(['tick' => false]);
        }
        DB::transaction(function () use ($user_id, $request, $user, $current_user, $date_n) {

            $task_user = DB::table('task_user')->where('task_id', $request->task_id)->where('user_id', $request->user_id)->first();

            $lastWatcher = DB::table('task_watchers as t1')->where('t1.task_user_id', $user->active_task_id)->whereRaw('t1.created_at=(select max(t2.created_at) from task_watchers as t2 where t2.task_user_id=t1.task_user_id)')->first();

            DB::table('users')->where('id', $user_id)->update([
                'active_task_id' => $task_user->id,
                'back_up_active_task_id' => null
            ]);
            if ($lastWatcher) {
                DB::table('task_watchers')->where('id', $lastWatcher->id)->update(['stopped_at' => date('Y-m-d H:i:s')]);
            }
            $this->createTaskWatcher($request, $task_user->id);
            $text = "У вас есть новая активная задача";
            $notif = Notification::create([
                'user_id' => $current_user->id,
                'text' => $text,
                'publish_date' => $date_n,
                'type' => 2
            ]);
            DB::table('notification_user')->insert([
                'to_user' => $user_id,
                'notification_id' => $notif->id
            ]);
            broadcast(new TaskChange($user->id, $text, $current_user->withRole(), $date_n, $notif->id));
        });
        return $this->successResponse(['tick' => true]);
    }

    private function createTaskWatcher(Request $request, $task_user_id)
    {
        $lastWatcher = [
            'task_user_id' => $task_user_id,
            'created_at' => date('Y-m-d H:i:s')
        ];
        DB::table('task_watchers')->insert($lastWatcher);

        $res = [
            'tick' => true
        ];
        return $res;
    }

    public function show($id)
    {
        $res = DB::table("task_user as t1")->leftJoin("tasks as t2", "t1.task_id", "=", "t2.id")->leftJoin("steps as t3", "t2.step_id", "=", "t3.id")->leftJoin("projects as t4", "t4.id", "=", "t3.project_id")->where("t1.task_id", $id)->select('t1.*', 't2.*', "t3.title as step_title", "t3.project_id", "t4.title as project_title")->get()->toArray();


        $user_ids = array_column($res, 'user_id');
        $res = $res[0];
        $result = [
            'projects' => [
                'id' => $res->project_id,
                'title' => $res->project_title
            ],
            'step_ids' => [
                'id' => $res->step_id,
                'title' => $res->step_title
            ],
            'title' => $res->title,
            'user_ids' => $user_ids,
            'step_id' => $res->step_id,
            'time' => $res->time,
            'type' => $res->type,
            'deadline' => $res->deadline,
        ];

        return $this->successResponse($result);
    }
}
