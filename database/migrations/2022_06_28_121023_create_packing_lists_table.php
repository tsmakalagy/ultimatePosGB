<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackingListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packing_lists', function (Blueprint $table) {
            $table->increments('id');       
            $table->text('bar_code')->nullable();
            $table->unsignedInteger('the_package_id')->nullable();
            $table->foreign('the_package_id')->references('id')->on('the_packages')->onDelete('cascade');
            $table->decimal('volume',20,4)->nullable();
            $table->decimal('longueur',20,2)->nullable();
            $table->decimal('largeur',20,2)->nullable();
            $table->decimal('hauteur',20,2)->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_tel')->nullable();
            $table->decimal('weight',20,2)->nullable();
            $table->string('image')->nullable();
            $table->boolean('mode_transport')->default(0);
            $table->string('other_field1',20,2)->nullable();
            $table->string('other_field2',20,2)->nullable();
            $table->dateTime('date_envoi');

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
        Schema::dropIfExists('packing_lists');
    }
}
