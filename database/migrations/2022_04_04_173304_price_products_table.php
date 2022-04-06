<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PriceProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table->increments('id');       
            $table->string('name');
            $table->string('specifications',70);
            $table->decimal('china_price',20,2);
            $table->string('kuaidi',70);
            $table->integer('dimension');
            $table->integer('volume');
            $table->text('lien');
            $table->timestamps();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
