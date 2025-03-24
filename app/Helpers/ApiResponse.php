<?php

namespace App\Helpers;

class ApiResponse
{

    public static function success($data = null, $meta = null)
    {

        $response  = [
            'data' => $data
        ];

        if ($meta) {
            $response['meta'] = $meta;
        }

        return response()->json($response, 200);
    }

    public  static function error($message, $statusCode = 400)
    {
        return response()->json([
            'error' => [
                'message' => $message
            ]
        ], $statusCode);
    }

    public static function validationError($errors)
    {
        return response()->json([
            'error' => [
                'message' => "Validation failed",
                'errors' => $errors
            ]
        ], 400);
    }
}
