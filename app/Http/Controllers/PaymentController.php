<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    use ResponseTrait;

    //* Fetch all Payments with pagination
    public function index(Request $request)
    {
        $payment = Payment::paginate(8);
        return $this->successResponse(PaymentResource::collection($payment));
    }

    //* Show Payment by its id
    public function show(Payment $id)
    {
        return $this->successResponse($id);
    }

    //* Create Payment with validation
    public function store(Request $request)
    {
        $create_payment = DB::table('payments')->insert($this->makeValidation($request));

        return $this->successResponse($create_payment);
    }

    //* Update Payment by its id
    public function update(Request $request, Payment $id)
    {
        $id->update($this->makeValidation($request));
        return $this->successResponse($id);
    }

    //* Delete payment by its id
    public function destroy($id)
    {
        $delete = DB::table('payments')->where('id', $id)->delete();
        return $this->successResponse($delete);
    }

    //* Validation
    private function makeValidation(Request $request)
    {
        return $request->validate([
            'comment' => 'required|string|min:3',
            'payment_date' => 'required|date',
            'step_id' => 'required|integer',
            'currency_id' => 'required|integer',
            'amount' => 'required',
            'payment_type' => 'required|integer'
        ]);
    }
}
