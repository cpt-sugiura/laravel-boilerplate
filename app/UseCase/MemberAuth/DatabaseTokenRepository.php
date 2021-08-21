<?php

namespace App\UseCase\MemberAuth;

use App\Models\Eloquents\Member;
use Illuminate\Auth\Passwords;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseTokenRepository extends Passwords\DatabaseTokenRepository
{
    /** @var CanResetPasswordContract|Member */
    protected Member $member;

    public function create(CanResetPasswordContract $member): string
    {
        $this->member = $member;

        return parent::create($member);
    }

    /**
     * @param  string $email
     * @param  string $token
     * @return array
     */
    protected function getPayload($email, $token)
    {
        return [
            'email'      => $email,
            'token'      => $this->hasher->make($token),
            'created_at' => new Carbon()
        ];
    }

    /**
     * 与えられた設定を元に、MembersDatabaseTokenRepositoryのインスタンスを作って返す。
     * @return DatabaseTokenRepository
     * @see PasswordBrokerManager
     */
    public static function createTokenRepository(): DatabaseTokenRepository
    {
        $config = config('auth.passwords.members');

        $key = config('app.key');

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        $connection = $config['connection'] ?? null;

        return new self(
            DB::connection($connection),
            app('hash'),
            $config['table'],
            $key,
            $config['expire']
        );
    }
}
