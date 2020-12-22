<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Step;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    use ResponseTrait;

    //* Fetch all projects with deadline and paid amount in percentage
    public function index(Request $request)
    {
        $projects = DB::table('projects')->select('projects.*', DB::raw('(SELECT COUNT(tasks.id) FROM tasks WHERE tasks.step_id IN (SELECT id FROM steps WHERE steps.project_id=projects.id)) as task_count'), DB::raw('(SELECT COUNT(tasks.id) FROM tasks WHERE tasks.step_id IN (SELECT id FROM steps WHERE steps.project_id=projects.id AND tasks.approved=1)) as approved_count'), DB::raw('(SELECT SUM(t1.percent)/ COUNT(t1.percent) AS paid_percent FROM (SELECT ((steps.price - steps.debt) * 100 / steps.price) AS percent FROM steps WHERE steps.project_id=projects.id) AS t1) AS paid_percent'))->paginate(10);
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
        $this->makeValidation($request);

        DB::transaction(function () use ($request) {
            $project = $request->project;
            if ($request->has('project.photo')) {
                $project['photo'] = $request->file('project.photo')->store('projects');
            }

            $project = Project::create($project);

            $steps = $request->steps;
            foreach ($steps as $key => $val) {
                $steps[$key]['project_id'] = $project->id;
            }
            DB::table('steps')->insert($steps);
            return $this->successResponse([], 201, 'Successfully created');
        });
    }

    //* Update project by its id   
    public function update(Request $request, $id)
    {
        $this->makeValidation($request);

        $oldProject = Project::where('id', $id)->first();
        $project = $request->project;

        if ($request->input('project.photo') !== null) {
            if ($oldProject->photo)
                Storage::disk('public')->delete($oldProject->photo);
            $project['photo'] = $request->file('project.photo')->store('projects');
        }

        $oldProject->update($project);

        Step::updateOrCreate($request->steps);

        return $this->successResponse([], 201, 'Successfully updated');
    }

    //* Delete project by its id    
    public function destroy($id)
    {
        $delete = DB::table('projects')->where('id', $id)->delete();
        return $this->successResponse($delete);
    }

    //* Get all payment credentials
    public function getCredentials(Request $request)
    {
        $data = [
            'payment_types' => config('params.payment_types'),
            'currencies' => config('params.currencies')
        ];
        return $this->successResponse($data);
    }

    //* Validates the requrest for projects
    public function makeValidation(Request $request)
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
            'steps' => 'required|array',
            'steps.*.price' => 'required',
            'steps.*.currency_id' => [
                'required',
                Rule::in(array_keys(config('params.currencies')))
            ],
            'steps.*.payment_type' => [
                'required',
                Rule::in(array_keys(config('params.payment_types')))
            ],
            'steps.*.payment_date' => 'required|date',
            'steps.*.title' => 'required|string|min:3|max:255'
        ]);
    }

    /*
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
    */
}
