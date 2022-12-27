<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockManagementMigrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('stock_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('store_id')->nullable();
            $table->unsignedInteger('sup_org_id');
            $table->timestamp('entry_date_ad')->nullable();
            $table->timestamp('entry_date_bs')->nullable();
            $table->string('adjustment_no')->nullable();

            $table->unsignedSmallInteger('created_by');
            $table->unsignedSmallInteger('approved_by')->nullable();

            $table->tinyText('comments')->nullable();
            $table->unsignedFloat('gross_total');
            $table->unsignedFloat('total_discount')->nullable();
            $table->unsignedFloat('flat_discount')->nullable();
            $table->unsignedFloat('taxable_amount');
            $table->unsignedFloat('tax_total');
            $table->unsignedFloat('net_amount');
            $table->unsignedInteger('sup_status_id');

            $table->timestamps();
            $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('sup_status_id')->references('id')->on('sup_status')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
        });


        Schema::create('stock_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('stock_id');
            $table->unsignedInteger('sup_org_id');
            $table->unsignedSmallInteger('store_id')->nullable();
            $table->unsignedInteger('mst_item_id');
            $table->unsignedBigInteger('available_total_qty');
            $table->unsignedBigInteger('add_qty');
            $table->unsignedBigInteger('total_qty');
            $table->tinyText('batch_no')->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->unsignedInteger('free_item')->nullable();
            $table->unsignedFloat('discount')->nullable();
            $table->unsignedFloat('unit_cost_price');
            $table->unsignedFloat('unit_sales_price');
            $table->unsignedInteger('tax_vat')->nullable();
            $table->unsignedFloat('item_total');
            $table->foreign('mst_item_id')->references('id')->on('mst_items')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('stock_id')->references('id')->on('stock_entries')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });

        Schema::create('stock_items_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('stock_item_id');
            $table->unsignedInteger('sales_item_id')->nullable();
            $table->unsignedInteger('item_id');
            $table->unsignedSmallInteger('sup_org_id')->nullable();

            $table->unsignedSmallInteger('store_id')->nullable();
            $table->tinyText('barcode_details')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreign('stock_item_id')->references('id')->on('stock_items')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('sales_item_id')->references('id')->on('sales_items');
            $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();

        });

        }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_entries');
        Schema::dropIfExists('stock_items');
        Schema::dropIfExists('stock_items_details');
    }
}
