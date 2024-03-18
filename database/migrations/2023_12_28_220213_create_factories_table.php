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
        Schema::create('factories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('chain_id')->constrained('chains')->onUpdate('cascade')->onDelete('cascade');
            $table->string('version')->default('v1');
            $table->string('factory_version')->default('v1');
            $table->string('chainId');
            $table->string('foundry');
            $table->string('contract')->nullable();
            $table->string('type');
            $table->longText('factory_abi')->nullable();
            $table->longText('abi')->nullable();
            $table->boolean('active')->default(true);
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
        Schema::drop('factories');
    }
};
