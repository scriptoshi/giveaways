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
        Schema::create('launchpads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('coin_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('amm_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('factory_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('version')->default('v1');
            // contract data
            $table->decimal('listing_price', 26, 8);
            $table->decimal('presale_price', 26, 8);
            $table->decimal('softcap', 26, 8);
            $table->decimal('hardcap', 26, 8);
            $table->decimal('percent')->default(0);
            $table->decimal('min', 26, 8);
            $table->decimal('max', 26, 8);
            $table->decimal('presale_amount', 26, 8);
            $table->string('contract')->nullable();
            $table->string('base_token')->nullable();
            $table->string('chainId');
            $table->string('unsold_tokens');
            $table->string('type');
            $table->string('txhash')->nullable();
            $table->string('merkleRoot')->nullable();
            $table->string('pair')->nullable();
            $table->decimal('total', 26, 8)->default(0);
            $table->decimal('base_deposited', 26, 18, true)->nullable();
            $table->decimal('total_tokens', 26, 18, true)->nullable();
            $table->decimal('percentage', 26, 8)->default(0);
            $table->integer('lock_period')->default(0);
            $table->string('liquidity_percent')->default('0');
            $table->string('total_base')->default(0);
            $table->tinyInteger('status_code')->default(0);
            // local data
            $table->integer('participants')->default(0); // counts participants
            $table->string('status');
            // project;
            $table->string('name');
            $table->text('description');
            // token;
            $table->string('token_name');
            $table->string('token_contract');
            $table->string('token_decimals');
            $table->string('token_supply');
            $table->string('token_symbol');
            $table->string('logo_uri');
            // booleans;
            $table->boolean('is_finalized')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_force_failed')->default(false);
            $table->boolean('is_cancelled')->default(false);
            $table->boolean('burn')->default(false);
            // timestamps
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
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
        Schema::drop('launchpads');
    }
};
