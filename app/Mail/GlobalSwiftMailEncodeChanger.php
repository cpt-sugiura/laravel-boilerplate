<?php

namespace App\Mail;

use Swift_DependencyContainer;
use Swift_Preferences;

class GlobalSwiftMailEncodeChanger
{
    public const UTF8      = 'utf-8';
    public const ISO2022JP = 'iso-2022-jp';
    // 今どちらにセットされているかの目印。デフォルトは UTF8
    // ここの外部で設定されるとまったくあてにならなくなるので気休め
    protected static string $charset = self::UTF8;

    public static function getCurrentCharset(): string
    {
        return static::$charset;
    }

    /**
     * Laravel 内で送るメール（Swift Mailer で送られるメール）を ISO-2022-JP 形式に変更
     * @see https://swiftmailer.symfony.com/docs/japanese.html
     */
    public static function toIso2022Jp(): void
    {
        self::$charset = self::ISO2022JP;

        Swift_DependencyContainer::getInstance()
            ->register('mime.qpheaderencoder')
            ->asAliasOf('mime.base64headerencoder');

        Swift_Preferences::getInstance()->setCharset('iso-2022-jp');
    }

    /**
     * Laravel 内で送るメール（Swift Mailer で送られるメール）を UTF-8 形式に変更
     */
    public static function toUtf8(): void
    {
        self::$charset = self::UTF8;
        // ↑の toIso2022Jp メソッドで変更されるところを元々 UTF-8 で送れていた状態に復元する処理
        // 文字コード UTF-8 形式のメールを Quoted Printable 形式でエンコードする様に設定？いまいちよくわかっていない
        Swift_DependencyContainer::getInstance()
            ->register('mime.qpheaderencoder')
            ->addConstructorLookup('mime.charstream')
            ->asNewInstanceOf(\Swift_Mime_HeaderEncoder_QpHeaderEncoder::class);

        Swift_Preferences::getInstance()->setCharset('utf-8');
    }
}
