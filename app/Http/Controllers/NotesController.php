<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotesController extends Controller
{
    use ResponseTrait;

    //* Get all notes
    public function index(Request $request)
    {
        return $this->successResponse(Note::where('user_id', $request->user()->id)->paginate(30));
    }

    //* Show note by its id
    public function show(Request $request, $id)
    {
        $res = DB::table('notes')->where('id', $id)->where('user_id', $request->user()->id)->first();
        return $this->successResponse($res);
    }

    //* Store note
    public function store(Request $request)
    {
        $request->validate([
            'text' => 'required|string|min:3'
        ]);
        $create = Note::create([
            'text' => $request->text,
            'user_id' => $request->user()->id
        ]);
        return $this->successResponse($create);
    }

    //* Update note by its id
    public function update(Request $request, $id)
    {
        $request->validate([
            'text' => 'required|string|min:3'
        ]);
        DB::table('notes')->where('id', $id)->where('user_id', $request->user()->id)->update([
            'text' => $request->text
        ]);
        return $this->successResponse(true);
    }

    //* Delete note by its id
    public function destroy(Request $request, $id)
    {
        $delete = DB::table('notes')->where('id', $id)->where('user_id', $request->user()->id)->delete();
        return $this->successResponse($delete);
    }
}
