<?php

namespace App\Traits;

trait ResponseTrait
{
    public function successResponse($data = [], int $status_c = 200, $message = '', array $additional = null)
    {
        $res = [
            'data' => $data,
            'success' => true,
            'error' => false,
            'status_code' => $status_c,
            'message' => $message,
        ];
        if ($additional)
            $res[$additional['name']] = $additional['data'];
        return response()->json($res)->header('Access-Control-Allow-Origin', '*');
    }

    public function errorResponse($message, int $status_c = 400)
    {
        return response()->json([
            'data' => [],
            'success' => false,
            'error' => true,
            'status_code' => $status_c,
            'message' => $message
        ])->header('Access-Control-Allow-Origin', '*');
    }

    public function successPagination($data = [], int $status_c = 200, $message = '')
    {
        return [
            'success' => true,
            'error' => false,
            'status_code' => $status_c,
            'message' => $message
        ];
    }

    //* User doesn't have enought permissions
    public function notAllowed()
    {
        return response()->json([
            'success' => false,
            'error' => true,
            'status_code' => 403,
            'message' => 'Not-allowed'
        ]);
    }
}
