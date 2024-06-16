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
        Schema::create('giveaways', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('project_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->uuid('uuid');
            $table->string('slug');
            $table->string('brief')->nullable();
            $table->integer('duration')->nullable();
            $table->string('period')->nullable()->default('hours');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->decimal('prize');
            $table->decimal('fee');
            $table->decimal('gas');
            $table->decimal('gas_balance');
            $table->string('symbol')->default('USDT');
            $table->string('hash')->nullable();
            $table->integer('chainId')->nullable();
            $table->string('status')->nullable()->default('unpaid');
            $table->string('errors')->nullable()->default(NULl);
            $table->string('account')->nullable()->default(NULl);
            $table->integer('num_winners')->nullable();
            $table->integer('num_claimed')->nullable();
            $table->string('type')->default('draw');
            $table->integer('draw_size')->default(100);
            $table->boolean('live')->default(false);
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
        Schema::drop('giveaways');
    }
};
