<?php

namespace App\Library\FireBase\Contracts;

interface TranslatableToCloudMessage
{
    public function fcmTitle(): string;

    public function fcmMessage(): string;

    /**
     * @return string in_array($ret, Notify::NOTIFY_CATEGORIES)
     */
    public function fcmNotifyCategory(): string;

    /**
     * @return int|string member/{ret} で会員詳細APIの様なID
     */
    public function fcmIdOnUrl(): int|string;
}
