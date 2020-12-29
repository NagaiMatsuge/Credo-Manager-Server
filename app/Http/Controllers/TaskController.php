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
        $tasks = Task::all();
        return $this->successResponse($tasks);
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
