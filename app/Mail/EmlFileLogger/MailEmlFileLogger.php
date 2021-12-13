<?php

namespace App\Mail\EmlFileLogger;

use App\Models\Eloquents\MailLog;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\Message;

class MailEmlFileLogger
{
    /**
     * @param  MessageSending|MessageSent $event
     * @throws \Swift_IoException
     */
    public function handle(MessageSending | MessageSent $event): void
    {
        if (! $event->data || ! isset($event->data['message'])) {
            \Log::info('fire '.class_basename($event).'. but not exists data');

            return;
        }

        /** @var \Swift_Message|Message $message どちらのイベントでも共通 */
        $message = $event->data['message'];

        // ログファイルの出力先フルパスを呼び出す
        $logPath = $this->makeLogFilePath($message, $event);

        $this->saveAsEmlFile($logPath, $message);
        if ($event instanceof MessageSent) {
            $this->saveAsDatabaseRecord($message, $logPath);
        }

        // メールファイルとは別にログを残したり
        $subject = $message->getSubject();
        if ($event instanceof MessageSending) {
            \Log::info("メールを送信中。件名: {$subject}。ファイルパス: {$logPath}");
        } elseif ($event instanceof MessageSent) {
            \Log::info("メールを送信済。件名: {$subject}。ファイルパス: {$logPath}");
        }
    }

    /**
     * ログファイルの出力先パス
     * @param  \Swift_Message|Message     $message
     * @param  MessageSending|MessageSent $event
     * @return string
     */
    protected function makeLogFilePath(\Swift_Message | Message $message, MessageSending | MessageSent $event): string
    {
        // ファイルパスとして適切になる様に置換を行った件名 + メッセージオブジェクトハッシュ で程々探しやすく重複しにくいファイル名を作る
        $logNameBase = str_replace(str_split('\/:*?"><|'), '_', $message->getSubject())
            .'_'.spl_object_hash($message);
        // 送信前後でディレクトリを分ける
        // 後々 diff -qr コマンドなどで差分ファイルを見つけて探したり何なり。ディスク容量が気になるなら二重保存の代わりにファイル移動をつかうなど
        if ($event instanceof MessageSending) {
            $logPathBase = storage_path('logs/mail/send/').$logNameBase;
        } else {
            $logPathBase = storage_path('logs/mail/sent/').$logNameBase;
        }

        // ファイル名がダブった場合、連番をつける。
        $logPath     = $logPathBase.'.eml';
        $i           = 1;
        while (file_exists($logPath)) {
            $logPath = $logPathBase."_{$i}".'.eml';
            ++$i;
        }

        // 構築されたログファイルの出力先パスを返す
        return $logPath;
    }

    /**
     * @param  string                 $logPath
     * @param  Message|\Swift_Message $message
     * @throws \Swift_IoException
     */
    protected function saveAsEmlFile(string $logPath, Message | \Swift_Message $message): void
    {
        // Swift_Message の中身をログファイルを対象に出力させる
        $stream = new \Swift_ByteStream_FileByteStream($logPath, true);
        $message->toByteStream($stream);
    }

    /**
     * @param Message|\Swift_Message $message
     * @param string                 $logPath
     */
    protected function saveAsDatabaseRecord(Message | \Swift_Message $message, string $logPath): void
    {
        $from = $message->getFrom();
        if (is_array($from)) {
            $from = implode(', ', array_keys($from));
        }
        $to = $message->getTo();
        if (is_array($to)) {
            $to = implode(', ', array_keys($to));
        }

        $log                               = new MailLog();
        $log->subject                      = $message->getSubject() ?? '';
        $log->message_id                   = $message->getSwiftMessage()->getId() ?? '';
        $log->send_at                      = $message->getDate()->format('Y-m-d H:i:s') ?? '';
        $log->from                         = $from ?? '';
        $log->to                           = $to ?? '';
        $log->content_type                 = $message->getContentType() ?? '';
        $log->charset                      = $message->getCharset() ?? '';
        $log->return_path                  = $message->getReturnPath() ?? '';
        $log->mime_version                 = optional($message->getHeaders()->get('mime-version'))->toString() ?? '';
        $log->content_transfer_encoding    = optional($message->getHeaders()->get('content-transfer-encoding'))->toString() ?? '';
        $log->received                     = optional($message->getHeaders()->get('received'))->toString() ?? '';
        $log->headers                      = $message->getHeaders()->toString() ?? '';
        $log->content                      = $message->getBody() ?? '';
        $log->storage_path                 = str_replace(storage_path(''), '', $logPath);
        $log->save();
    }
}
