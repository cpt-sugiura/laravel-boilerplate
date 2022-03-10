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
     * @return bool
     */
    protected function hasTooManyLoginAttempts(): bool
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey(), $this->maxAttempts()
        );
    }

    /**
     * Increment the login attempts for the user.
     *
     * @return void
     */
    protected function incrementLoginAttempts(): void
    {
        $this->limiter()->hit(
            $this->throttleKey(), $this->decayMinutes() * 60
        );
    }

    /**
     * Clear the login locks for the given user credentials.
     *
     * @return void
     */
    protected function clearLoginAttempts(): void
    {
        $this->limiter()->clear($this->throttleKey());
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
