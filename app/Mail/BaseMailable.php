<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

abstract class BaseMailable extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct()
    {
        $this->withSymfonyMessage(
            function (\Symfony\Component\Mime\Email $message) {
                $message->returnPath(config('mail.return-path'));
            }
        );
    }

    abstract public function build(): self;
}
