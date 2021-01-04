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

    //* Create message and message file
    public function store(Request $request)
    {
        $this->makeValidation($request);
        $uploaded_files = [];
        DB::transaction(function () use ($request, $uploaded_files) {
            $message = Message::create($request->except(array_merge(['files', 'user_id'], ['user_id' => $request->user()->id])));
            $files = $request->files;
            if ($request->files !== null) {
                foreach ($files as $key => $file) {
                    $uploaded_files[] = [
                        'file' => $this->uploadFile($file['content'], 'message_files'),
                        'message_id' => $message->id,
                        'name' => $file['name']
                    ];
                }
                DB::table("message_files")->insert($uploaded_files);
            }
        });

        event(new NewMessage($request->task_id, $request->text, $uploaded_files, $request->user()->id));

        return $this->successResponse([], 201, 'Successfully created');
    }

    //* Delete message
    public function destroy(Request $request, Message $message)
    {
        if ($message->user_id == $request->user()->id)
            $delete = DB::table('messages')->where('id', $message)->delete();
        else
            return $this->notAllowed();
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
            'files.*.name' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->files !== null;
                }),
                'string'
            ],
            'files.*.content' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->files !== null;
                }),
                'string'
            ]
        ]);
    }

    //* Get all messages of the task
    public function getMessagesForTask(Request $request, $id)
    {
        $messages = Message::leftJoin('users', 'messages.user_id', '=', 'users.id')->where('task_id', $id)->with('files')->paginate(30)->toArray();
        $res = [];
        $last_user_id = null;
        foreach ($messages['data'] as $key => $message) {
            if ($last_user_id == $message['user_id']) {
                $res[count($res) - 1]['content'][] = [
                    'text' => $message['text'],
                    'file' => $message['files']
                ];
            } else {
                $res[] = [
                    'user_id' => $message['user_id'],
                    'photo' => $message['photo'],
                    'color' => $message['color'],
                    'name' => $message['name'],
                    'content' => [
                        [
                            'text' => $message['text'],
                            'file' => $message['files']
                        ]
                    ]
                ];
            }
            $last_user_id = $message['user_id'];
        }
        $messages['data'] = $res;
        return response()->json(array_merge($messages, $this->successPagination()));
    }
}
