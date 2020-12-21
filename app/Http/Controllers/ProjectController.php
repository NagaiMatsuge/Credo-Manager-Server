<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    use ResponseTrait;

    //* Fetch all projects with deadline and paid amount in percentage
    public function index(Request $request)
    {
        $projects = Project::select('projects.*',  DB::raw('(select count(id) from tasks where tasks.project_id = projects.id AND tasks.approved=1) as approved_tasks'), DB::raw('(select count(id) from tasks where tasks.project_id=projects.id) as num_tasks'))->paginate(5)->toArray();

        $tasks = DB::select("select price, debt, project_id from tasks");
        foreach ($projects['data'] as $key => $project) {
            $id = $project['id'];
            $tasksOfProject = [];
            foreach ($tasks as $task) {
                if ($task->project_id == $id) {
                    $tasksOfProject[] = $task;
                }
            }
            $debtPercent = 0;
            $taskCount = 0;
            foreach ($tasksOfProject as $t) {
                $taskCount++;
                $debtPercent += ($t->price - $t->debt) * 100 / $t->price;
            }
            if ($taskCount === 0) {
                $projects['data'][$key]['paid_in_percentage'] = null;
            } else {
                $projects['data'][$key]['paid_in_percentage'] = ($debtPercent * 100) / ($taskCount * 100);
            }
            $date = explode("-", $project['deadline']);
            $projects['data'][$key]['deadline'] = $date[2] . ' ' . config('params.month_format.' . $date[1]) . ' ' .  $date[0];
        }

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
            'project.photo' => 'nullable|image',
            'project.color' => [
                Rule::requiredIf($request->input('project.photo') == null),
                'string'
            ],
            'project.title' => 'required|string|min:3|max:255',
            'project.description' => 'nullable|min:10',
            'project.deadline' => 'required|date|date_format:Y-m-d',
            'tasks' => 'required|array',
            'tasks.*.price' => 'required|integer',
            'tasks.*.currency_id' => 'required|integer',
            'tasks.*.payment_type' => 'required|integer',
            'tasks.*.payment_date' => 'required|date',
            'tasks.*.title' => 'required|string|min:3|max:255'
        ]);

        $project = $request->project;
        if ($request->has('project.photo')) {
            $project['photo'] = $request->file('project.photo')->store('projects');
        }

        $project = Project::create($project);

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
