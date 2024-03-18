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
        Schema::create('coins', function (Blueprint $table) { 
            $table->bigIncrements('id');
            $table->foreignId('chain_id')->constrained('chains')->onUpdate('cascade')->onDelete('cascade');
            $table->uuid('uuid');
            $table->string('name');
            $table->string('chainId');
            $table->string('logo_uri',600)->nullable();
            $table->string('symbol');
            $table->string('contract')->nullable();
            $table->integer('decimals')->default(18);
            $table->decimal('usd_rate',32,8)->default(0);
            $table->boolean('is_native')->default(false);
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
        Schema::drop('coins');
    }
};
