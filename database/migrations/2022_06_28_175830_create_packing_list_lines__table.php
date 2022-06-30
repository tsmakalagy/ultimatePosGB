<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackingListLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packing_list_lines_', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('packing_list_id');
            $table->foreign('packing_list_id')->references('id')->on('packing_lists')->onDelete('cascade');
            $table->unsignedInteger('the_package_id');
            $table->foreign('the_package_id')->references('id')->on('the_packages')->onDelete('cascade');
          
            $table->decimal('qte', 22, 4)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packing_list_lines_');
    }
}
