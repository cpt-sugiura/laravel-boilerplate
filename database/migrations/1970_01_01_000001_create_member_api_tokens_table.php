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
        Schema::create('member_api_tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('token')->comment('APIトークン. これ抜きでAPIが叩かれたら通信拒否');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE member_api_tokens COMMENT '会員用APIトークン'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_api_tokens');
    }
};
