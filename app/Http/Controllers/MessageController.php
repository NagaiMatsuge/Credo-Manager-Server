<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MessageController extends Controller
{
    use ResponseTrait;

    //* Fetch all messages
    public function index(Request $request)
    {
        $msg = Message::all();
        return $this->successResponse($msg);
    }

    //* Show message by its id
    public function show(Message $message)
    {
        return $this->successResponse($message);
    }

    //* Create message
    public function store(Request $request)
    {
        $validation = $this->makeValidation($request);
        $create = Message::create($validation);

        return $this->successResponse($create);
    }

    //* Update message by its id
    public function update(Request $request, Message $message)
    {
        $validation = $this->makeValidation($request);
        $message->update($validation);
        return $this->successResponse($message);
    }
    //* Delete message
    public function destroy(Message $message)
    {
        $delete = DB::table('messages')->where('id', $message)->delete();
        return $this->successResponse($delete);
    }
    //* Validation
    public function makeValidation(Request $request)
    {
        return $request->validate([
            'user_id' => 'required',
            'text' => 'nullable',
            'task_id' => 'required',
            'file' => [
                Rule::requiredIf(function () use ($request) {
                    return !($request->has('text')) and ($request->input('text') == null);
                }),
                'string'
            ],
        ]);
    }
}
