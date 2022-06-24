<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('the_packages', function (Blueprint $table) {
            $table->increments('id');       
            $table->text('bar_code');
            $table->unsignedInteger('package_id');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->decimal('volume',20,2);
            $table->decimal('longeur',20,2);
            $table->decimal('largeur',20,2);
            $table->decimal('hauteur',20,2);
            $table->string('customer_name');
            $table->string('customer_tel');
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
        Schema::dropIfExists('the_packages');
    }
}
