<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_transactions', function (Blueprint $table) {
     $table->increments('id');
            // $table->integer('business_id')->unsigned();
            // $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->string('type')->nullable();
            $table->enum('status', ['received', 'pending', 'ordered', 'draft', 'final']);
            $table->enum('payment_status', ['paid', 'due']);
            // $table->integer('contact_id')->unsigned();
            // $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->string('invoice_no')->nullable();
            $table->string('ref_no')->nullable();
            $table->dateTime('transaction_date');
            $table->decimal('total_before_tax', 22, 4)->default(0)->comment('Total before the purchase/invoice tax, this includeds the indivisual product tax');
            // $table->integer('tax_id')->unsigned()->nullable();
            // $table->foreign('tax_id')->references('id')->on('tax_rates')->onDelete('cascade');
            $table->decimal('tax_amount', 22, 4)->default(0);
            // $table->enum('discount_type', ['fixed', 'percentage'])->nullable();
            $table->decimal('discount_amount', 22, 4)->default(0);
            // $table->string('shipping_details')->nullable();
            // $table->decimal('shipping_charges', 22, 4)->default(0);
            // $table->text('additional_notes')->nullable();
            // $table->text('staff_note')->nullable();
            $table->decimal('final_total', 22, 4)->default(0);
            $table->integer('created_by')->unsigned()->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->string('customer_name')->nullable();
            $table->string('customer_tel')->nullable();
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
        Schema::dropIfExists('package_transactions');
    }
}
