<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBusinessLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `business_locations` MODIFY `state` VARCHAR(100) NULL;');
        DB::statement('ALTER TABLE `business_locations` MODIFY `zip_code` CHAR(7) NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `business_locations` MODIFY `state` VARCHAR(100) NOT NULL;');
        DB::statement('ALTER TABLE `business_locations` MODIFY `zip_code` CHAR(7) NOT NULL;');
    }
}
