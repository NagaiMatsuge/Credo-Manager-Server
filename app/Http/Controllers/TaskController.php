<?php

namespace App\Http\Controllers;

use App\Models\Task;
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
        $create_task = Task::create($request->validate([
            'title' => 'required|string|min:3|max:255',
            'project_id' => 'required|integer',
            'price' => 'required|integer',
            'currency_id' => 'required|integer',
            'payment_type' => 'required|integer',
            'payment_date' => 'required|date|date_format:Y-m-d',
            'time_left' => 'required|date_format:H:i'
        ]));

        return $this->successResponse($create_task);
    }
//* Update task by its id
    public function update(Request $request, Task $id)
    {
        $id->update($request->all());
        return $this->successResponse($id);
    }
//* Delete task by its id
    public function destroy($id)
    {
        $delete = DB::table('tasks')->where('id', $id)->delete();
        return $this->successResponse($delete);
    }
}
