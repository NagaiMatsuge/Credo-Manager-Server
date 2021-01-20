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
        return $this->successResponse(Note::paginate(10));
    }

    //* Show note by its id
    public function show(Request $request, $id)
    {
        return $this->successResponse($id);
    }

    //* Store note
    public function store(Request $request)
    {
        $create = Note::create($request->validate([
            'text' => 'request|string|min:3'
        ]));
        return $this->successResponse($create);
    }
    
    //* Update note by its id
    public function update(Request $request, $id)
    {
        $update = $id->update($request->validate([
            'text' => 'request|string|min:3'
        ]));
        return $this->successResponse($update);
    }

    //* Delete note by its id
    public function destroy($id)
    {
        $delete = DB::table('notes')->where('id', $id)->delete();
        return $this->successResponse($delete);
    }
}
