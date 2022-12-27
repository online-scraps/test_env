<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class StockAdjustmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
            Schema::create('item_qty_detail', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedSmallInteger('sup_org_id')->nullable();
                $table->unsignedSmallInteger('store_id')->nullable();
                $table->unsignedSmallInteger('item_id')->nullable();
                $table->integer('item_qty')->nullable();

                $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreign('item_id')->references('id')->on('mst_items')->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();

                $table->unsignedSmallInteger('created_by');
                $table->unsignedSmallInteger('updated_by')->nullable();
                $table->unsignedSmallInteger('deleted_by')->nullable();
                $table->unsignedInteger('deleted_uq_code')->default(1);
                $table->timestamp('deleted_at')->nullable();
                $table->timestamps();
            });
            Schema::create('batch_qty_detail', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedSmallInteger('sup_org_id')->nullable();
                $table->unsignedSmallInteger('store_id')->nullable();
                $table->unsignedSmallInteger('item_id')->nullable();
                $table->tinyText('batch_no')->nullable();
                $table->string('batch_from')->nullable();
                $table->integer('batch_qty')->nullable();
                $table->float('batch_price')->nullable();

                $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreign('item_id')->references('id')->on('mst_items')->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();

                $table->unsignedSmallInteger('created_by');
                $table->unsignedSmallInteger('updated_by')->nullable();
                $table->unsignedSmallInteger('deleted_by')->nullable();
                $table->unsignedInteger('deleted_uq_code')->default(1);
                $table->timestamp('deleted_at')->nullable();
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
        //
        Schema::dropIfExists('item_qty_detail');
        Schema::dropIfExists('batch_qty_detail');
    }
}
