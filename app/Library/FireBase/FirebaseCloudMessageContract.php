<?php

namespace App\Library\FireBase;

use App\Library\FireBase\Contracts\TranslatableToCloudMessage;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging\MulticastSendReport;
use Kreait\Firebase\Messaging\RegistrationToken;
use Kreait\Firebase\Messaging\RegistrationTokens;

interface FirebaseCloudMessageContract
{
    /**
     * @param  RegistrationTokens|RegistrationToken[]|string[] $registrationTokens
     * @param  bool                                            $validateOnly
     * @throws MessagingException
     * @throws FirebaseException
     * @return MulticastSendReport
     */
    public function sendMulticast($registrationTokens, bool $validateOnly = false): ?MulticastSendReport;

    /**
     * @param $token
     * @throws MessagingException
     * @throws FirebaseException
     * @return array
     */
    public function send($token): array;

    public function setMessageFromFcmNotifiable(TranslatableToCloudMessage $notifiable): self;
}
