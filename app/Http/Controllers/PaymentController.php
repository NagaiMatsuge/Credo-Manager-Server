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
        $create_payment = DB::table('payments')->insert($request->validate([
            'comment' => 'required|min:3',
            'payment_date' => 'required|date|date_format: Y-m-d',
            'step_id' => 'required',
            'currency_id' => 'required',
            'amount' => 'required|integer',
            'payment_type' => 'required'
        ]));
        
        return $this->successResponse($create_payment);
    }

    //* Update Payment by its id
    public function update(Request $request, Payment $id)
    {
        $id = DB::table('payments')->update($request->validate([
            'comment' => 'required|min:3',
            'payment_date' => 'required|date|date_format: Y-m-d',
            'step_id' => 'required',
            'currency_id' => 'required',
            'amount' => 'required|integer',
            'payment_type' => 'required'
        ]));
        return $this->successResponse($id);
    }
}
