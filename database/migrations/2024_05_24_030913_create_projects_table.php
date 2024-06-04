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
        Schema::create('projects', function (Blueprint $table) { 
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->uuid('uuid');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->integer('rank');
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
        Schema::drop('projects');
    }
};
