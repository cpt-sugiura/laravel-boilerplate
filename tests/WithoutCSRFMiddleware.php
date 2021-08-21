<?php

namespace Tests;

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\VerifyCsrfToken;
use Exception;

trait WithoutCSRFMiddleware
{
    /**
     * Prevent auth middleware from being executed for this test class.
     *
     * @throws Exception
     */
    public function disableCSRFMiddlewareForAllTests(): void
    {
        if (method_exists($this, 'withoutMiddleware')) {
            $this->withoutMiddleware(VerifyCsrfToken::class);
        } else {
            throw new Exception('Unable to disable middleware. MakesHttpRequests trait not used.');
        }
    }
}
