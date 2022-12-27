<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMstItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('mst_stores', function (Blueprint $table) {
            $table->unsignedSmallInteger('parent_id')->nullable();
        });

        Schema::table('mst_stores', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate(); 
        });

        Schema::create('child_item_stores', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('store_id')->nullable();
            $table->unsignedSmallInteger('item_id')->nullable();

            $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('item_id')->references('id')->on('mst_items')->cascadeOnDelete()->cascadeOnUpdate();

        });

        Schema::table('mst_items', function (Blueprint $table) {
            $table->unsignedSmallInteger('child_store_id')->nullable();
            $table->foreign('child_store_id')->references('id')->on('child_item_stores')->cascadeOnDelete()->cascadeOnUpdate();
            
        });

        

        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_items', function (Blueprint $table) {
            $table->dropColumn('child_store_id');
        });
        Schema::dropIfExists('child_item_stores');
    }
}
