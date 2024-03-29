<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Models\Message;
use App\Models\MessageFile;
use App\Models\User;
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
            $auth_user_id = $request->user()->id;
            $message = Message::create(array_merge($request->except(['files', 'user_id']), ['user_id' => $auth_user_id]));
            $files = $request->input('files');
            if (!empty($files)) {
                foreach ($files as $key => $file) {
                    $uploaded_files[] = [
                        'file' => $this->uploadFile($file['content'], 'message_files'),
                        'message_id' => $message->id,
                        'name' => $file['name'],
                        'size' => $file['size']
                    ];
                }
                DB::table("message_files")->insert($uploaded_files);
            }
            $user_ids = DB::table('task_user')->where('task_id', $request->task_id)->get()->unique('user_id')->pluck('user_id')->toArray();
            $admin_manager_ids = User::role(['Admin', 'Manager'])->get()->pluck('id')->toArray();
            $user_ids = array_unique(array_merge($user_ids, $admin_manager_ids));

            $unread_messages = [];
            foreach ($user_ids as $user_id) {
                if ($user_id !== $auth_user_id) {
                    $unread_messages[] = [
                        'user_id' => $user_id,
                        'message_id' => $message->id
                    ];
                }
                broadcast(new NewMessage($request->task_id, $request->text, $uploaded_files, $request->user(), $user_id));
            }
            DB::table('unread_messages')->insert($unread_messages);
        });
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
                'string',
                function ($attribute, $value, $fail) {
                    $size = (int)(strlen(rtrim($value, '=')) * 0.75);
                    if ($size > 2000000) {
                        $fail('The ' . $attribute . ' is too large.');
                    }
                },
            ],
            'file.*.size' => [
                'string'
            ]
        ]);
    }

    //* Get all messages of the task
    public function getMessagesForTask(Request $request, $id)
    {
        $messages = Message::leftJoin('users', 'messages.user_id', '=', 'users.id')->select('messages.*', 'users.name', 'users.email', 'users.photo', 'users.color')->where('messages.task_id', $id)->orderBy('messages.created_at', 'desc')->paginate(30)->toArray();
        $message_ids = array_column($messages['data'], 'id');
        $message_files = MessageFile::whereIn('message_id', $message_ids)->get();
        foreach ($messages['data'] as $key => $message) {
            $messages['data'][$key]['files'] = [];
            foreach ($message_files as $message_file) {
                if ($message_file['message_id'] == $message['id']) {
                    $messages['data'][$key]['files'][] = $message_file;
                }
            }
        }
        $res = [];
        $last_user_id = null;
        $count = count($messages['data']) - 1;
        for ($i = $count; $i >= 0; $i--) {
            if ($last_user_id == $messages['data'][$i]['user_id']) {
                $res[count($res) - 1]['content'][] = [
                    'text' => $messages['data'][$i]['text'],
                    'files' => $messages['data'][$i]['files']
                ];
            } else {
                $res[] = [
                    'user_id' => $messages['data'][$i]['user_id'],
                    'photo' => $messages['data'][$i]['photo'],
                    'color' => $messages['data'][$i]['color'],
                    'name' => $messages['data'][$i]['name'],
                    'content' => [
                        [
                            'text' => $messages['data'][$i]['text'],
                            'files' => $messages['data'][$i]['files']
                        ]
                    ]
                ];
            }
            $last_user_id = $messages['data'][$i]['user_id'];
        }
        $messages['data'] = $res;
        return response()->json(array_merge($messages, $this->successPagination()));
    }

    public function userHasReadMessage(Request $request)
    {
        $request->validate([
            'task_id' => 'integer|required'
        ]);
        $res = DB::table('unread_messages')->where('user_id', $request->user()->id)->whereRaw('message_id in (select messages.id from messages where messages.task_id=?)', [$request->task_id])->delete();
        return $this->successResponse($res);
    }
}
