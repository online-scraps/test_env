<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFixedAssetsMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fixed_asset_entries', function (Blueprint $table) {
            $table->id();
            $table->timestamp('entry_date_ad')->nullable();
            $table->timestamp('entry_date_bs')->nullable();
            $table->tinyText('comments')->nullable();
            $table->unsignedFloat('gross_total')->nullable();
            $table->unsignedFloat('total_depreciation')->nullable();
            $table->unsignedFloat('taxable_amount')->nullable();
            $table->unsignedFloat('tax_total')->nullable();
            $table->unsignedFloat('net_amount')->nullable();
            $table->string('upload_bill')->nullable();

            $table->timestamps();

            $table->foreignId('sup_org_id')
                ->nullable()
                ->constrained('sup_organizations')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('store_id')
                ->nullable()
                ->constrained('mst_stores')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('status_id')
                ->nullable()
                ->constrained('sup_status')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::create('fixed_asset_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('add_qty')->nullable();
            $table->unsignedBigInteger('total_qty')->nullable();
            $table->tinyText('batch_no')->nullable();
            $table->unsignedFloat('unit_cost_price')->nullable();
            $table->unsignedInteger('tax_vat')->nullable();
            $table->unsignedFloat('depreciation')->nullable();
            $table->unsignedFloat('item_total')->nullable();

            $table->timestamps();

            $table->foreignId('sup_org_id')
                ->nullable()
                ->constrained('sup_organizations')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('mst_item_id')
                ->nullable()
                ->constrained('mst_items')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('fixed_asset_entry_id')
                ->nullable()
                ->constrained('fixed_asset_entries')
                ->onUpdate('cascade')
                ->onDelete('cascade');

        });

        Schema::table('stock_items_details', function (Blueprint $table) {
            $table->unsignedInteger('stock_item_id')->change()->nullable();
            $table->foreignId('fixed_asset_item_id')
                ->nullable()
                ->constrained('fixed_asset_items')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::table('stock_entries', function (Blueprint $table) {
            $table->unsignedFloat('gross_total')->change()->nullable();
            $table->unsignedFloat('taxable_amount')->change()->nullable();
            $table->unsignedFloat('tax_total')->change()->nullable();
            $table->unsignedFloat('net_amount')->change()->nullable();
        });

        Schema::table('stock_items', function (Blueprint $table) {
            $table->unsignedFloat('unit_cost_price')->change()->nullable();
            $table->unsignedFloat('unit_sales_price')->change()->nullable();
            $table->unsignedFloat('item_total')->change()->nullable();
        });

        Artisan::call('generate:permissions');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fixed_asset_entries');
        Schema::dropIfExists('fixed_asset_items');

        Schema::table('stock_items_details', function (Blueprint $table) {
            $table->dropForeign('fixed_asset_item_id');
        });

        Schema::table('stock_entries', function (Blueprint $table) {
            $table->unsignedFloat('gross_total')->change();
            $table->unsignedFloat('taxable_amount')->change();
            $table->unsignedFloat('tax_total')->change();
            $table->unsignedFloat('net_amount')->change();
        });

        Schema::table('stock_items', function (Blueprint $table) {
            $table->unsignedFloat('unit_cost_price')->change();
            $table->unsignedFloat('unit_sales_price')->change();
            $table->unsignedFloat('item_total')->change();
        });
    }
}
