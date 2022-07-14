<?php

namespace App\Mail\SmtpChangeableEncodingTransport;

use Symfony\Component\Mailer\Transport\AbstractTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;
use Symfony\Component\Mailer\Transport\TransportInterface;

/**
 * EsmtpTransportFactory 内で new されている Transport クラスを代えたのみで、ロジックは完全に同じです
 * @see \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory
 */
final class SmtpChangeableEncodingTransportFactory extends AbstractTransportFactory
{
    public function create(Dsn $dsn): TransportInterface
    {
        $tls  = 'smtps' === $dsn->getScheme() ? true : null;
        $port = $dsn->getPort(0);
        $host = $dsn->getHost();

        // ここを esmtp から独自実装の SmtpChangeableEncodingTransport に代えただけ
        $transport = new SmtpChangeableEncodingTransport($host, $port, $tls, $this->dispatcher, $this->logger);

        if ('' !== $dsn->getOption('verify_peer') && ! filter_var($dsn->getOption('verify_peer', true), \FILTER_VALIDATE_BOOLEAN)) {
            /** @var SocketStream $stream */
            $stream        = $transport->getStream();
            $streamOptions = $stream->getStreamOptions();

            $streamOptions['ssl']['verify_peer']      = false;
            $streamOptions['ssl']['verify_peer_name'] = false;

            $stream->setStreamOptions($streamOptions);
        }

        if ($user = $dsn->getUser()) {
            $transport->setUsername($user);
        }

        if ($password = $dsn->getPassword()) {
            $transport->setPassword($password);
        }

        if (null !== ($localDomain = $dsn->getOption('local_domain'))) {
            $transport->setLocalDomain($localDomain);
        }

        if (null !== ($restartThreshold = $dsn->getOption('restart_threshold'))) {
            $transport->setRestartThreshold((int) $restartThreshold, (int) $dsn->getOption('restart_threshold_sleep', 0));
        }

        if (null !== ($pingThreshold = $dsn->getOption('ping_threshold'))) {
            $transport->setPingThreshold((int) $pingThreshold);
        }

        return $transport;
    }

    protected function getSupportedSchemes(): array
    {
        return ['smtp', 'smtps'];
    }
}
