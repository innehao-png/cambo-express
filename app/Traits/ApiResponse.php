<?php

namespace App\Traits;

trait ApiResponse
{
    /**
     * Send a success response.
     *
     * @param  mixed  $data
     * @param  string|null  $message
     * @param  int  $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data = [], $message = null, $code = 200)
    {
        $response = is_array($data) ? $data : ['data' => $data];

        if ($message !== null) {
            $response['message'] = $message;
        }

        return response()->json($response, $code);
    }

    /**
     * Send an error response.
     *
     * @param  string  $message
     * @param  int  $code
     * @param  array  $additional
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($message, $code = 400, $additional = [])
    {
        $response = ['message' => $message];

        if (!empty($additional)) {
            $response = array_merge($response, $additional);
        }

        return response()->json($response, $code);
    }

    /**
     * Send a not found response.
     *
     * @param  string  $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function notFoundResponse($message = 'Resource not found')
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Send an unauthorized response.
     *
     * @param  string  $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function unauthorizedResponse($message = 'Unauthorized')
    {
        return $this->errorResponse($message, 401);
    }
}
