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
        Schema::create('questers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('giveaway_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->uuid('uuid')->unique()->nullable();
            $table->decimal('percent');
            $table->decimal('sleep', 24, 8);
            $table->string('qid');
            $table->string('address');
            $table->integer('pump')->default(1);
            $table->string('status');
            $table->string('comment')->nullable();
            $table->string('signature')->nullable();
            $table->json('claim')->nullable();
            $table->string('sleep_signature')->nullable();
            $table->string('sleep_hash')->nullable();
            $table->json('sleep_claim')->nullable();
            $table->timestamp('sleep_claimed_at')->nullable();
            $table->timestamp('boosted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_pump_at')->nullable();
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
        Schema::drop('questers');
    }
};
