<?php

namespace App\Mail\SmtpChangeableEncodingTransport;

use App\Mail\SmtpChangeableEncodingTransport\Mime\Header\UnstructuredHeaderForISO2022JP;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;

/**
 * ISO-2022-JP にエンコードする処理本体
 * 元々 Laravel が使うと定めた smtp メーラーで使っている transport を継承、
 * 継承元の send メソッドの前に ISO-2022-JP への変換処理をはさみこむことで
 * ISO-2022-JP への変換を実現
 */
class SmtpChangeableEncodingTransport extends EsmtpTransport
{
    public static string $charset  = MailCharset::UTF_8;
    public static string $encode   = MailEncode::QUOTED_PRINTABLE;

    public function send(RawMessage $message, Envelope $envelope = null): ?SentMessage
    {
        // Laravel9 の中で new Email しているので確実に Email が来る
        if ($message instanceof Email && self::$charset === MailCharset::ISO_2022_JP) {
            /** @var \Closure $encodeFn メールアドレスをISO-2022-JP形式に変換するための関数。 array_map に使うために用意 */
            $encodeFn = static fn (Address $from) => new Address(
                $from->getAddress(),
                mb_encode_mimeheader($from->getName(), mailCharset::valueInMimeHeader(self::$charset), MailEncode::valueInMimeHeader(self::$encode))
            );
            // ヘッダー各部を ISO-2022-JP にエンコード
            $subjectRaw = $message->getSubject();
            $headers    = $message->getHeaders();
            $headers->remove('subject');
            $headers->add(new UnstructuredHeaderForISO2022JP('Subject', $subjectRaw));

            // todo ここから↓が怪しい。
            $message->from(...array_map($encodeFn, $message->getFrom()));
            $message->to(...array_map($encodeFn, $message->getTo()));
            $message->replyTo(...array_map($encodeFn, $message->getReplyTo()));
            $message->cc(...array_map($encodeFn, $message->getCc()));
            $message->bcc(...array_map($encodeFn, $message->getBcc()));

            // 本文を ISO-2022-JP にエンコード
            if ($message->getHtmlBody() !== null) {
                $message->html(mb_convert_encoding($message->getHtmlBody(), mailCharset::valueInMimeHeader(self::$charset)), mailCharset::valueInContentType(self::$charset));
            } elseif ($message->getTextBody() !== null) {
                $message->text(mb_convert_encoding($message->getTextBody(), mailCharset::valueInMimeHeader(self::$charset)), mailCharset::valueInContentType(self::$charset));
            }
        }
        // 本来の送信ロジックへパス
        return parent::send($message, $envelope);
    }
}
