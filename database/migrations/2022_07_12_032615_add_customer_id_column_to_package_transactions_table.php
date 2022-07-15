<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomerIdColumnToPackageTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_transactions', function (Blueprint $table) {
            $table->unsignedInteger('customer_id')->after('type')->nullable();
            $table->foreign('customer_id')->references('id')->on('contacts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package_transactions', function (Blueprint $table) {
            //
        });
    }
}
