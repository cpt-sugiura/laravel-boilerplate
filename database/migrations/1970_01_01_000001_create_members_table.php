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
        Schema::create('members', function (Blueprint $table) {
            $table->bigIncrements('member_id');
            $table->string('name')->comment('名前');
            $table->integer('gender')->comment('性別');
            $table->date('birthday')->comment('生年月日');
            $table->string('email')->comment('メールアドレス')->index(true);
            $table->string('password')->comment('パスワード');
            $table->integer('status')->comment('ステータス');
            $table->string('auth_token')->comment('認証用トークン。ログイン時限定APIでこれが送られてない時は弾く')->unique(true)->nullable(true)->default();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE members COMMENT '会員'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
    }
};
