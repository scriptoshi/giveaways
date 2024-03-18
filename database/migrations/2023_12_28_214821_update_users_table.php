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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nonce')->unique()->nullable();
            $table->string('referral')->nullable();
            $table->timestamp('last_seen_at')->nullable();
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nonce');
            $table->dropColumn('referral');
            $table->dropColumn('use_multiple_accounts');
            $table->dropColumn('last_seen_at');
        });
    }
};
