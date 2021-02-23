<?php

namespace App\Exceptions;

use App\Http\HttpStatus;
use App\Models\Eloquents\BaseEloquent;
use App\UseCase\SQLFormatter\SqlFormatter;
use Arr;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use JsonException;
use Log;
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
     * @param  Request   $request
     * @param  Exception $exception
     * @throws Exception
     * @return Response
     */
    public function render($request, Exception $exception): Response
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
        ($response instanceof JsonResponse) && $this->setQueryLogToJsonResponse($response);

        if ($response->getStatusCode() === HttpStatus::INTERNAL_SERVER_ERROR) {
            Log::channel('develop_slack')->error(json_encode(
                [
                 'url'      => $request->url(),
                 'params'   => $request->all(),
                 'queryLog' => SqlFormatter::formatFromDBGetQueryLog(DB::getQueryLog()),
                ],
                JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT
            ));
            Log::channel('develop_slack')->error($exception);
            $content = $response->getContent();
            foreach (str_split($content, 5000) as $unit) {
                Log::channel('develop_slack')->error($unit);
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
