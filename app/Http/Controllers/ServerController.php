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
        if (!$request->user()->hasRole(['Admin']))
            return $this->notAllowed();

        $request->validate([
            'server.title' => 'required|min:3|max:255',
            'server.host' => 'required|string|unique:servers,host',
            'ftp_access.title' => 'required|min:3|max:255',
            'ftp_access.host' => 'required|string',
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
        DB::transaction(function () use ($data, $request) {
            $email = $request->user()->email;
            $server = Server::create($data['server']);
            FtpAccess::create(array_merge($data['ftp_access'], ['server_id' => $server->id]));
            DbAccess::create(array_merge($data['db_access'], ['server_id' => $server->id]));
            $ftp = FtpAccessFacade::setUser($data['ftp_access']['login'])->setPassword($data['ftp_access']['password']);
            $ftp_create = $ftp->create($email);
            if (!$ftp_create['success']) {
                //This throw is needed to revert datbase changes back, don't remove it!
                throw new Exception($ftp_create['message']);
                return;
            }
            $db = DbAccessFacade::setUser($data['db_access']['login'])
                ->setPassword($data['db_access']['password'])
                ->setHost($data['db_access']['server_name'])
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
        });
        return $this->successResponse([]);
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
