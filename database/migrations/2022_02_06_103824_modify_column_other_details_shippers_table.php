<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyColumnOtherDetailsShippersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement('ALTER TABLE `shippers` MODIFY `other_details` VARCHAR(128) NULL;');
        DB::statement('ALTER TABLE `shippers` MODIFY `type` VARCHAR(128) NULL;');
        DB::statement('ALTER TABLE `shippers` MODIFY `shipper_name` VARCHAR(128) NOT NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        DB::statement('ALTER TABLE `shippers` MODIFY `other_details` VARCHAR(128) NOT NULL;');
    }
}
