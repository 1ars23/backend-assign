<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Success Response method
     *
     * @param array $body
     * @param array|string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($body = [], $message = [], $statusCode = 200)
    {
        return response()->json([
            'message' => $message,
            'body' => $body
        ], $statusCode);
    }

    /**
     * Error Response method
     *
     * @param array|string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($message = [], $statusCode = 400)
    {
        return response()->json([
            'message' => $message,
            'body' => (object) []
        ], $statusCode);
    }
}
