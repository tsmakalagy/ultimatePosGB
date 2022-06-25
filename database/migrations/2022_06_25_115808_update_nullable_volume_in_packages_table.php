<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateNullableVolumeInPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
             $table->decimal('volume',20,2)->nullable()->change();
            $table->decimal('longeur',20,2)->nullable()->change();
            $table->decimal('largeur',20,2)->nullable()->change();
            $table->decimal('hauteur',20,2)->nullable()->change();
            $table->decimal('weight',20,2)->nullable()->change();
            $table->boolean('status')->default(0)->nullable()->change();
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
