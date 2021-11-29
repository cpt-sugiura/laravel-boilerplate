<?php

namespace App\Http\Controllers\AdminBrowserAPI\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;

trait ThrottlesLogins
{
    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param  Request $request
     * @return bool
     */
    protected function hasTooManyLoginAttempts(Request $request): bool
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request), $this->maxAttempts()
        );
    }

    /**
     * Increment the login attempts for the user.
     *
     * @param  Request $request
     * @return void
     */
    protected function incrementLoginAttempts(Request $request): void
    {
        $this->limiter()->hit(
            $this->throttleKey($request), $this->decayMinutes() * 60
        );
    }

    /**
     * Clear the login locks for the given user credentials.
     *
     * @param  Request $request
     * @return void
     */
    protected function clearLoginAttempts(Request $request): void
    {
        $this->limiter()->clear($this->throttleKey($request));
    }

    /**
     * Fire an event when a lockout occurs.
     *
     * @param  Request $request
     * @return void
     */
    protected function fireLockoutEvent(Request $request): void
    {
        event(new Lockout($request));
    }

    /**
     * Get the rate limiter instance.
     *
     * @return RateLimiter
     */
    protected function limiter(): RateLimiter
    {
        return app(RateLimiter::class);
    }

    /**
     * Get the maximum number of attempts to allow.
     *
     * @return int
     */
    public function maxAttempts(): int
    {
        return property_exists($this, 'maxAttempts') ? $this->maxAttempts : 5;
    }

    /**
     * Get the number of minutes to throttle for.
     *
     * @return int
     */
    public function decayMinutes(): int
    {
        return property_exists($this, 'decayMinutes') ? $this->decayMinutes : 1;
    }
}
