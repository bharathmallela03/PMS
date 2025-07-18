<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSetupTokenToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // In the migration file
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('setup_token')->nullable();
        $table->timestamp('password_set_at')->nullable();
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
            //
        });
    }
}
