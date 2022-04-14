<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->increments('id');       
            $table->string('product_name')->nullable();
            $table->string('product_spec')->nullable();
            $table->decimal('china_price',20,2)->nullable();
            $table->text('kuaidi')->nullable();
            $table->decimal('size',20,2)->nullable();
            $table->decimal('volume',20,2)->nullable();
            $table->decimal('weight',20,2)->nullable();
            $table->string('link')->nullable();
            $table->string('other_field1',20,2)->nullable();
            $table->string('suggested_price',20,2)->nullable();
            $table->decimal('byship_price',20,2)->nullable();
            $table->decimal('byplane_price',20,2)->nullable();

            $table->timestamps();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_prices');
    }
}
