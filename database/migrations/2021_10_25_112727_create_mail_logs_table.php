<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('mail_logs', function (Blueprint $table) {
            $table->bigIncrements('mail_log_id');
            $table->string('subject');
            $table->string('message_id');
            $table->string('from');
            $table->string('to');
            $table->string('content_type');
            $table->string('charset');
            $table->string('return_path');
            $table->string('mime_version');
            $table->string('content_transfer_encoding');
            $table->string('received');
            $table->text('headers')->comment('ヘッダー全体');
            $table->longText('content');
            $table->string('storage_path')->comment('/storageをルートにしたルート相対パス。storage_path(これ)でemlファイルにたどり着ける');
            $table->timestamp('send_at')->nullable()->comment('送信日時');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_logs');
    }
}
