<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
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
        // ------------------------Users ------------------------------------
        $users = DB::table('users as t19')->select(DB::raw('(select t11.name from roles as t11 where t11.id=(select t12.role_id from model_has_roles as t12 where t12.model_uuid=t19.id))as user_role'), 't19.id as user_id', 't19.name as user_name', 't19.photo as user_photo', DB::raw('(1) as worked'), 't19.color as user_color')->paginate(4)->toArray();
        // ------------------------Users ------------------------------------

        // ------------------------Tasks ------------------------------------

        $tasks = DB::select('select t25.* from (select (select t2.id from tasks as t2 where t2.id=t20.task_id) as task_id, t20.active as status, (select t3.title from tasks as t3 where t3.id=t20.task_id) as title,
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
        from task_user as t20) as t25 where t25.task_approved=0', [$request->user()->id]);
        // ------------------------Users ------------------------------------
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
                    if ($task->status) {
                        $res[$user->user_id]['tasks']['active'][] = [
                            'id' => $task->task_id,
                            'project' => [
                                'id' => $task->project_id,
                                'title' => $task->project_title
                            ],
                            'title' => $task->title,
                            'time' => $task->time,
                            'type' => $task->type,
                            'tick' => $task->tick,
                            'unread_count' => $task->unread_count,
                            'deadline' => $task->deadline,
                            'time_spent' => (int)$task->time_spent
                        ];
                    } else {
                        $res[$user->user_id]['tasks']['inactive'][] = [
                            'id' => $task->task_id,
                            'project' => [
                                'id' => $task->project_id,
                                'title' => $task->project_title
                            ],
                            'title' => $task->title,
                            'time' => $task->time,
                            'type' => $task->type,
                            'tick' => $task->tick,
                            'unread_count' => $task->unread_count,
                            'deadline' => $task->deadline,
                            'time_spent' => (int)$task->time_spent
                        ];
                    }
                }
            }
        }
        unset($users['data']);
        return $this->successResponse($res, 200, '', ['name' => 'links', 'data' => $users]);
    }

    //* Show list of tasks to User
    public function showToUser(Request $request)
    {
        $userTasks = DB::select('select t30.* from (select
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
        from task_user as t1 where t1.user_id=?) as t30 where t30.task_approved=0', [$request->user()->id]);

        $res = [
            'tasks' => [
                'active' => [],
                'inactive' => []
            ]
        ];
        foreach ($userTasks as $task) {
            if ($task->active) {
                $res['tasks']['active'][] = [
                    'id' => $task->task_id,
                    'project' => [
                        'id' => $task->project_id,
                        'title' => $task->project_title
                    ],
                    'title' => $task->task_title,
                    'time' => $task->time,
                    'type' => $task->type,
                    'tick' => $task->tick,
                    'unread_count' => $task->unread_count,
                    'deadline' => $task->deadline,
                    'time_spent' => (int)$task->time_spent
                ];
            } else {
                $res['tasks']['inactive'][] = [
                    'id' => $task->task_id,
                    'project' => [
                        'id' => $task->project_id,
                        'title' => $task->project_title
                    ],
                    'title' => $task->task_title,
                    'time' => $task->time,
                    'type' => $task->type,
                    'tick' => $task->tick,
                    'unread_count' => $task->unread_count,
                    'deadline' => $task->deadline,
                    'time_spent' => $task->time_spent
                ];
            }
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
                    'time' => $request->time ?? 0,
                    'type' => $request->type,
                    'tick' => $request->tick,
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
                $taskUserUpdate = array_merge($taskUserUpdate, $request->only(['tick']));
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
            'tick' => [
                'nullable',
                'boolean'
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
        $users = User::allUsersWithRoles();
        $res = [
            'developers' => [],
            'designers' => []
        ];
        foreach ($users as $user) {
            if (strpos($user->role, 'designer') !== false) {
                $res['designers'][] = $user;
            } else if (strpos($user->role, 'admin') == false && strpos($user->role, 'manager') == false) {
                $res['developers'][] = $user;
            }
        }
        return $this->successResponse($res);
    }

    public function clock(Request $request)
    {
        $request->validate([
            'task_id' => 'required|integer'
        ]);
        $user_id = $request->user()->id;

        $lastWatcher = DB::table('task_watchers as t1')->where('t1.task_id', $request->task_id)->whereRaw('created_at=(select max(t2.created_at) from task_watchers as t2 where t2.task_id=t1.task_id and t2.user_id=t1.user_id)')->where('t1.user_id', $user_id)->first();
        $task = DB::table('task_user')->select('type')->where('user_id', $user_id)->where('task_id', $request->task_id)->first();

        if (!$task)
            return $this->errorResponse('task/not-found');

        if (!in_array($task->type, ["1", "3"]))
            return $this->errorResponse('task-type/mismatch');

        if (!$lastWatcher)
            return $this->successResponse($this->createTaskWatcher($request, $user_id));

        if ($lastWatcher->stopped_at == null) {
            DB::transaction(function () use ($lastWatcher, $user_id, $request) {
                DB::table('task_watchers')->where('id', $lastWatcher->id)->update(['stopped_at' => now()]);
                DB::table('task_user')->where('user_id', $user_id)->where('task_id', $request->task_id)->update(['tick' => false]);
            });

            $res = [
                'tick' => false
            ];
        } else {
            $res = $this->createTaskWatcher($request, $user_id);
        }
        return $this->successResponse($res);
    }

    private function createTaskWatcher(Request $request, $user_id)
    {
        $lastWatcher = [
            'task_id' => $request->task_id,
            'user_id' => $user_id,
            'created_at' => now()
        ];
        DB::transaction(function () use ($lastWatcher, $request, $user_id) {
            DB::table('task_watchers')->insert($lastWatcher);
            DB::table('task_user')->where('user_id', $user_id)->where('task_id', $request->task_id)->update([
                'tick' => true
            ]);
        });

        $res = [
            'tick' => true
        ];
        return $res;
    }
}
