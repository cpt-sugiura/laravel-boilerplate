<?php

namespace App\Library\FireBase;

use App\Library\FireBase\Contracts\TranslatableToCloudMessage;
use Illuminate\Support\Str;
use JsonException;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\MessageData;
use Kreait\Firebase\Messaging\MulticastSendReport;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\RegistrationToken;
use Kreait\Firebase\Messaging\RegistrationTokens;
use Log;

class FirebaseCloudMessage implements FirebaseCloudMessageContract
{
    private Messaging $messaging;
    private CloudMessage $message;

    /**
     * FirebaseCloudMessage constructor.
     * @param Messaging|null $messaging
     */
    protected function __construct(Messaging $messaging = null)
    {
        $this->messaging = $messaging ?? (new Factory())->createMessaging();
    }

    public static function makeService(): FirebaseCloudMessageContract
    {
        if (config('firebase.use_mock') || ! config('firebase.projects.app.credentials.file')) {
            return new FirebaseCloudMessageMock();
        }

        return new self();
    }

    /**
     * @param  RegistrationToken[]|RegistrationTokens|string[]  $registrationTokens
     * @param  bool                                             $validateOnly
     * @return MulticastSendReport|null
     * @throws FirebaseException
     * @throws JsonException
     * @throws MessagingException
     */
    public function sendMulticast(array|RegistrationTokens $registrationTokens, bool $validateOnly = false): ?MulticastSendReport
    {
        if (count($registrationTokens) === 0) {
            return null;
        }

        $reports  = $this->messaging->sendMulticast($this->message, $registrationTokens, $validateOnly);
        $fcmLogId = Str::random();
        foreach ($reports->failures()->getItems() as $error) {
            Log::error('failed sent fcm', compact('fcmLogId'));
            Log::error($error->error());
        }
        foreach ($reports->successes()->getItems() as $success) {
            Log::info('success sent fcm', compact('fcmLogId'));
            Log::info(json_encode($success->target(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT), compact('fcmLogId'));
            Log::info(json_encode($success->result(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT), compact('fcmLogId'));
            Log::info(json_encode($success->message(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT), compact('fcmLogId'));
        }

        return $reports;
    }

    /**
     * @param $token
     * @throws FirebaseException
     * @throws MessagingException
     * @return array
     */
    public function send($token): array
    {
        return $this->messaging->send($this->message, $token);
    }

    public function setMessageFromFcmNotifiable(TranslatableToCloudMessage $notifiable): self
    {
        $this->message = CloudMessage::new()
            ->withNotification(Notification::create($notifiable->fcmTitle(), $notifiable->fcmMessage()))
            ->withApnsConfig(ApnsConfig::new()->withBadge(1))
            ->withData(
                MessageData::fromArray([
                    'category' => $notifiable->fcmNotifyCategory(),
                    'id'       => $notifiable->fcmIdOnUrl(),
                ])
            );

        return $this;
    }
}
