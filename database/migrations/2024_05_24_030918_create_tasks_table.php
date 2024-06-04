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
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('giveaway_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('quest_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('quester_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->uuid('uuid');
            $table->string('type');
            $table->string('response')->nullable();
            $table->string('status')->nullable()->default('pending');
            $table->decimal('sleep', 16, 8)->default(1);
            $table->boolean('validated')->default(false);
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
        Schema::drop('tasks');
    }
};
