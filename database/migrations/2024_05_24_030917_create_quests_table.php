<?php

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
        Schema::create('quests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('giveaway_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('connection_id')->nullable()->constrained();
            $table->string('username')->nullable();
            $table->string('groupId')->nullable();
            $table->string('type')->nullable()->default('twitter');
            $table->string('status')->nullable()->default('pending');
            $table->decimal('min', 16, 8)->default(0);
            $table->integer('sleep')->default(1);
            $table->json('data')->nullable()->default(NULL);
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
        Schema::drop('quests');
    }
};
