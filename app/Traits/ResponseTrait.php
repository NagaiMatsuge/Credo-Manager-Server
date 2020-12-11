<?php

namespace App\Traits;

trait ResponseTrait
{
    public function successResponse($data = [], int $status_c = 200, $message = '')
    {
        return response()->json([
            'data' => $data,
            'success' => true,
            'error' => false,
            'status_code' => $status_c,
            'message' => $message
        ]);
    }

    public function errorResponse($message, int $status_c = 400)
    {
        return response()->json([
            'data' => [],
            'success' => false,
            'error' => true,
            'status_code' => $status_c,
            'message' => $message
        ]);
    }
}
