<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');       
            $table->text('bar_code');
            $table->string('product');
            $table->decimal('volume',20,2);
            $table->decimal('weight',20,2);
            $table->string('image')->nullable();
            $table->boolean('status')->default(0);
             $table->string('other_field1',20,2)->nullable();
            $table->string('other_field2',20,2)->nullable();
  
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
        Schema::dropIfExists('packages');
    }
}
