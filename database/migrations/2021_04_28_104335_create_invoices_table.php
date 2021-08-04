<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('supplier_id')->unsigned();
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreignId('currency_id')->nullable();
            $table->date('date');
            $table->string('invoice_number');
            $table->decimal('total', 15, 2);
            $table->decimal('amount_due', 15, 2);
            $table->decimal('amount_paid', 15, 2);
            $table->text('description')->nullable();
            $table->string('status')->nullable();
            $table->string('paid_by')->nullable();
            $table->foreignId('company_owner_id')->nullable();
            $table->foreignId('company_id');
            $table->string('xero_invoice_id')->nullable();
            $table->boolean('paid')->default(false);
            $table->boolean('archived')->default(false);
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
        Schema::dropIfExists('invoices');
    }
}
