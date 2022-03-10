<?php

namespace App\UseCase\AdminBrowserAPI\Auth\Registered\Mail;

use App\Mail\BaseMailable;
use Carbon\Carbon;

/**
 * パスワードリセット通知方法の定義クラス
 */
class PasswordResetMail extends BaseMailable
{
    /** @var string パスワードリセット用トークン */
    public string $token;
    /** @var string パスワードリセット用名前 */
    public string $name;
    /** @var Carbon パスワードリセットURL期限 */
    public Carbon $expiredAt;

    /**
     * 初期化。
     * @param string $token
     * @param Carbon $expiredAt
     * @param string $name
     */
    public function __construct(string $token, Carbon $expiredAt, string $name = '')
    {
        parent::__construct();
        $this->token     = $token;
        $this->expiredAt = $expiredAt;
        $this->name      = $name;
    }

    public function build(): BaseMailable
    {
        return $this
            ->subject(trans('passwords.accounts.mail.subject'))
            ->view(
                'mail.accounts.password_reset_mail',
                [
                    'name'         => $this->name,
                    'resetUrl'     => route('auth.password_reset', $this->token),
                    'expiredAtStr' => $this->expiredAt->format('Y年m月d日 H:i:s'),
                ]
            );
    }
}
