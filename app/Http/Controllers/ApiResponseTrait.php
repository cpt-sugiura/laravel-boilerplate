<?php

namespace App\Http\Controllers;

use App\Http\HttpStatus;
use App\Http\Presenters\BasePresenter;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use JsonException;
use JsonSerializable;
use Log;
use Response;

/**
 * APIのレスポンスで使う形式を定義するtrait
 * Trait ApiResponse
 * @package App\Http\Controllers
 */
trait ApiResponseTrait
{
    /**
     * @param  BasePresenter|array|JsonSerializable|string|int|float $result
     * @param  string                                                $message
     * @return JsonResponse
     */
    public function makeResponse(BasePresenter|array|JsonSerializable|string|int|float $result, string $message = ''): JsonResponse
    {
        if (is_array($result) || $result instanceof JsonSerializable) {
            try {
                $result = array_key_camel(json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, 4096, JSON_THROW_ON_ERROR));
            } catch (JsonException $e) {
                Log::error($e);

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
    public function makeErrorResponse(string $message, int $code = HttpStatus::INTERNAL_SERVER_ERROR): JsonResponse
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
     * @param string $message
     * @param int    $code
     *@throws HttpResponseException
     * @return void
     */
    public function throwErrorResponse(string $message, int $code = HttpStatus::INTERNAL_SERVER_ERROR): void
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
            HttpStatus::OK
        );
    }
}
