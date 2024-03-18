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
        Schema::create('tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('chain_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('factory_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('chainId');
            $table->string('logo_uri', 600)->nullable();
            $table->string('name');
            $table->string('symbol');
            $table->string('contract_name')->nullable();
            $table->integer('decimals')->default(18);
            $table->decimal('total_supply', 32, 8)->default(0);
            $table->string('contract')->nullable();
            $table->string('txhash')->nullable();
            $table->string('type');
            $table->boolean('status')->default(true);
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
        Schema::drop('tokens');
    }
};
