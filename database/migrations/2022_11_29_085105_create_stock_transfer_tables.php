<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTransferTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_transfer_entries', function (Blueprint $table) {
            $table->id();
            $table->date('entry_date_bs')->nullable();
            $table->date('entry_date_ad')->nullable();
            $table->string('adjustment_no')->nullable();
            $table->text('comments')->nullable();
            $table->foreignId('from_store_id')->nullable()->constrained('mst_stores')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('to_store_id')->nullable()->constrained('mst_stores')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('sup_status_id')->nullable()->constrained('sup_status')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('sup_org_id')->nullable()->constrained('sup_organizations')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->integer('item_qty')->nullable();
            $table->float('item_price')->nullable();
            $table->float('tax_vat')->nullable();
            $table->float('item_discount')->nullable();
            $table->float('item_total')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->softDeletes();
            $table->timestamps();

            $table->foreignId('unit_id')->nullable()->constrained('mst_units')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('mst_items')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('stock_transfer_id')->nullable()->constrained('stock_transfer_entries')->cascadeOnDelete()->cascadeOnDelete();
            $table->foreignId('from_store_id')->nullable()->constrained('mst_stores')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('to_store_id')->nullable()->constrained('mst_stores')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('sup_org_id')->nullable()->constrained('sup_organizations')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_transfer_entries');
    }
}
