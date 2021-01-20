<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    use ResponseTrait;

    //* Fetch all tasks
    public function index(Request $request)
    {
        if ($request->user()->hasRole('Admin')) {
            return $this->showToAdmin($request);
        } else {
            return $this->showToUser($request);
        }
    }

    //* Show List of tasks to Admin
    public function showToAdmin(Request $request)
    {
        $users = Task::getUserListForAdmin();

        $tasks = Task::getAllUserTasks($request->user()->id);

        $res = [];
        foreach ($users['data'] as $user) {
            $res[$user->user_id] = [
                'user' => [
                    'id' => $user->user_id,
                    'name' => $user->user_name,
                    'role' => $user->user_role,
                    'photo' => $user->user_photo,
                    'worked' => $user->worked,
                    'color' => $user->user_color
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
                        'id' => $task->task_id,
                        'project' => [
                            'id' => $task->project_id,
                            'title' => $task->project_title
                        ],
                        'title' => $task->title,
                        'time' => $task->time,
                        'type' => $task->type,
                        'unread_count' => $task->unread_count,
                        'deadline' => $task->deadline,
                        'time_spent' => (int)$task->time_spent
                    ];
                    $res[$user->user_id]['tasks'][$task->status ? 'active' : 'inactive'][] = $task_info;
                }
            }
        }
        unset($users['data']);
        return $this->successResponse($res, 200, '', ['name' => 'links', 'data' => $users]);
    }

    //* Show list of tasks to User
    public function showToUser(Request $request)
    {
        $userTasks = Task::getUserTasks($request->user()->id);

        $res = [
            'tasks' => [
                'active' => [],
                'inactive' => []
            ]
        ];
        foreach ($userTasks as $task) {
            $task_data = [
                'id' => $task->task_id,
                'project' => [
                    'id' => $task->project_id,
                    'title' => $task->project_title
                ],
                'title' => $task->task_title,
                'time' => $task->time,
                'type' => $task->type,
                'unread_count' => $task->unread_count,
                'deadline' => $task->deadline,
                'time_spent' => (int)$task->time_spent
            ];
            $res['tasks'][$task->active ? 'active' : 'inactive'][] = $task_data;
        }
        $res['active'] = true;
        $res['hide'] = true;

        return $this->successResponse($res);
    }

    //* Create task with validation
    public function store(Request $request)
    {
        if (!$request->user()->hasRole(['Admin', 'Manager']))
            return $this->notAllowed();

        $this->makeValidation($request);
        DB::transaction(function () use ($request) {
            $task = $request->only(['step_id', 'title']);
            $newTask = Task::create($task);
            $user_ids = $request->user_ids;
            $userTasks = [];
            foreach ($user_ids as $user_id) {
                $userTasks[] = [
                    'user_id' => $user_id,
                    'task_id' => $newTask->id,
                    'time' => $request->time,
                    'type' => $request->type,
                    'deadline' => $request->deadline ?? null,
                    'created_at' => now()
                ];
            }
            DB::table('task_user')->insert($userTasks);
        });
        return $this->successResponse([], 201, "Successfully created");
    }

    //* Update task by its id As Admin or Manager
    public function update(Request $request, $id)
    {
        $this->makeValidation($request, true);

        DB::transaction(function () use ($request, $id) {
            $authority = $request->user()->hasRole(['Admin', 'Manager']);
            $taskUserUpdate = $request->only(['time']);
            //Admin Can update title, step, approved, type but cannot change active state of the task
            //User can change active state but cannot change type
            $taskUpdate = [];
            if ($authority) {
                $taskUpdate = $request->only(['title', 'step_id', 'approved']);
                $taskUserUpdate = array_merge($taskUserUpdate, $request->only(['active', 'type', 'deadline']));
            } else {
                $taskUpdate = $request->only(['finished']);
            }
            if (!empty($taskUpdate))
                DB::table('tasks')->where('id', $id)->update($taskUpdate);

            //When the update is invoked by admin, execution touches all user_ids
            //When update is invoked by user, execution touches only that user
            if (!empty($taskUserUpdate)) {
                DB::table('task_user')->when($authority, function ($query) use ($request) {
                    return $query->whereIn('user_id', $request->user_ids);
                })->when(!$authority, function ($query) use ($request) {
                    return $query->where('user_id', $request->user()->id);
                })->where('task_id', $id)->update($taskUserUpdate);
            }
        });
        return $this->successResponse([], 200, 'Successfully updated');
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
                Rule::requiredIf(!$for_update),
                'integer'
            ],
            'user_ids' => [
                Rule::requiredIf($request->user()->hasRole(['Admin', 'Manager'])),
                'array'
            ],
            'user_ids.*' => 'string',
            'title' => [
                Rule::requiredIf(!$for_update),
                'string',
                'min:3',
                'max:255',
            ],
            'active' => 'nullable|boolean',
            'time' => [
                Rule::requiredIf(!$for_update),
                'integer',
                'max:1000000000'
            ],
            'type' => [
                Rule::requiredIf(!$for_update),
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
            $projects = $projects->where('title', 'like', '%' . $request->title . '%');
        }

        $users = DB::table('users')->select('id', 'name');
        if ($request->name) {
            $users = $users->where('name', 'like', '%' . $request->name . '%');
        }
        return $this->successResponse(['users' => $users->paginate(5), 'projects' => $projects->paginate(5)]);
    }

    //* Get Sorted users for creating tasks
    public function getUserListForCreatingTask(Request $request)
    {
        $res = $this->userList();
        return $this->successResponse($res);
    }

    private function userList()
    {
        $users = User::allUsersWithRoles()->get();
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
        if (!$request->user()->hasRole(['Admin', 'Manager']))
            return $this->notAllowed();

        $request->validate([
            'task_id' => 'nullable|integer',
            'user_id' => 'required|string'
        ]);

        $user_id = $request->user_id;
        if (!$request->has('task_id')) {
            DB::table('task_user')->where('user_id', $user_id)->update(['active' => false]);
            DB::table('task_watchers')->where('user_id', $user_id)->where('stopped_at', null)->update([
                'stopped_at' => now()
            ]);
            return $this->successResponse(['tick' => false]);
        }

        DB::transaction(function () use ($user_id, $request) {

            $lastWatcher = DB::table('task_watchers as t1')->where('t1.task_id', $request->task_id)->whereRaw('created_at=(select max(t2.created_at) from task_watchers as t2 where t2.task_id=t1.task_id and t2.user_id=t1.user_id)')->where('t1.user_id', $user_id)->first();
            $active_task = DB::table('task_user')->where('user_id', $user_id)->where('active', true)->first();

            if ($active_task) {
                DB::table('task_user')->where('user_id', $user_id)->where('active', true)->update(['active' => false]);
                DB::table('task_watchers')->where('id', $lastWatcher->id)->update(['stopped_at' => now()]);
            }
            $this->createTaskWatcher($request, $user_id);

            DB::table('task_user')->where('user_id', $user_id)->where('task_id', $request->task_id)->update([
                'active' => true
            ]);
        });
        return $this->successResponse(['tick' => true]);
    }

    private function createTaskWatcher(Request $request, $user_id)
    {
        $lastWatcher = [
            'task_id' => $request->task_id,
            'user_id' => $user_id,
            'created_at' => now()
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
