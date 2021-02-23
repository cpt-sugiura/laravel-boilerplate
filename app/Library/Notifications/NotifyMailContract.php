<?php

namespace App\Library\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

interface NotifyMailContract
{
    /**
     * @return array 通信の配送方式. in_array('mail', returnArr) を期待
     */
    public function via(): array;

    public function toMail(): MailMessage;
}
