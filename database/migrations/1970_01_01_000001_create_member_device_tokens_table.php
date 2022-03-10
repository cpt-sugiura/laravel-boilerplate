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
        Schema::create('member_device_tokens', function (Blueprint $table) {
            $table->bigIncrements('member_device_token_id');
            $table->unsignedBigInteger('member_id');
            $table->string('device_token')->comment('デバイストークン');
            $table->integer('some_notify_control')->comment('何か通知制御');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE member_device_tokens COMMENT '会員用デバイストークン'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_device_tokens');
    }
};
