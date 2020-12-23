<?php

namespace App\Http\Controllers;

use App\Http\Resources\StepResource;
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

    private $project_shape;
    private $steps_shape;

    public function __construct()
    {
        $this->project_shape = [
            'title' => '',
            'description' => '',
            'deadline' => date('Y-m-d')
        ];
        $this->steps_shape = [
            [
                'title' => '',
                'price' => '',
                'currency_id' => [
                    'id' => 1,
                    'name' => config("params.currencies.1")
                ],
                'payment_type' => [
                    'id' => 1,
                    'name' => config("params.payment_types.1")
                ]
            ]
        ];
    }

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

            $project = Project::create($project);

            $steps = $request->steps;
            foreach ($steps as $key => $val) {
                $steps[$key]['project_id'] = $project->id;
                $steps[$key]['currency_id'] = $steps[$key]['currency_id']['id'];
                $steps[$key]['payment_type'] = $steps[$key]['payment_type']['id'];
                $steps[$key]['debt'] = $steps[$key]['price'];
                unset($steps[$key]['id']);
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
            unset($project['id']);

            $oldProject->update($project);

            $steps = $request->steps;

            $stepsNeedToBeCreated = [];
            foreach ($steps as $key => $val) {
                if ($val['id'] !== null) {
                    $steps[$key]['currency_id'] = $steps[$key]['currency_id']['id'];
                    $steps[$key]['payment_type'] = $steps[$key]['payment_type']['id'];
                    $steps[$key]['debt'] = $steps[$key]['price'];
                } else {
                    $stepNew = $steps[$key];
                    $stepNew['project_id'] = $id;
                    $stepNew['debt'] = $stepNew['price'];
                    $stepNew['currency_id'] = $stepNew['currency_id']['id'];
                    $stepNew['payment_type'] = $stepNew['payment_type']['id'];
                    $stepsNeedToBeCreated[] = $stepNew;
                    unset($steps[$key]);
                }
            }

            if (count($stepsNeedToBeCreated))
                DB::table('steps')->insert($stepsNeedToBeCreated);

            if (count($steps))
                Step::upsert($steps, ['id'], ['title', 'project_id', 'price', 'currency_id', 'payment_type']);
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
        $data = $this->getPaymentAndCurrencies();
        return $this->successResponse($data);
    }

    //* Get the currencies and payment methods
    private function getPaymentAndCurrencies($includeShapes = true)
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
        $res = [
            'payment_types' => $payment_types_res,
            'currencies' => $currencies_res,
        ];
        if ($includeShapes) {
            $res['project'] = $this->project_shape;
            $res['steps'] = $this->steps_shape;
        }
        return $res;
    }


    public function getProjectSteps(Project $project)
    {
        $steps = $project->step()->get();
        $data = [
            'project' => $project,
            'steps' => StepResource::collection($steps)
        ];
        return $this->successResponse(array_merge($this->getPaymentAndCurrencies(), $data));
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
            'steps.*.price' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value > 999999999) {
                        $fail('The ' . $attribute . ' is too big.');
                    }
                },
            ],
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

    //* Get all payments for project
    public function getPayments(Request $request, $id)
    {
        $paymentsOfProject = DB::table("payments")->whereRaw('payments.step_id in (select steps.id from steps where steps.project_id=?)', [$id])->paginate(5)->toArray();
        return $this->successResponse(array_merge($paymentsOfProject, $this->getPaymentAndCurrencies(false)));
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
