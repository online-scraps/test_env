<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesOrderVoucherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_order_voucher', function (Blueprint $table) {
            $table->increments('id');
            $table->string('bill_no')->nullable();
            $table->integer('bill_type')->nullable();
            $table->unsignedSmallInteger('customer_id')->nullable();
            $table->string('bill_date_bs', 10)->nullable();
            $table->date('bill_date_ad')->nullable();
            $table->smallInteger('discount_type')->nullable();
            $table->float('discount')->nullable();
            $table->string('remarks')->nullable();
            $table->float('gross_amt')->nullable();
            $table->float('discount_amt')->nullable();
            $table->float('taxable_amt')->nullable();
            $table->float('total_tax_vat')->nullable();
            $table->float('net_amt')->nullable();
            $table->date('transaction_date_ad')->nullable();
            
            $table->unsignedSmallInteger('sup_org_id')->nullable();
 	 	 	$table->unsignedSmallInteger('store_id')->nullable();
 	 	 	$table->unsignedSmallInteger('status_id')->nullable();
 	 	 	$table->unsignedSmallInteger('discount_approver_id')->nullable();
 	 	 	$table->boolean('is_active')->default(true);
 	 	 	$table->timestamps();

			$table->unsignedSmallInteger('created_by');
			$table->unsignedSmallInteger('updated_by')->nullable();
			$table->unsignedSmallInteger('deleted_by')->nullable();
			$table->unsignedInteger('deleted_uq_code')->default(1);
			$table->timestamp('deleted_at')->nullable();
			
 
            $table->foreign('customer_id')->references('id')->on('mst_suppliers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('status_id')->references('id')->on('sup_status')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('discount_approver_id')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
        });

        Schema::create('sales_order_voucher_items', function (Blueprint $table) {

            $table->increments('id');
            $table->unsignedSmallInteger('sales_order_voucher_id')->nullable();
            $table->unsignedSmallInteger('item_id')->nullable();
            $table->unsignedSmallInteger('unit_id')->nullable();
            $table->unsignedSmallInteger('store_id')->nullable();

            $table->float('item_price')->nullable();
            $table->float('tax_vat')->nullable();
            $table->float('item_discount')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->integer('total_qty')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->softDeletes();
            $table->timestamps();

            
            $table->foreign('item_id')->references('id')->on('mst_items')->cascadeOnDelete()->cascadeOnDelete();
            $table->foreign('sales_order_voucher_id')->references('id')->on('sales_order_voucher')->cascadeOnDelete()->cascadeOnDelete();
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
        Schema::dropIfExists('sales_order_voucher');
        Schema::dropIfExists('sales_order_voucher_items');
    }
}
