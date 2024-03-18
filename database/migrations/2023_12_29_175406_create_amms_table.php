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
        Schema::create('amms', function (Blueprint $table) { 
            $table->bigIncrements('id');
            $table->foreignId('chain_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('chainId');
            $table->string('name');
            $table->string('url')->nullable();
            $table->string('info_url')->nullable();
            $table->string('token_url')->nullable();
            $table->string('logo_uri')->nullable();
            $table->string('pair_url')->nullable();
            $table->string('router');
            $table->string('factory');
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
        Schema::drop('amms');
    }
};
