<?php

namespace App\Http\Controllers;

use App\Models\DbAccess;
use App\Models\FtpAccess;
use App\Models\Server;
use App\Patterns\Builders\DbAccess\DbAccessFacade;
use App\Patterns\Builders\FtpAccess\FtpAccessFacade;
use App\Patterns\Builders\Server\ServerFacade;
use App\Traits\ResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServerController extends Controller
{
    use ResponseTrait;

    //* Fetch all servers with pagination
    public function index(Request $request)
    {
        $server_details = Server::paginate(10);
        return $this->successResponse($server_details);
    }

    //* Create server, ftp_access, db_access with validation
    public function store(Request $request)
    {
        $request->validate([
            'server.title' => 'required|min:3|max:255',
            'server.host' => 'required|integer|unique:servers,host',
            'ftp_access.title' => 'required|min:3|max:255',
            'ftp_access.host' => 'required|integer',
            'ftp_access.login' => 'required',
            'ftp_access.password' => 'required|string',
            'ftp_access.description' => 'nullable|min:10',
            'db_access.server_name' => 'required',
            'db_access.db_name' => 'required|unique:db_access,db_name',
            'db_access.login' => 'required',
            'db_access.password' => 'required|string',
            'db_access.description' => 'nullable|min:10',
        ]);

        $data = $request->input();
        $error = null;
        DB::transaction(function () use ($data) {
            $server = Server::create($data['server']);
            FtpAccess::create(array_merge($data['ftp_access'], ['server_id' => $server->id]));
            DbAccess::create(array_merge($data['db_access'], ['server_id' => $server->id]));
            $ftp = FtpAccessFacade::setUser($data['ftp_access']['login'])->setPassword($data['ftp_access']['password']);
            $ftp_create = $ftp->create();
            if (!$ftp_create['success']) {
                $error = $ftp['message'];
                //This throw is needed to revert datbase changes back, don't remove it!
                throw new ModelNotFoundException();
                return;
            }
            $db = DbAccessFacade::setUser($data['db_access']['login'])
                ->setPassword($data['db_access']['password'])
                ->setHost($data['db_access']['server_name'])
                ->setDatabaseName($data['db_access']['db_name']);
            $db_create = $db->create();

            if (!$db_create['success']) {
                //Delete the user
                $ftp_delete = $ftp->delete();
                if (!$ftp_delete["success"]) info($ftp_delete['message']);
                $error = $db['message'];
                //This throw is needed to revert datbase changes back, don't remove it!
                throw new ModelNotFoundException();
                return;
            }

            $server = ServerFacade::setUser($data['server']['host'])->setDir("/home//" + $data["ftp_access"]["login"])->create();
            if (!$server['success']) {
                //Delete the user
                $ftp_delete = $ftp->delete();
                if (!$ftp_delete["success"]) info($ftp_delete['message']);
                //Delete database access
                $db_delete = $db->delete();
                if (!$db_delete["success"]) info($db_delete['message']);
                $error = $db['message'];
                //This throw is needed to revert datbase changes back, don't remove it!
                throw new ModelNotFoundException();
                return;
            }
        });
        if ($error) return $this->successResponse([]);
        else return $this->errorResponse([$error]);
    }

    //* Show server, ftp_access, db_access by server's id    
    public function show(Request $request, $id)
    {
        $id = DB::table('servers')
            ->join('ftp_access', 'servers.id', '=', 'ftp_access.server_id')
            ->join('db_access', 'servers.id', '=', 'db_access.server_id')
            ->select('ftp_access.*', 'db_access.*', 'servers.title as server_name', 'servers.host as server_host')
            ->get();
        return $this->successResponse($id);
    }

    //* Update server, ftp_access, db-access by server's id    
    public function update(Request $request, $id)
    {
        $data = $request->input();
        DB::transaction(function () use ($id, $data) {
            DB::table('servers')->where('id', $id)->update($data['server']);
            DB::table('ftp_access')->where('id', $id)->update($data['ftp_access']);
            DB::table('db_access')->where('id', $id)->update($data['db_access']);
        });
    }

    //* Delete server, ftp_access, db_access by server's id    
    public function destroy($id)
    {
        $delete = DB::table('servers')->where('id', $id)->delete();
        return $this->successResponse($delete);
    }
}
