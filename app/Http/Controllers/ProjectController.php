<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        $projects = Project::paginate(10);
        return $this->successResponse($projects);    
    }

    public function show(Project $id)
    {
        return $this->successResponse($id);
    }

    public function store(Request $request)
    {
        $create_projects = Project::create($request->validate([
            'server_id' => 'required|integer',
            'title' => 'required|string|min:3|max:255',
            'description' => 'nullable|min:10'
        ]));
        return $this->successResponse($create_projects);
    }

    public function update(Request $request, Project $id)
    {
        $id->update($request->all());
        return $this->successResponse($id);
    }

    public function destroy($project)
    {
        $delete = DB::table('projects')->where('id', $project)->delete();
        return $this->successResponse($delete);
    }
}
