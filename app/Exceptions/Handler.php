<?php

namespace App\Exceptions;

use App\Http\HttpStatus;
use App\Models\Eloquents\BaseEloquent;
use Arr;
use Auth;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use JsonException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request       $request
     * @param  Exception     $exception
     * @throws JsonException
     * @throws \Throwable
     * @return Response
     */
    public function render($request, \Throwable $exception): Response
    {
        if ($exception instanceof ValidationException) {
            return response()->json(
                [
                    'success' => false,
                    'message' => implode("\n", Arr::flatten($exception->validator->getMessageBag()->toArray())),
                    'body'    => [
                        'errors' => $exception->validator->getMessageBag()->toArray(),
                    ],
                ],
                HttpStatus::UNPROCESSABLE_ENTITY
            );
        }
        if ($exception instanceof ValidationLikeException) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
                HttpStatus::UNPROCESSABLE_ENTITY
            );
        }

        if ($exception instanceof ModelNotFoundException) {
            $exception = $this->handleModelNotFoundException($exception);
        }

        $response = parent::render($request, $exception);
        (config('app.env') !== config('app.in_production_env_name'))
        && ($response instanceof JsonResponse)
        && $this->setQueryLogToJsonResponse($response);

        if ($response->getStatusCode() === HttpStatus::INTERNAL_SERVER_ERROR) {
            $logger = dev_slack_log();
            $logger->error('ERROR in '.$request->url());
            $logger->error(implode("\n", [
                'LOGIN_INFO',
                'web: '.Auth::guard('web')->id(),
                'admin_web: '.Auth::guard('admin_web')->id(),
            ]));
            $logger->error($exception);
            if ($response instanceof JsonResponse) {
                $content = $response->getContent();
                foreach (str_split($content, 5000) as $unit) {
                    $logger->error($unit);
                }
            }
        }

        return $response;
    }

    /**
     * @param  JsonResponse  $response
     * @throws JsonException
     * @return JsonResponse
     */
    protected function setQueryLogToJsonResponse(JsonResponse $response): JsonResponse
    {
        $queryLog    = DB::getQueryLog();
        $content     = $response->getContent();
        $baseContent = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        $newContent  = array_merge(['queryLog' => $queryLog], $baseContent);
        $response->setData($newContent);

        return $response;
    }

    /**
     * @param  ModelNotFoundException $exception
     * @return NotFoundHttpException
     */
    protected function handleModelNotFoundException(ModelNotFoundException $exception): NotFoundHttpException
    {
        /** @var BaseEloquent $className */
        $className = $exception->getModel();
        $message   = $className::getNaturalLanguageName().'が見つかりませんでした。';

        return new NotFoundHttpException($message, $exception);
    }
}
