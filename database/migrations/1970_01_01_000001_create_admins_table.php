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
        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('admin_id');
            $table->string('name')->comment('名前');
            $table->string('email')->comment('メールアドレス')->index(true);
            $table->string('password')->comment('パスワード');
            $table->string('remember_token')->comment('継続ログイン用トークン');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE admins COMMENT '管理者'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
};
