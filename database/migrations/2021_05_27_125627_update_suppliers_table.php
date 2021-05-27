<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('swift_code');
            $table->dropColumn('purpose');
            $table->bigInteger('bank_id')->unsigned()->nullable();
            $table->foreign('bank_id')->references('id')->on('banks');
            $table->bigInteger('purpose_id')->unsigned()->nullable();
            $table->foreign('purpose_id')->references('id')->on('purposes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('swift_code');
            $table->string('purpose');
            $table->dropForeign(['bank_id']);
            $table->dropForeign(['purpose_id']);
            $table->dropColumn('bank_id');
            $table->dropColumn('purpose_id');
        });
    }
}
