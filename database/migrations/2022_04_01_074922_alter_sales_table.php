<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('sales_items', function (Blueprint $table) {
            $table->unsignedSmallInteger('grn_id')->nullable();
            $table->unsignedSmallInteger('item_qty_detail_id')->nullable();
            $table->unsignedSmallInteger('batch_qty_detail_id')->nullable();
            $table->integer('total_qty')->nullable();
            $table->integer('return_qty')->nullable();
            // $table->integer('remaining_qty')->nullable();
            $table->string('batch_no')->nullable();
            $table->integer('batch_qty')->nullable();

            $table->foreign('grn_id')->references('id')->on('grns')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('item_qty_detail_id')->references('id')->on('item_qty_detail')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('batch_qty_detail_id')->references('id')->on('batch_qty_detail')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('grn_id');
            $table->dropColumn('approved_by');
            $table->dropColumn('item_qty_detail_id');
            $table->dropColumn('total_qty');
            $table->dropColumn('batch_qty_detail_id');
            $table->dropColumn('batch_no');
            $table->dropColumn('batch_qty');
            $table->dropColumn('store_id');
        });
    }
}
