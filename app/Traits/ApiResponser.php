<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait ApiResponser
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    function sendResponse($data = [], $message = '', $code = 200):JsonResponse
    {
        $response = [
            'data' => $data,
            'message' => $message,
            'errors' => null,
        ];

        return response()->json($response, $code);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    function sendError($message, $errors = [], $code = 404):JsonResponse
    {
        $response = [
            'data' => null,
            'message' => $message,
            'errors' => $errors,
        ];

        return response()->json($response, $code);
    }
}
