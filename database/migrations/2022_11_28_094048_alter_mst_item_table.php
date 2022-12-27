<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMstItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_items', function (Blueprint $table) {

            // Main Unit Details
            $table->string('op_stock_qty')->nullable();
            $table->string('op_stock_val')->nullable();

            // Discount & Markup Det.
            $table->string('sales_discount')->nullable();
            $table->string('purchase_discount')->nullable();
            $table->boolean('sales_disc_str')->default(false);
            $table->boolean('purchase_disc_str')->default(false);

            // Item Price Info
            $table->string('sales_price_sale')->nullable();
            $table->string('sales_price_purchase')->nullable();
            $table->string('purchase_price_sale')->nullable();
            $table->string('purchase_price_purchase')->nullable();
            $table->string('mrp_sale')->nullable();
            $table->string('mrp_purchase')->nullable();
            $table->string('min_sale_price_sale')->nullable();
            $table->string('min_sale_price_purchase')->nullable();
            $table->string('self_val_price_sale')->nullable();
            $table->string('self_val_price_purchase')->nullable();

            // Default Unit for Salesq
            $table->boolean('tax_inc_sales')->default(false);
            $table->boolean('sales_account_sales')->default(false);
            // $table->integer('sales_acount_ledger_id')->default(false);
            $table->boolean('tax_inc_purchase')->default(false);
            $table->boolean('sales_account_purchase')->default(false);
            // $table->integer('purchase_acount_ledger_id')->default(false);

            $table->foreignId('sales_acount_ledger_id')->nullable()->constrained('general_ledgers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('purchase_acount_ledger_id')->nullable()->constrained('general_ledgers')->cascadeOnUpdate()->cascadeOnDelete();



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
    }
}
