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
        Schema::create('topups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('giveaway_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('paid', 12, 4)->nullable()->default(0);
            $table->decimal('paid_before', 12, 4)->nullable()->default(0);
            $table->decimal('prize_before', 12, 4)->nullable()->default(0);
            $table->decimal('fee_before', 12, 4)->nullable()->default(0);
            $table->decimal('sleep_before', 24, 16)->nullable()->default(0);
            $table->string('hash')->unique()->nullable();
            $table->string('account')->nullable();
            $table->integer('num_winners')->nullable();
            $table->integer('num_winners_before')->nullable();
            $table->string('status')->default('unpaid');
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
        Schema::drop('topups');
    }
};
