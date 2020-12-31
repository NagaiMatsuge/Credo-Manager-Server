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

        $tasks = DB::table('task_user as t20')->select(DB::raw('(select t2.id from tasks as t2 where t2.id=t20.task_id) as task_id, t20.active as status, (select t3.title from tasks as t3 where t3.id=t20.task_id) as title'), DB::raw('(select t4.project_id from (select t5.* from steps as t5 where t5.id=(select t6.step_id from tasks as t6 where t6.id=t20.task_id)) as t4) as project_id'), DB::raw('(select t10.title from projects as t10 where t10.id=(select t7.project_id from (select t8.* from steps as t8 where t8.id=(select t9.step_id from tasks as t9 where t9.id=t20.task_id)) as t7)) as project_title'), 't20.user_id', 't20.unlim as unlimited', 't20.tick', 't20.time')->get()->toArray();
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
                'active' => false,
                'hide' => false
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
                            'unlimited' => $task->unlimited,
                            'tick' => $task->tick
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
                            'unlimited' => $task->unlimited,
                            'tick' => $task->tick
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
        $userTasks = DB::table('task_user as t1')->select(DB::raw('(select t4.project_id from (select t5.* from steps as t5 where t5.id=(select t6.step_id from tasks as t6 where t6.id=t1.task_id)) as t4) as project_id'), DB::raw('(select t10.title from projects as t10 where t10.id=(select t7.project_id from (select t8.* from steps as t8 where t8.id=(select t9.step_id from tasks as t9 where t9.id=t1.task_id)) as t7)) as project_title'), 't1.task_id as task_id', 't1.active as active', DB::raw('(select t3.title from tasks as t3 where t3.id=t1.task_id) as task_title'), DB::raw('(select t10.finished from tasks as t10 where t10.id=t1.task_id) as task_finished'), 't1.time', 't1.unlim as unlimited', 't1.tick')->where('t1.user_id', $request->user()->id)->get()->toArray();

        $res = [];
        foreach ($userTasks as $task) {
            if (!$task->task_finished) {
                if ($task->active) {
                    $res['tasks']['active'][] = [
                        'id' => $task->task_id,
                        'project' => [
                            'id' => $task->project_id,
                            'title' => $task->project_title
                        ],
                        'title' => $task->task_title,
                        'time' => $task->time,
                        'unlimited' => $task->unlimited,
                        'tick' => $task->tick
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
                        'unlimited' => $task->unlimited,
                        'tick' => $task->tick
                    ];
                }
            }
        }
        $res['active'] = false;
        $res['hide'] = false;

        return $this->successResponse($res);
    }

    //* Show task by its id
    public function show(Task $id)
    {
        return $this->successResponse($id);
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
                    'unlim' => $request->unlimited,
                    'tick' => $request->tick,
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
            //Admin Can update title, step, approved, unlim but cannot change active state of the task
            //User can change active state but cannot change unlimited
            if ($authority) {
                $taskUpdate = $request->only(['title', 'step_id', 'approved']);
                DB::table('tasks')->where('id', $id)->update($taskUpdate);
                if ($request->has('unlimited'))
                    $taskUserUpdate['unlim'] = $request->unlimited;
            } else {
                $taskUserUpdate = array_merge($taskUserUpdate, $request->only(['active', 'finished', 'tick']));
            }
            //When the update is invoked by admin, execution touches all user_ids
            //When update is invoked by user, execution touches only that user
            DB::table('task_user')->when($authority, function ($query) use ($request) {
                return $query->whereIn('user_id', $request->user_ids);
            })->when(!$authority, function ($query) use ($request) {
                return $query->where('user_id', $request->user()->id);
            })->where('task_id', $id)->update($taskUserUpdate);
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
                Rule::requiredIf($request->unlimited == false),
                'integer'
            ],
            'unlimited' => [
                Rule::requiredIf(!$for_update),
                'boolean'
            ],
            'tick' => [
                'nullable',
                'boolean'
            ],
            'finished' => 'nullable|boolean',
            'approved' => 'nullable|boolean'
        ]);
    }

    //* Show messages that belongs to task_id
    public function showMessages(Task $id)
    {
        $msg = $id->messages()->get();
        return $this->successResponse($msg);
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

    //* Describe your method
    public function updateAsUser(Request $request)
    {
    }
}
