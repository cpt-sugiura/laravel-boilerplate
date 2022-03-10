<?php

namespace App\Models\Eloquents;

use App\Models\Eloquents\BaseEloquent as Model;

class MailLog extends Model
{
    public $table = 'mail_logs';
    protected $primaryKey = 'mail_log_id';


    public $guarded = [
        'mail_log_id',
        'created_at',
        'updated_at',
    ];

    protected $casts =[
        'mail_log_id' => 'integer',
        'subject' => 'string',
        'message_id' => 'string',
        'return_path' => 'string',
        'from' => 'string',
        'to' => 'string',
        'content_type' => 'string',
        'charset' => 'string',
        'mime_version' => 'string',
        'content_transfer_encoding' => 'string',
        'received' => 'string',
        'headers' => 'string',
        'content' => 'string',
        'storage_path' => 'string',
        'send_at' => 'date',
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

}
