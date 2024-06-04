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
        Schema::create('mints', function (Blueprint $table) { 
            $table->bigIncrements('id');
            $table->foreignId('nfts_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('owner')->nullable();
            $table->string('tokenId')->nullable();
            $table->string('txhash')->nullable();
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
        Schema::drop('mints');
    }
};
