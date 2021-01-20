<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

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
        
    }
}
