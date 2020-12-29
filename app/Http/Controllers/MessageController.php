<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Models\Message;
use App\Traits\ResponseTrait;
use App\Traits\UploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MessageController extends Controller
{
    use ResponseTrait, UploadTrait;

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
        $this->makeValidation($request);
        $uploaded_files =[];
        DB::transaction(function () use($request, $uploaded_files) {
            $message = Message::create($request->except(['files']));
            $files = $request->files;
            if($request->has('files')) {
                foreach($files as $key => $file){
                    $uploaded_files[]['file'] = $this->uploadFile($request->input('files'), 'message_files');
                    $uploaded_files[]['message_id'] = $message->id;
                }
                DB::table("message_files")->insert($uploaded_files);
            }
        });

        event(new NewMessage($request->user_id, $request->text));

        return $this->successResponse([], 201, 'Successfully created');
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
            'text' => 'nullable|string',
            'task_id' => 'required|integer',
            'files' => [
                Rule::requiredIf(function () use ($request) {
                    return !($request->has('text')) and ($request->input('text') == null);
                }),
                'array'
            ],
        ]);
    }
}
