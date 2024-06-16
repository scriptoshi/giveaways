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
        Schema::table('questers', function (Blueprint $table) {
            //
            $table->decimal('percent')->default(0)->change();
            $table->decimal('gas', 24, 8)->default(0)->change();;
            $table->string('qid')->nullable()->change();
            $table->string('address')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questers', function (Blueprint $table) {
            //
        });
    }
};
