<?php

namespace App\Traits;


use Illuminate\Http\JsonResponse;

trait ResponseHandler
{

    public function responseError($msg = "Bad Request", $statusCode = 400): JsonResponse
    {
        if (!$msg)
            $msg = __('main.bad_request');
        return $this->responseData((object)[], $msg, $statusCode);

    }

    public function responseSuccess($msg = "", $statusCode = 200): JsonResponse
    {
        if (!$msg)
            $msg = __('main.successful');
        return $this->responseData((object)[], $msg, $statusCode);
    }

    public function responseData($value, $msg = "", $statusCode = 200): JsonResponse
    {
        $success_codes = [200, 201];
        if (!$msg)
            $msg = __('main.successful');
        return response()->json([
            'success' => in_array($statusCode, $success_codes),
            'message' => $msg,
            'data' => $value
        ], $statusCode);
    }

    public function exceptionResponse($exception = null, $message = null, $statusCode = 500): JsonResponse
    {
        if (!is_null($exception))
            report($exception);
        if (is_null($message))
            $message = __('main.something_went_wrong');
        return $this->responseError($message, $statusCode);
    }

    public function responseCustomMessage($msg = null, $statusCode): JsonResponse
    {
        if (!$msg)
            $msg = __('main.successful');
        return $this->responseData((object)[], $msg, $statusCode);
    }
}
