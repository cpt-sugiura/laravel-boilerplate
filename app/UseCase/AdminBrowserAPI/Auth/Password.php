<?php

namespace App\UseCase\AdminBrowserAPI\Auth;

use Auth;
use Illuminate\Auth\Passwords\PasswordBroker;

class Password
{
    private DatabaseTokenRepository $tokenRepository;

    /**
     * Password constructor.
     * @param DatabaseTokenRepository|null $tokenRepository
     */
    public function __construct(DatabaseTokenRepository $tokenRepository = null)
    {
        $this->tokenRepository = $tokenRepository ?? DatabaseTokenRepository::createTokenRepository();
    }

    public function hash(string $plain): string
    {
        return $this->tokenRepository->getHasher()->make($plain);
    }

    public function verify(string $plain, string $hashed): bool
    {
        return $this->tokenRepository->getHasher()->check($plain, $hashed);
    }

    public function getBroker(): PasswordBroker
    {
        return new PasswordBroker(
            $this->tokenRepository,
            Auth::createUserProvider('admins')
        );
    }
}
