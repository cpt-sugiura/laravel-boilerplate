<?php

namespace App\UseCase\MemberAuth\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * パスワードリセット通知方法の定義クラス
 */
class MemberPasswordResetNotification extends Notification
{
    use Queueable;

    /** @var string パスワードリセット用トークン */
    public string $token;
    /** @var string|null パスワードリセット用名前 */
    public ?string $name;
    /** @var Carbon パスワードリセットURL期限 */
    public Carbon $expiredAt;
    /** @var string メール題名 */
    protected string $title = 'パスワード再発行のご案内';

    /**
     * 初期化。
     * @param string      $token
     * @param Carbon      $expiredAt
     * @param string|null $name
     */
    public function __construct(string $token, Carbon $expiredAt, $name = null)
    {
        $this->token     = $token;
        $this->expiredAt = $expiredAt;
        $this->name      = $name;
    }

    /**
     * 通知の配信方式を決定。
     * @return array
     */
    public function via(): array
    {
        return ['mail'];
    }

    /**
     * パスワードリセットリンク通知メールを送信。
     * @return MailMessage
     */
    public function toMail(): MailMessage
    {
        return (new MailMessage())
            ->subject($this->title)
            ->view(
                'member.auth.password_reset_mail',
                [
                    'name'         => $this->name ?? '',
                    'resetUrl'     => route('member.auth.password.reset', ['token' => $this->token]),
                    'expiredAtStr' => $this->expiredAt->format('Y-m-d H:i:s'),
                ]
            );
    }
}
