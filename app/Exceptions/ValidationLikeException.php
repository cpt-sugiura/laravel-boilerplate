<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * バリデーションエラー例外と似た扱いをされる例外 Illuminate\Validation\ValidationException と同じインターフェースで返される
 * Class ValidationLikeException
 * @package App\Exceptions
 */
class ValidationLikeException extends RuntimeException
{
}
