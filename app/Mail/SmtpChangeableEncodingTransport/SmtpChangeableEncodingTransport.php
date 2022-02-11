<?php

namespace App\Mail\SmtpChangeableEncodingTransport;

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
    public static MailCharset $charset = MailCharset::UTF_8;
    public static MailEncode $encode = MailEncode::QUOTED_PRINTABLE;

    public function send(RawMessage $message, Envelope $envelope = null): ?SentMessage
    {
        // Laravel9 の中で new Email しているので確実に Email が来る
        if($message instanceof Email && self::$charset === MailCharset::ISO_2022_JP) {
            /** @var \Closure $encodeFn メールアドレスをISO-2022-JP形式に変換するための関数。 array_map に使うために用意 */
            $encodeFn = static fn(Address $from) => new Address(
                $from->getAddress(),
                mb_encode_mimeheader($from->getName(), self::$charset->valueInMimeHeader(), self::$encode->valueInMimeHeader())
            );
            // ヘッダー各部を ISO-2022-JP にエンコード
            $message->subject(mb_encode_mimeheader($message->getSubject(), self::$charset->valueInMimeHeader(), self::$encode->valueInMimeHeader()));
            $message->from(...array_map($encodeFn, $message->getFrom()));
            $message->to(...array_map($encodeFn, $message->getTo()));
            $message->replyTo(...array_map($encodeFn, $message->getReplyTo()));
            $message->cc(...array_map($encodeFn, $message->getCc()));
            $message->bcc(...array_map($encodeFn, $message->getBcc()));

            // 本文を ISO-2022-JP にエンコード
            if($message->getHtmlBody() !== null) {
                $message->html(mb_convert_encoding($message->getHtmlBody(), self::$charset->valueInMimeHeader()), self::$charset->valueInContentType());
            } elseif($message->getTextBody() !== null) {
                $message->text(mb_convert_encoding($message->getTextBody(), self::$charset->valueInMimeHeader()), self::$charset->valueInContentType());
            }
        }
        // 本来の送信ロジックへパス
        return parent::send($message, $envelope);
    }
}
