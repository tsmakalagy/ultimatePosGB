<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShipperTypeIdColumnsToShippersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shippers', function (Blueprint $table) {
            $table->unsignedInteger('shipper_type_id')->after('tel')->nullable();
            $table->foreign('shipper_type_id')->references('id')->on('shipper_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shippers', function (Blueprint $table) {
            //
        });
    }
}
