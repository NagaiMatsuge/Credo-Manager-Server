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
        return $this->showToAdmin($request);
        if ($request->user->hasRole('Admin')) {
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
        $userTasks = DB::table('task_user as t1')->select(DB::raw('(select t4.project_id from (select t5.* from steps as t5 where t5.id=(select t6.step_id from tasks as t6 where t6.id=t1.task_id)) as t4) as project_id'), DB::raw('(select t10.title from projects as t10 where t10.id=(select t7.project_id from (select t8.* from steps as t8 where t8.id=(select t9.step_id from tasks as t9 where t9.id=t1.task_id)) as t7)) as project_title'), 't1.task_id as task_id', 't1.active as active', DB::raw('(select t3.title from tasks as t3 where t3.id=t1.task_id) as task_title'), DB::raw('(select t10.finished from tasks as t10 where t10.id=t1.task_id) as task_finished'), 't1.time', 't1.unlim as unlimited', 't1.tick')->where('t1.user_id', $request->user->id)->get()->toArray();

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
    /*
    active           $
    task_id          $
    project_id       $
    project_title    $
    task_title       $
    task_time        $
    task_unlimited   $
    task_tick        $

    active
    hide
*/
    //* Show task by its id
    public function show(Task $id)
    {
        return $this->successResponse($id);
    }

    //* Create task with validation
    public function store(Request $request)
    {
        $this->makeValidation($request);

        $tasks = $request->tasks;

        foreach ($tasks as $key => $task) {
            $tasks[$key]['step_id'] = $request->step_id;
        }
        $user = User::where('id', $request->user_id)->first();
        $user->tasks()->createMany($tasks);

        return $this->successResponse([], 201, "Successfully created");
    }

    //* Update task by its id
    public function update(Request $request, Task $task)
    {
        $validation = $this->makeValidation($request, true);
        $task->update($validation);
        return $this->successResponse($task);
    }

    //* Delete task by its id
    public function destroy($id)
    {
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
            'title' => [
                Rule::requiredIf(!$for_update),
                'string',
                'min:3',
                'max:255',
            ],
            'active' => 'nullable|boolean',
            'time' => 'required|integer',
            'unlim' => [
                Rule::requiredIf(!$for_update),
                'boolean'
            ],
            'tick' => [
                Rule::requiredIf($for_update),
                'boolean'
            ]
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
        $projects = DB::table('projects')->select('id', 'title')->get();
        $users = DB::table('users')->select('id', 'name')->get();
        return $this->successResponse(['users' => $users, 'projects' => $projects]);
    }
}
