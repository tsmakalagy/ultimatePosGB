<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeOtherColumnToPackageTransactionPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_transaction_payments', function (Blueprint $table) {
              $table->boolean('is_advance')->default(0)->after('created_by');
            $table->boolean('paid_through_link')->default(0)->after('created_by');
            $table->string('gateway')->nullable()->after('paid_through_link');
            $table->string('document')->nullable()->after('note');
            $table->string('payment_ref_no')->nullable()->after('note');
            $table->integer('account_id')->nullable()->after('payment_ref_no');
            $table->string('transaction_no')->nullable()->after('method');
            $table->integer('payment_for')->after('created_by')->nullable();
            $table->integer('parent_id')->after('payment_for')->nullable();
            

            $table->boolean('is_return')->after('transaction_id')->default(false)->comment('Used during sales to return the change');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package_transaction_payments', function (Blueprint $table) {
            //
        });
    }
}
