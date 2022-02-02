<?php

namespace App\Library\FireBase;

use App\Library\FireBase\Contracts\TranslatableToCloudMessage;
use Kreait\Firebase\Messaging\MulticastSendReport;
use Kreait\Firebase\Messaging\RegistrationToken;
use Kreait\Firebase\Messaging\RegistrationTokens;
use Log;

/**
 * FireBaseの認証情報なしに動かすためのモック
 * Class FirebaseCloudMessageMock
 * @package App\Library\FireBase
 */
class FirebaseCloudMessageMock implements FirebaseCloudMessageContract
{
    /**
     * @param  RegistrationToken[]|RegistrationTokens|string[]  $registrationTokens
     * @param  bool                                             $validateOnly
     * @return MulticastSendReport|null
     */
    public function sendMulticast(array|RegistrationTokens $registrationTokens, bool $validateOnly = false): ?MulticastSendReport
    {
        Log::info('called mock FCM');

        return MulticastSendReport::withItems([]);
    }

    /**
     * @param $token
     * @return array
     */
    public function send($token): array
    {
        return [];
    }

    public function setMessageFromFcmNotifiable(TranslatableToCloudMessage $notifiable): self
    {
        return $this;
    }
}
