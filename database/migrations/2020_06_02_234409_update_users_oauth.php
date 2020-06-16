<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersOauth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('oauth_token')->nullable()->default(null);
            $table->text('oauth_refreshtoken')->nullable()->default(null);
            $table->timestamp('oauth_expires')->nullable()->default(null);
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
            $table->dropColumn('oauth_expires');
            $table->dropColumn('oauth_refreshtoken');
            $table->dropColumn('oauth_token');
        });
    }
}
