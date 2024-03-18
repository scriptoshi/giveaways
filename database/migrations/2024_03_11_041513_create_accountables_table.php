<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accountables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->morphs('accountable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accountables');
    }
};
