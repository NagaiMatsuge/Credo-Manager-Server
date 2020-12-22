<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Step;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Traits\DateTimeTrait;

class ProjectController extends Controller
{
    use ResponseTrait, DateTimeTrait;

    //* Fetch all projects with deadline and paid amount in percentage
    public function index(Request $request)
    {
        $projects = DB::table('projects')->select('projects.*', DB::raw('(SELECT COUNT(tasks.id) FROM tasks WHERE tasks.step_id IN (SELECT id FROM steps WHERE steps.project_id=projects.id)) as task_count'), DB::raw('(SELECT COUNT(tasks.id) FROM tasks WHERE tasks.step_id IN (SELECT id FROM steps WHERE steps.project_id=projects.id AND tasks.approved=1)) as approved_count'), DB::raw('(SELECT SUM(t1.percent)/ COUNT(t1.percent) AS paid_percent FROM (SELECT ((steps.price - steps.debt) * 100 / steps.price) AS percent FROM steps WHERE steps.project_id=projects.id) AS t1) AS paid_percent'))->paginate(10);
        $projects = $projects->toArray();
        foreach ($projects['data'] as $key => $project) {
            $date = explode("-", $project->deadline);
            $projects['data'][$key]->deadline = $date[2] . ' ' . config('params.month_format.' . $date[1]) . ' ' .  $date[0];
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
        $this->makeValidation($request);

        DB::transaction(function () use ($request) {
            $project = $request->project;
            if ($request->has('project.photo')) {
                $project['photo'] = $request->file('project.photo')->store('projects');
            }

            $project['deadline'] = $this->makeDateFillable($project['deadline'], '.');
            $project = Project::create($project);

            $steps = $request->steps;
            foreach ($steps as $key => $val) {
                $steps[$key]['project_id'] = $project->id;
                $steps[$key]['currency_id'] = $steps[$key]['currency_id']['id'];
                $steps[$key]['payment_type'] = $steps[$key]['payment_type']['id'];
                $steps[$key]['debt'] = $steps[$key]['price'];
            }
            DB::table('steps')->insert($steps);
        });
        return $this->successResponse([], 201, 'Successfully created');
    }

    //* Update project by its id   
    public function update(Request $request, $id)
    {
        $this->makeValidation($request);

        DB::transaction(function () use ($request, $id) {
            $oldProject = Project::where('id', $id)->first();
            $project = $request->project;

            if ($request->input('project.photo') !== null) {
                if ($oldProject->photo)
                    Storage::disk('public')->delete($oldProject->photo);
                $project['photo'] = $request->file('project.photo')->store('projects');
            }

            $project['deadline'] = $this->makeDateFillable($project['deadline'], '.');

            $oldProject->update($project);

            $steps = $request->steps;

            foreach ($steps as $key => $val) {
                $steps[$key]['currency_id'] = $steps[$key]['currency_id']['id'];
                $steps[$key]['payment_type'] = $steps[$key]['payment_type']['id'];
            }

            Step::updateOrCreate($steps);
        });

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
        $payment_types = config('params.payment_types');
        $payment_types_res = [];
        foreach ($payment_types as $key => $val) {
            $payment_types_res[] = [
                'id' => $key,
                'name' => $val
            ];
        }
        $currencies = config('params.currencies');
        $currencies_res = [];
        foreach ($currencies as $key => $val) {
            $currencies_res[] = [
                'id' => $key,
                'name' => $val
            ];
        }
        $data = [
            'payment_types' => $payment_types_res,
            'currencies' => $currencies_res
        ];
        return $this->successResponse($data);
    }

    public function getProjectSteps(Project $project)
    {
        $steps = $project->step()->get();
        $data = [
            'project' => $project,
            'steps' => $steps
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
            'project.deadline' => 'required|date|date_format:d.m.Y',
            'steps' => 'required|array',
            'steps.*.price' => 'required',
            'steps.*.currency_id.id' => [
                'required',
                Rule::in(array_keys(config('params.currencies')))
            ],
            'steps.*.payment_type.id' => [
                'required',
                Rule::in(array_keys(config('params.payment_types')))
            ],
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
