<?php

namespace App\Models\Eloquents\Member\Concerns;

use App\Library\Notifications\MemberPasswordResetNotification;
use Auth;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Auth\TokenGuard;
use Illuminate\Support\Str;

trait MemberAuth
{
    /**
     * 使っている認証用プロバイダを返す
     * @return EloquentUserProvider
     */
    public static function getAuthProvider(): EloquentUserProvider
    {
        /** @var TokenGuard $guard */
        $guard = Auth::guard('api');
        /** @var EloquentUserProvider $provider */
        $provider = $guard->getProvider();

        return $provider;
    }

    /**
     * 会員に紐づいた認証機能からパスワードハッシュを生成
     * @param  string $password
     * @return string
     */
    public static function makePassword(string $password): string
    {
        return self::getAuthProvider()->getHasher()->make($password);
    }

    /**
     * @return string APIトークンを作成
     */
    public static function makeApiToken(): string
    {
        return Str::random(static::AUTH_TOKEN_LENGTH);
    }

    /**
     * APIトークンを生成して、それを保存
     * @return string
     */
    public function resetApiToken(): string
    {
        $token = self::makeApiToken();

        $this->forceFill([static::AUTH_TOKEN_NAME => $token])->save();

        return $token;
    }

    /**
     * パスワードリセットメール送信先メールアドレス.
     *
     * @return string
     */
    public function getEmailForPasswordReset(): string
    {
        return $this->email;
    }

    /**
     * パスワードリセット通知の送信
     * @param  string $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(
            new MemberPasswordResetNotification(
                $token,
                now()->addMinutes(config('auth.passwords.users.expire')),
                $this->name
            )
        );
    }

    /**
     * ログイン済みだが使えないユーザを弾いたりなどしています。絶対にbool値を返すメソッドとして維持してください。
     * @return bool
     */
    public function canUseAccount(): bool
    {
        return $this->status === self::STATUS_ENABLE;
    }
}
