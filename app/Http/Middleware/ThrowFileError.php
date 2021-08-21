<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * PHP の $_FILES 内のエラーを読み取って例外として投げる
 * Class ThrowFileError
 * @package App\Http\Middleware
 */
class ThrowFileError
{
    public function handle(Request $request, Closure $next)
    {
        foreach ($request->allFiles() as $file) {
            /** @var UploadedFile $file */
            if ($file->getError() !== UPLOAD_ERR_OK) {// UPLOAD_ERR_OK は PHP 組み込み定数
                $errorMsgMap = [
                    UPLOAD_ERR_INI_SIZE   => Response::HTTP_REQUEST_ENTITY_TOO_LARGE,
                    UPLOAD_ERR_FORM_SIZE  => Response::HTTP_REQUEST_ENTITY_TOO_LARGE,
                    UPLOAD_ERR_PARTIAL    => Response::HTTP_BAD_REQUEST, // アップロードしている途中でリクエストを中断した時に起こるのがほとんど
                    UPLOAD_ERR_NO_FILE    => Response::HTTP_BAD_REQUEST,
                    UPLOAD_ERR_NO_TMP_DIR => Response::HTTP_INTERNAL_SERVER_ERROR,
                    UPLOAD_ERR_CANT_WRITE => Response::HTTP_INTERNAL_SERVER_ERROR,
                    UPLOAD_ERR_EXTENSION  => Response::HTTP_INTERNAL_SERVER_ERROR,
                ];
                throw new HttpException($errorMsgMap[$file->getError()], $file->getErrorMessage());
            }
        }

        return $next($request);
    }
}
