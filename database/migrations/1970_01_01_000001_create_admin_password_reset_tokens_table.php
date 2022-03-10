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
        Schema::create('admin_password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->index(true);
            $table->string('token');
            $table->timestamp('created_at')->nullable(true);
        });

        DB::statement("ALTER TABLE admin_password_reset_tokens COMMENT '管理者用パスワードリセットトークン'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_password_reset_tokens');
    }
};
