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
        Schema::create('boost_quester', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boost_id')->nullable()->constrained('questers');
            $table->foreignId('quester_id')->nullable()->constrained('questers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boost_quester');
    }
};
