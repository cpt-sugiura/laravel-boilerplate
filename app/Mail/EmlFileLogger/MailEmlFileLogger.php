<?php

namespace App\Mail\EmlFileLogger;

use App\Models\Eloquents\MailLog;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\Message;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Header\AbstractHeader;

class MailEmlFileLogger
{
    /**
     * @param  MessageSending|MessageSent  $event
     */
    public function handle(MessageSending|MessageSent $event): void
    {
        if(!$event->data || !isset($event->data['message'])) {
            \Log::info('fire ' . class_basename($event) . '. but not exists data');

            return;
        }

        /** @var Message $message どちらのイベントでも共通 */
        $message = $event->data['message'];

        // ログファイルの出力先フルパスを呼び出す
        $logPath = $this->makeLogFilePath($message, $event);

        $this->saveAsEmlFile($logPath, $message);
//        if($event instanceof MessageSent) {
        $this->saveAsDatabaseRecord($message, $logPath);
//        }

        // メールファイルとは別にログを残したり
        $subject = $message->getSubject();
        if($event instanceof MessageSending) {
            \Log::info("メールを送信中。件名: {$subject}。ファイルパス: {$logPath}");
        } elseif($event instanceof MessageSent) {
            \Log::info("メールを送信済。件名: {$subject}。ファイルパス: {$logPath}");
        }
    }

    /**
     * ログファイルの出力先パス
     * @param  Message                     $message
     * @param  MessageSending|MessageSent  $event
     * @return string
     */
    protected function makeLogFilePath(Message $message, MessageSending|MessageSent $event): string
    {
        // ファイルパスとして適切になる様に置換を行った件名 + メッセージオブジェクトハッシュ で程々探しやすく重複しにくいファイル名を作る
        $logNameBase = str_replace(str_split('\/:*?"><|'), '_', $message->getSubject())
            . '_' . spl_object_hash($message);
        // 送信前後でディレクトリを分ける
        // 後々 diff -qr コマンドなどで差分ファイルを見つけて探したり何なり。ディスク容量が気になるなら二重保存の代わりにファイル移動をつかうなど
        if($event instanceof MessageSending) {
            $logPathBase = storage_path('logs/mail/send/') . $logNameBase;
        } else {
            $logPathBase = storage_path('logs/mail/sent/') . $logNameBase;
        }

        // ファイル名がダブった場合、連番をつける。
        $logPath = $logPathBase . '.eml';
        $i       = 1;
        while(file_exists($logPath)) {
            $logPath = $logPathBase . "_{$i}" . '.eml';
            ++$i;
        }

        // 構築されたログファイルの出力先パスを返す
        return $logPath;
    }

    /**
     * @param  string   $logPath
     * @param  Message  $message
     */
    protected function saveAsEmlFile(string $logPath, Message $message): void
    {
        file_put_contents($logPath, $message->getSymfonyMessage()->toString());
    }

    /**
     * @param  Message  $message
     * @param  string   $logPath
     */
    protected function saveAsDatabaseRecord(Message $message, string $logPath): void
    {
        $from = implode(', ', array_map(static fn(Address $address) => $address->toString(), $message->getFrom()));
        $to   = implode(', ', array_map(static fn(Address $address) => $address->toString(), $message->getTo()));

        $headersArr = [];
        foreach($message->getSymfonyMessage()->getPreparedHeaders()->all() as $header) {
            preg_match('/(.*?): (.*)/', $header->toString(), $matches);
            if(count($matches) === 3) {
                $headersArr[$matches[1]] = $matches[2];
            }
        }
        foreach($message->getBody()->getPreparedHeaders()->all() as $header) {
            // todo params header の考慮これでよいか検証
            preg_match('/(.*?): (.*?); .*/', $header->toString(), $matches);
            if(count($matches) === 3) {
                $headersArr[$matches[1]] = $matches[2];
            }
            preg_match('/.*charset=(.*)/', $header->toString(), $matches);
            if(count($matches) === 2) {
                $headersArr['charset'] = $matches[1];
            }
        }
        $log                            = new MailLog();
        $log->subject                   = $message->getSubject() ?? '';
        $log->message_id                = $headersArr['Message-ID'] ?? '';
        $log->send_at                   = $message->getDate()?->format('Y-m-d H:i:s') ?? '';
        $log->from                      = $from ?? '';
        $log->to                        = $to ?? '';
        $log->content_type              = $headersArr['Content-Type'] ?? '';
        $log->charset                   = $headersArr['charset'] ?? '';
        $log->return_path               = $message->getReturnPath()?->toString() ?? '';
        $log->mime_version              = $headersArr['MIME-Version'] ?? '';
        $log->content_transfer_encoding = $headersArr['Content-Transfer-Encoding'] ?? '';
        $log->received                  = $message->getHeaders()->getHeaderBody('received') ?? '';
        $log->headers                   = $message->getHeaders()->toString() ?? '';
        $log->content                   = $message->getBody()->bodyToString() ?? '';
        $log->storage_path              = str_replace(storage_path(''), '', $logPath);
        $log->save();
    }
}
