<?php

namespace App\Models\Eloquents;

use App\Models\Eloquents\BaseEloquent as Model;
use Illuminate\Support\Carbon;

/**
 * @property int    mail_log_id
 * @property string subject
 * @property string message_id
 * @property Carbon $send_at
 * @property string from
 * @property string to
 * @property string content_type
 * @property string charset
 * @property string return_path
 * @property string mime_version
 * @property string content_transfer_encoding
 * @property string received
 * @property string headers
 * @property string content
 * @property string storage_path
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class MailLog extends Model
{
    public $table = 'mail_logs';

    protected $primaryKey = 'mail_log_id';

    public $fillable = [
    ];

    protected $casts =[
        'send_at' => 'datetime'
    ];
}
