<?php

namespace Tests;

use App\Http\Middleware\Authenticate;
use Exception;

trait WithoutAuthMiddleware
{
    /**
     * Prevent auth middleware from being executed for this test class.
     *
     * @throws Exception
     */
    public function disableAuthMiddlewareForAllTests(): void
    {
        if (method_exists($this, 'withoutMiddleware')) {
            $this->withoutMiddleware(Authenticate::class);
        } else {
            throw new Exception('Unable to disable middleware. MakesHttpRequests trait not used.');
        }
    }
}
