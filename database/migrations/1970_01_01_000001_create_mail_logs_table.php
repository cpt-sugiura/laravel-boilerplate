<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_logs', function (Blueprint $table) {
            $table->bigIncrements('mail_log_id');
            $table->string('subject')->comment('件名');
            $table->string('message_id')->comment('メール自体の持つメールとしてのID');
            $table->string('return_path')->comment('return-pathとされているメールアドレス');
            $table->string('from')->comment('差出人');
            $table->string('to')->comment('宛先');
            $table->string('content_type')->comment('形式');
            $table->string('charset')->comment('使用文字コード');
            $table->string('mime_version')->comment('MIMEバージョン');
            $table->string('content_transfer_encoding')->comment('使用変換形式');
            $table->string('received')->comment('経路');
            $table->text('headers')->comment('ヘッダー全体');
            $table->text('content')->comment('本文');
            $table->string('storage_path')->comment('/storageをルートにしたルート相対パス。storage_path(これ)でemlファイルにたどり着ける');
            $table->timestamp('send_at')->nullable(true)->comment('送信日時');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE mail_logs COMMENT 'メールログ'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mail_logs');
    }
};
