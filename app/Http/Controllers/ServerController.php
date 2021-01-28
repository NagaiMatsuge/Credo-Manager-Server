<?php

namespace App\Http\Controllers;

use App\Models\DbAccess;
use App\Models\FtpAccess;
use App\Models\Server;
use App\Patterns\Builders\DbAccess\DbAccessFacade;
use App\Patterns\Builders\FtpAccess\FtpAccessFacade;
use App\Patterns\Builders\Server\ServerFacade;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ServerController extends Controller
{
    use ResponseTrait;

    //* Fetch all servers with pagination
    public function index(Request $request)
    {
        $server_details = Server::with('ftp_access')->with('db_access')->paginate(10);
        return $this->successResponse($server_details);
    }

    //* Create server, ftp_access, db_access with validation
    public function store(Request $request)
    {
        if (!$request->user()->hasRole(['Admin']))
            return $this->notAllowed();

        $this->validateRequest($request);

        $data = $request->input();
        $data['server']['type'] = $data['server']['type_id'];
        unset($data['server']['type_id']);
        DB::transaction(function () use ($data, $request) {
            $email = $request->user()->email;
            $server = Server::create($data['server']);
            FtpAccess::create(array_merge($data['ftp_access'], ['server_id' => $server->id]));
            DbAccess::create(array_merge($data['db_access'], ['server_id' => $server->id]));
            if ($request->input('server.type_id') == 1) {

                $ftp = FtpAccessFacade::setUser($data['ftp_access']['login'])->setPassword($data['ftp_access']['password']);
                $ftp_create = $ftp->create($email);
                if (!$ftp_create['success']) {
                    //This throw is needed to revert datbase changes back, don't remove it!
                    throw new Exception($ftp_create['message']);
                    return;
                }
                $db = DbAccessFacade::setUser($data['db_access']['login'])
                    ->setPassword($data['db_access']['password'])
                    ->setDatabaseName($data['db_access']['db_name']);
                $db_create = $db->create($email);

                if (!$db_create['success']) {
                    //Delete the user
                    $ftp->delete($email);
                    //This throw is needed to revert datbase changes back, don't remove it!
                    throw new Exception($db_create['message']);
                    return;
                }

                $server = ServerFacade::setHost($data['server']['host'])->create($email);
                if (!$server['success']) {
                    //Delete the user
                    $ftp->delete($email);
                    //Delete database access
                    $db->delete($email);
                    //This throw is needed to revert datbase changes back, don't remove it!
                    throw new Exception($server['message']);
                    return;
                }
            }
        });
        return $this->successResponse([]);
    }

    //* Update server, ftp_access, db-access by server's id    
    public function update(Request $request, $id)
    {
        $this->validateRequest($request);
        $server = DB::table('servers')->where('id', $id)->first();
        if ($server->type == 1)
            return $this->updateLocalServer($request, $id);
        else
            return $this->updateClientServer($request, $id);
    }

    //* Update local server information
    public function updateLocalServer(Request $request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $server = $request->only(['server.title']);
            $ftp = $request->only(['ftp_access.description']);
            $db = $request->only(['db_access.description']);
            DB::table('servers')->where('id', $id)->update($server['server']);
            DB::table('ftp_access')->where('server_id', $id)->update($ftp['ftp_access']);
            DB::table('db_access')->where('server_id', $id)->update($db['db_access']);
        });
    }

    //* Show one server with ftp and db_accesses
    public function show(Request $request, $id)
    {
        $res = Server::where('id', $id)->with('ftp_access')->with('db_access')->first();
        return $this->successResponse($res);
    }

    //* Update client server
    public function updateClientServer(Request $request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $server = $request->only(['server.title', 'server.host']);
            $ftp = $request->only(['ftp_access.description', 'ftp_access.port', 'ftp_access.host', 'ftp_access.login', 'ftp_access.password']);
            $db = $request->only(['db_access.description', 'db_access.db_name', 'db_access.host', 'db_access.login', 'db_access.password']);
            DB::table('servers')->where('id', $id)->update($server['server']);
            DB::table('ftp_access')->where('server_id', $id)->update($ftp['ftp_access']);
            DB::table('db_access')->where('server_id', $id)->update($db['db_access']);
        });
    }

    private function validateRequest(Request $request)
    {
        $request->validate([
            'server.type_id' => [
                'required',
                Rule::in(array_keys(config('params.server_types')))
            ],
            'server.title' => 'required|min:3|max:255',
            'server.host' => 'required|string|unique:servers,host',
            'server.project_id' => 'required|integer',
            'ftp_access.port' => 'required|integer',
            'ftp_access.host' => 'required|string',
            'ftp_access.login' => 'required|string',
            'ftp_access.password' => 'required|string',
            'ftp_access.description' => 'nullable|string|min:6',
            'db_access.host' => 'required|string',
            'db_access.db_name' => 'required|string|unique:db_access,db_name',
            'db_access.login' => 'required|string',
            'db_access.password' => 'required|string',
            'db_access.description' => 'nullable|string|min:6',
        ]);
    }

    //* Delete server, ftp_access, db_access by server's id    
    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasRole(['Admin']))
            return $this->notAllowed();

        DB::transaction(function () use ($request, $id) {
            $email = $request->user()->email;
            $serverDetails = Server::where('id', $id)->with('ftp_access')->with('db_access')->first()->toArray();
            Server::where('id', $id)->delete();
            $server_host = $serverDetails['host'];
            $server_delete = ServerFacade::setHost($server_host)->delete($email);
            if (!$server_delete['success']) throw new Exception($server_delete['message']);
            foreach ($serverDetails['ftp_access'] as $ftp) {
                $ftp_user = $ftp['login'];
                $ftp_delete = FtpAccessFacade::setUser($ftp_user)->delete($email);
                if (!$ftp_delete['success']) throw new Exception($ftp_delete['message']);
            }
            foreach ($serverDetails['db_access'] as $db) {
                $db_user = $db['login'];
                $db_name = $db['db_name'];
                $db_delete = DbAccessFacade::setUser($db_user)->setDatabaseName($db_name)->delete($email);
                if (!$db_delete['success']) throw new Exception($db_delete['message']);
            }
        });
        return $this->successResponse([]);
    }
}
