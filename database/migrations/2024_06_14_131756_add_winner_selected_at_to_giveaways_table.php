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
        Schema::table('giveaways', function (Blueprint $table) {
            //
            $table->timestamp('winner_selected_at')->after('ends_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('giveaways', function (Blueprint $table) {
            //
            $table->dropColumn(['winner_selected_at']);
        });
    }
};
