<?php

namespace App\Http\Controllers;

use App\Http\Resources\CurrencyCollection;
use App\Http\Resources\CurrencyResource;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\StepResource;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Server;
use App\Models\Step;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Traits\DateTimeTrait;
use App\Traits\UploadTrait;
use App\Traits\getPaymentAndCurrenciesTrait;

class ProjectController extends Controller
{
    use ResponseTrait, DateTimeTrait, UploadTrait, getPaymentAndCurrenciesTrait;

    //* Fetch all projects with deadline and paid amount in percentage
    public function index(Request $request)
    {
        $request->validate([
            'archive' => 'nullable|boolean'
        ]);
        $projects = DB::table('projects')->select('projects.*', DB::raw('(SELECT COUNT(tasks.id) FROM tasks WHERE tasks.step_id IN (SELECT id FROM steps WHERE steps.project_id=projects.id)) as task_count'), DB::raw('(SELECT COUNT(tasks.id) FROM tasks WHERE tasks.step_id IN (SELECT id FROM steps WHERE steps.project_id=projects.id AND tasks.approved=1)) as approved_count'), DB::raw('(SELECT SUM(t1.percent)/ COUNT(t1.percent) AS paid_percent FROM (SELECT ((steps.price - steps.debt) * 100 / steps.price) AS percent FROM steps WHERE steps.project_id=projects.id) AS t1) AS paid_percent'))->where('archived', $request->archive ?? false)->paginate(10);
        $counts = DB::select("select (select count(projects.id) from projects where projects.archived=1) as count_archived, (select count(projects.id) from projects where projects.archived=0) as count_not_archived");
        $projects = $projects->toArray();
        foreach ($projects['data'] as $key => $project) {
            $date = explode("-", $project->deadline);
            $projects['data'][$key]->deadline = $date[2] . ' ' . config('params.month_format.' . $date[1]) . ' ' .  $date[0];
        }
        $counts = [
            'count_archived' => $counts[0]->count_archived,
            'count_not_archived' => $counts[0]->count_not_archived,
        ];
        return $this->successResponse(array_merge($projects, $counts));
    }

    //* Create project and steps with validation    
    public function store(Request $request)
    {
        $this->makeValidation($request);

        DB::transaction(function () use ($request) {
            $project = $request->project;
            if ($request->has('project.photo') and $request->input('project.photo') !== null) {
                $project['photo'] = $this->uploadFile($request->input('project.photo'), 'projects');
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

            if ($request->has('project.photo')) {
                if ($oldProject->photo)
                    Storage::disk('public')->delete($oldProject->photo);
                $project['photo'] = $this->uploadFile($request->input('project.photo'), 'projects');
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
        $delete = Project::where('id', $id)->delete();
        return $this->successResponse($delete);
    }

    //* Get all payment credentials
    public function getCredentials(Request $request)
    {
        $data = $this->getPaymentAndCurrencies();
        return $this->successResponse($data);
    }

    //* Get the currencies and payment methods
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
            'project.photo' => 'nullable|string',
            'project.color' => [
                Rule::requiredIf(function () use ($request) {
                    return !($request->has('project.photo')) and ($request->input('project.photo') == null);
                }),
                'string'
            ],
            'project.title' => 'required|string|min:3|max:255',
            'project.description' => 'nullable|min:10',
            'project.deadline' => 'required|date|date_format:Y-m-d',
            'steps' => 'required|array',
            'steps.*.price' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value > 999999999999) {
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
        $paymentsOfProject = DB::table('payments as t2')->select('t2.*', DB::raw('(select t3.price from steps as t3 where t3.id=t2.step_id) as total_sum'), DB::raw('(select sum(t1.amount) from payments as t1 where t1.payment_date <= t2.payment_date and t1.step_id=t2.step_id group by t1.step_id) as minus_sum'), DB::raw('(select total_sum - minus_sum) as debt_left'), DB::raw('(select t5.title from steps as t5 where t5.id=t2.step_id) as step_title'))->whereRaw('t2.step_id in (select t4.id from steps as t4 where t4.project_id=?)', [$id])->orderBy('t2.payment_date', 'desc')->paginate(8);

        return (CurrencyResource::collection($paymentsOfProject)->additional($this->successPagination()));
    }

    //* Archive or dearchive the project
    public function archive(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|boolean'
        ]);
        DB::table('projects')->where('id', $id)->update(['archived' => $request->status]);
        return $this->successResponse([], 200, "Successfully Updated");
    }

    //* Get projects server
    public function getServer(Request $request, $id)
    {
        $res = Server::where('project_id', $id)->with('ftp_access')->with('db_access')->get();
        return $this->successResponse($res);
    }
}
