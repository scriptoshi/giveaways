<?php

/** dev:
 *Stephen Isaac:  ofuzak@gmail.com.
 *Skype: ofuzak
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('connections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('userId');
            $table->integer('followers')->nullable()->default(0);
            $table->integer('following')->nullable()->default(0);
            $table->integer('tweets')->nullable()->default(0);
            $table->boolean('verified')->nullable()->default(false);
            $table->text('description')->nullable();
            $table->string('username')->nullable();
            $table->string('email')->nullable();
            $table->string('provider');
            $table->string('token')->nullable();
            $table->string('refreshToken')->nullable();
            $table->string('expiresIn')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('connections');
    }
};
