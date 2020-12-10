<?php
namespace App\Traits;

trait ResponseTrait{
    public function successResposne($data = [], int $status_c = 200){
        return response()->json([
            'data' => $data,
            'success' => true,
            'error' => false,
            'status_code' => $status_c
        ]);
    }
}