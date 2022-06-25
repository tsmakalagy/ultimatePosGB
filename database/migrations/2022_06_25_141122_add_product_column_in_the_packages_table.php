<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProductColumnInThePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('the_packages', function (Blueprint $table) {
            $table->text('product')->after('bar_code');
            $table->string('other_field1')->nullable()->change();
            $table->string('other_field2')->nullable()->change();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('the_packages', function (Blueprint $table) {
            //
        });
    }
}
