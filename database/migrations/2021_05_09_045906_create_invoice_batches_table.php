<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_name')->unique();
            $table->date('date');
            $table->decimal('total', 15, 2);
            $table->boolean('generated')->default(false);
            $table->string('xero_batch_payment_id')->nullable();
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
        Schema::dropIfExists('invoice_batches');
    }
}
