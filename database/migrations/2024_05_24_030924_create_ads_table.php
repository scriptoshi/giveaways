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
        Schema::create('ads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->uuid('uuid');
            $table->string('category');
            $table->json('tags')->nullable();
            $table->string('title');
            $table->string('slug')->nullable()->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2)->nullable()->default(0);
            $table->integer('duration')->nullable();
            $table->string('period');
            $table->integer('rating')->default(0);
            $table->string('telegram')->nullable();
            $table->string('url')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('promoted_at')->nullable();
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
        Schema::drop('ads');
    }
};
