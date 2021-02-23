<?php

namespace App\Http\Controllers;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use JsonException;
use JsonSerializable;
use Response;

/**
 * APIのレスポンスで使う形式を定義するtrait
 * Trait ApiResponse
 * @package App\Http\Controllers
 */
trait ApiResponseTrait
{
    /**
     * @param  mixed        $result
     * @param  string       $message
     * @return JsonResponse
     */
    public function makeResponse($result, $message = ''): JsonResponse
    {
        if (is_array($result) || $result instanceof JsonSerializable) {
            try {
                $result = array_key_camel(json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, 4096, JSON_THROW_ON_ERROR));
            } catch (JsonException $e) {
                return $this->makeErrorResponse('JSONレスポンスの生成に失敗しました。');
            }
        }

        return Response::json(
            [
                'success' => true,
                'body'    => $result,
                'message' => $message,
            ]
        );
    }

    /**
     * @param  string       $message
     * @param  int          $code
     * @return JsonResponse
     */
    public function makeErrorResponse(string $message, $code = 500): JsonResponse
    {
        return Response::json(
            [
                'success' => false,
                'message' => $message,
            ],
            $code
        );
    }

    /**
     * @param  string                $message
     * @param  int                   $code
     * @throws HttpResponseException
     * @return void
     */
    public function throwErrorResponse(string $message, $code = 500): void
    {
        throw new HttpResponseException(Response::json(['success' => false, 'message' => $message], $code));
    }

    /**
     * @param  string       $message
     * @return JsonResponse
     */
    public function makeSuccessResponse(string $message = ''): JsonResponse
    {
        return Response::json(
            [
                'success' => true,
                'message' => $message,
            ],
            200
        );
    }
}
