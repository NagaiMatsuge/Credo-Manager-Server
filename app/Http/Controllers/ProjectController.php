<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    use ResponseTrait;

    //* Fetch all projects with deadline
    public function index(Request $request)
    {
        $projects = DB::table('projects')->select('projects.*', DB::raw('(select max(deadline) from tasks where tasks.project_id = projects.id) as deadline'))->get();
        return $this->successResponse($projects);
    }

    //* Show project by its id
    public function show(Project $id)
    {
        return $this->successResponse($id);
    }

    //* Create project and tasks with validation    
    public function store(Request $request)
    {
        $request->validate([
            'project.title' => 'required|string|min:3|max:255',
            'project.description' => 'nullable|min:10',
            'tasks' => 'required|array',
            'tasks.*.price' => 'required|integer',
            'tasks.*.currency_id' => 'required|integer',
            'tasks.*.payment_type' => 'required|integer',
            'tasks.*.payment_date' => 'required|date',
            'tasks.*.title' => 'required|string|min:3|max:255'
        ]);
        $project = Project::create($request->project);
        $tasks = $request->tasks;
        foreach ($tasks as $key => $val) {
            $tasks[$key]['project_id'] = $project->id;
        }
        DB::table('tasks')->insert($tasks);
    }

    //* Update project by its id   
    public function update(Request $request, Project $id)
    {
        $id->update($request->all());
        return $this->successResponse($id);
    }

    //* Delete project by its id    
    public function destroy($id)
    {
        $delete = DB::table('projects')->where('id', $id)->delete();
        return $this->successResponse($delete);
    }
}
