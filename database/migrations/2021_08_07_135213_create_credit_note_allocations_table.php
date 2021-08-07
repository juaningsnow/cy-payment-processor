<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditNoteAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_note_allocations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('credit_note_id')->unsigned();
            $table->foreign('credit_note_id')->references('id')->on('credit_notes');
            $table->bigInteger('invoice_id')->nullable()->unsigned();
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credit_note_allocations');
    }
}
