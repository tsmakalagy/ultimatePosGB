<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeColumnsToPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->decimal('longueur',20,2)->after('weight');
            $table->decimal('largeur',20,2)->after('weight');
            $table->decimal('hauteur',20,2)->after('weight');
            $table->string('customer_name')->after('weight');
            $table->string('customer_tel')->after('weight');
            $table->text('product')->nullable()->change();
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            //
        });
    }
}
