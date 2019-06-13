<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ModifyBillProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills_project', function (Blueprint $table) {
            $table->dropColumn('date');
            $table->string('year')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bills_project', function ($table) {
            $table->date('date')->nullable();
            $table->dropColumn('year');
        });
    }
}
