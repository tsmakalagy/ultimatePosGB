<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaidOnColumnToPackageTransactionPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_transaction_payments', function (Blueprint $table) {
            $table->dateTime('paid_on')->nullable()->after('method');
            $table->integer('created_by')->nullable()->after('paid_on');
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
