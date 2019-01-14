<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToHeartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hearts', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('have_read')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hearts', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('have_read');
        });
    }
}
