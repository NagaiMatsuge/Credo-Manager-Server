<?php

namespace App\Http\Controllers;

use App\Http\Resources\StepResource;
use App\Models\Step;
use App\Models\Task;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StepController extends Controller
{
    use ResponseTrait;

    //* Fetch all steps with pagination
    public function index(Request $request)
    {
        return StepResource::collection(Step::paginate(8));
    }

    //* Show step by its id
    public function show(Step $id)
    {
        return $this->successResponse(new StepResource($id));
    }

    //* Create step with valiadtion
    public function store(Request $request)
    {
        $this->makeValidation($request);

        $step = $request->step;
        $step['debt'] = $step['price'];
        $step = Step::create($step);

        return $this->successResponse([], 201, "Successfully updated");
    }

    //* Update step by its id
    public function update(Request $request, Step $id)
    {
        $id->update($request->all());
        return $this->successResponse($id);
    }

    //* Delete step by its id
    public function destroy($id)
    {
        $delete = DB::table('steps')->where('id', $id)->delete();
        return $this->successResponse($delete);
    }

    //* Validate requests for Steps
    public function makeValidation(Request $request)
    {
        $request->validate([
            'step.title' => 'required|string|min:3|max:255',
            'step.project_id' => 'required|integer',
            'step.price' => 'required',
            'step.currency_id' => [
                'required',
                Rule::in(array_keys(config('params.currencies')))
            ],
            'step.payment_type' => [
                'required',
                Rule::in(array_keys(config('params.payment_types')))
            ],
            'step.payment_date' => 'required|date|date_format:Y-m-d',
            'tasks' => 'nullable|array',
            'tasks.*.title' => [
                'string',
                Rule::requiredIf($request->has('tasks')),
                'min:3',
                'max:255'
            ],
            'tasks.*.deadline' => [
                Rule::requiredIf($request->has('tasks')),
                'date',
                'date_format:Y-m-d'
            ]
        ]);
    }
}
