<?php

namespace App\Http\Controllers;

use App\Models\DbAccess;
use App\Models\FtpAccess;
use App\Models\Server;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServerController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        $server_details = Server::paginate(10);
        return $this->successResponse($server_details);
    }
    public function store(Request $request)
    {
       $request->validate([
           'server.title' => 'required|min:3|max:255',
           'server.host' => 'required|integer',
           'ftp_access.title' => 'required|min:3|max:255',
           'ftp_access.host' => 'required|integer',
           'ftp_access.login' => 'required',
           'ftp_access.password' => 'required|string',
           'ftp_access.description' => 'nullable|min:10',
           'db_access.server_name' => 'required',
           'db_access.db_name' => 'required',
           'db_access.login' => 'required',
           'db_access.password' => 'required|string',
           'db_access.description' => 'nullable|min:10',
       ]);
       $data = $request->input();
       DB::transaction(function () use($data) {
            $server = Server::create($data['server']);
            FtpAccess::create(array_merge($data['ftp_access'], ['server_id' => $server->id]));
            DbAccess::create(array_merge($data['db_access'], ['server_id' => $server->id]));
       });
    }
    public function show(Request $request, $id)
    {
        $id = DB::table('servers')
        ->join('ftp_access', 'servers.id', '=', 'ftp_access.server_id')
        ->join('db_access', 'servers.id', '=', 'db_access.server_id')
        ->select('ftp_access.*','db_access.*', 'servers.title as server_name', 'servers.host as server_host')
        ->get();
        return $this->successResponse($id);
    }

    public function update(Request $request, $id)
    {
        $data = $request->input();
        DB::transaction(function () use($id, $data) {
            DB::table('servers')->where('id', $id)->update($data['server']);
            DB::table('ftp_access')->where('id', $id)->update($data['ftp_access']);
            DB::table('db_access')->where('id', $id)->update($data['db_access']);          
        });
    }
    public function destroy($id)
    {
        $delete = DB::table('servers')->where('id', $id)->delete();
        return $this->successResponse($delete);
    }
}
