<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    use ResponseTrait;

    //* Fetch all tasks
    public function index()
    {
        // ------------------------Users ------------------------------------
        $users = DB::table('users as t19')->select(DB::raw('(select t11.name from roles as t11 where t11.id=(select t12.role_id from model_has_roles as t12 where t12.model_uuid=t19.id))as user_role'), DB::raw('(select t13.id from (select t14.id from users as t14 where t14.id=t19.id) as t13) as user_id'), DB::raw('(select t15.name from (select t16.name from users as t16 where t16.id=t19.id) as t15) as user_name'), DB::raw('(select t17.photo from (select t18.photo from users as t18 where t18.id=t19.id) as t17) as user_photo'), DB::raw('(1) as worked'))->paginate(4)->toArray();
        // ------------------------Users ------------------------------------

        // ------------------------Tasks ------------------------------------

        $tasks = DB::table('task_user as t20')->select(DB::raw('(select t2.id from tasks as t2 where t2.id=t20.task_id) as task_id, t20.active as status, (select t3.title from tasks as t3 where t3.id=t20.task_id) as title'), DB::raw('(select t4.project_id from (select t5.* from steps as t5 where t5.id=(select t6.step_id from tasks as t6 where t6.id=t20.task_id)) as t4) as project_id'), DB::raw('(select t10.title from projects as t10 where t10.id=(select t7.project_id from (select t8.* from steps as t8 where t8.id=(select t9.step_id from tasks as t9 where t9.id=t20.task_id)) as t7)) as project_title'), 't20.user_id')->get()->toArray();
        // ------------------------Users ------------------------------------

        $res = [];
        foreach ($users['data'] as $user) {
            $res[$user->user_id] = [
                'user' => [
                    'id' => $user->user_id,
                    'name' => $user->user_name,
                    'role' => $user->user_role,
                    'photo' => $user->user_photo,
                    'worked' => $user->worked
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
                            'title' => $task->title
                        ];
                    } else {
                        $res[$user->user_id]['tasks']['inactive'][] = [
                            'id' => $task->task_id,
                            'project' => [
                                'id' => $task->project_id,
                                'title' => $task->project_title
                            ],
                            'title' => $task->title
                        ];
                    }
                }
            }
        }
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
    public function update(Request $request, Task $id)
    {
        $validation = $this->makeValidation($request);
        $id->update($validation);
        return $this->successResponse($id);
    }

    //* Delete task by its id
    public function destroy($id)
    {
        $delete = DB::table('tasks')->where('id', $id)->delete();
        return $this->successResponse($delete);
    }

    //* Validate the request for tasks
    public function makeValidation(Request $request)
    {
        return $request->validate([
            'step_id' => 'required|integer',
            'tasks' => 'required|array',
            'tasks.*.title' => 'required|string|min:3|max:255',
            'tasks.*.deadline' => 'required|date|date_format:Y-m-d'
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
