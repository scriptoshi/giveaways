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
        Schema::create('nfts', function (Blueprint $table) { 
            $table->bigIncrements('id');
            $table->foreignId('giveaway_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('quest_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->string('symbol');
            $table->string('contract');
            $table->string('chainId');
            $table->string('hash')->nullable();
            $table->json('meta')->nullable();
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
        Schema::drop('nfts');
    }
};
