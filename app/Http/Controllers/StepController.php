<?php

namespace App\Http\Controllers;

use App\Models\Step;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StepController extends Controller
{
    use ResponseTrait;

    //* Fetch all steps with pagination
    public function index(Request $request)
    {
        $steps = Step::paginate(8);
        return $this->successResponse($steps);
    }

    //* Show step by its id
    public function show(Step $id)
    {
        return $this->successResponse($id);
    }

    //* Create step with valiadtion
    public function store(Request $request)
    {
        $create_step = Step::create($request->validate([
            'title' => 'required|string|min:3|max:255',
            'project_id' => 'required|integer',
            'price' => 'required|integer',
            'debt' => 'required|integer',
            'currency_id' => 'required|integer',
            'payment_type' => 'required|integer',
            'payment_date' => 'required|date|date_format:Y-m-d',
        ]));
        return $this->successResponse($create_step);
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
}
