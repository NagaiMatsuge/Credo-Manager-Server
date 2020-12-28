<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    use ResponseTrait;

    //* Fetch all Payments with pagination
    public function index(Request $request)
    {
        return PaymentResource::collection(Payment::paginate(8));
    }

    //* Show Payment by its id
    public function show(Payment $payment)
    {
        return $this->successResponse($payment);
    }

    //* Create Payment with validation
    public function store(Request $request)
    {
        $validatedPayment = $this->makeValidation($request);
        DB::transaction(function () use ($request, $validatedPayment) {
            DB::table('payments')->insert($validatedPayment);
            DB::select('update steps set debt=debt-? where id=?', [$request->amount, $request->step_id]);
        });

        return $this->successResponse([], 201, 'Successfully created');
    }

    //* Update Payment by its id
    public function update(Request $request, Payment $payment)
    {
        $updatedPayment = $this->makeValidation($request);
        DB::transaction(function () use ($request, $payment, $updatedPayment) {
            $amount = $payment->amount;
            $newAmount = $request->amount;
            DB::select('update steps set debt=debt+? where id=?', [$amount - $newAmount, $request->step_id]);
            $payment->update($updatedPayment);
        });
        return $this->successResponse([], 201, 'Successfully updated');
    }

    //* Delete payment by its id
    public function destroy(Payment $payment)
    {
        DB::transaction(function () use ($payment) {
            DB::select('update steps set debt=debt+? where id=?', [$payment->amount, $payment->step_id]);
            $payment->delete();
        });
        return $this->successResponse([], 200, 'Successfully deleted');
    }

    //* Validation
    private function makeValidation(Request $request)
    {
        return $request->validate([
            'comment' => 'required|string|min:3',
            'payment_date' => 'required|date',
            'step_id' => 'required|integer',
            'currency_id' => [
                'required',
                Rule::in(array_keys(config('params.currencies')))
            ],
            'amount' => 'required|max:13',
            'payment_type' => [
                'required',
                Rule::in(array_keys(config('params.payment_types')))
            ]
        ]);
    }
}
