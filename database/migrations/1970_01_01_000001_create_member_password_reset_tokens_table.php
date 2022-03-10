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
        Schema::create('member_password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->index(true);
            $table->string('token');
            $table->timestamp('created_at')->nullable(true);
        });

        DB::statement("ALTER TABLE member_password_reset_tokens COMMENT '会員用パスワードリセットトークン'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_password_reset_tokens');
    }
};
