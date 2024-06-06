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
            $table->decimal('paid', 12, 4)->after('prize')->default(0);
            $table->decimal('prize', 12, 4)->default(0)->change();
            $table->decimal('fee', 12, 4)->default(0)->nullable()->change();
            $table->decimal('sleep', 24, 8)->default(0)->nullable()->change();
            $table->decimal('sleep_balance', 24, 8)->default(0)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('giveaways', function (Blueprint $table) {
            //
            $table->dropColumn(['paid', 'is_topup']);
            $table->decimal('prize')->change();
            $table->decimal('fee')->change();
            $table->decimal('sleep')->change();
            $table->decimal('sleep_balance')->change();
        });
    }
};
