<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('sup_org_id');
            $table->string('bill_no')->nullable();
            $table->string('return_bill_no')->nullable();
            $table->integer('bill_type')->nullable();
            $table->string('full_name')->nullable();
            $table->smallInteger('gender_id')->nullable();
            $table->integer('age')->nullable();
            $table->string('address', 200)->nullable();
            $table->string('contact_number')->nullable();
            $table->string('pan_vat')->nullable();
            $table->string('company_name')->nullable();
            $table->string('bill_date_bs', 10)->nullable();
            $table->date('bill_date_ad')->nullable();
            $table->smallInteger('discount_type')->nullable();
            $table->float('discount')->nullable();
            $table->string('remarks')->nullable();
            $table->string('payment_type')->nullable();
            $table->float('receipt_amt')->nullable();
            $table->float('gross_amt')->nullable();
            $table->float('discount_amt')->nullable();
            $table->float('taxable_amt')->nullable();
            $table->float('total_tax_vat')->nullable();
            $table->float('net_amt')->nullable();
            $table->float('paid_amt')->nullable();
            $table->float('refund')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('cheque_number')->nullable();
            $table->string('cheque_date')->nullable();
            $table->string('ac_holder_name')->nullable();
            $table->string('cheque_upload')->nullable();
            $table->float('due_amt')->nullable();
            $table->boolean('is_return')->default(false);
            $table->date('transaction_date_ad')->nullable();
            $table->unsignedSmallInteger('return_reason_id')->nullable();
            $table->unsignedSmallInteger('due_approver_id')->nullable();
            $table->unsignedSmallInteger('store_id')->nullable();
            $table->unsignedSmallInteger('status_id')->nullable();
            $table->unsignedSmallInteger('discount_approver_id')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->softDeletes();
            $table->timestamps();
            
            $table->foreign('gender_id')
            ->references('id')
            ->on('mst_genders')
            ->onUpdate('cascade')
            ->onDelete('restrict');
            
            $table->foreign('due_approver_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('return_reason_id')->references('id')->on('return_reasons')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('status_id')->references('id')->on('sup_status')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('discount_approver_id')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
        });


        // Schema::create('sales',fuction(Blue))

        Schema::create('sales_items', function (Blueprint $table) {

            $table->increments('id');
            $table->unsignedSmallInteger('sales_id')->nullable();
            $table->unsignedSmallInteger('item_id')->nullable();
            $table->unsignedSmallInteger('unit_id')->nullable();
            $table->unsignedSmallInteger('store_id')->nullable();

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

            
            $table->foreign('item_id')->references('id')->on('mst_items')->cascadeOnDelete()->cascadeOnDelete();
            $table->foreign('sales_id')->references('id')->on('sales')->cascadeOnDelete()->cascadeOnDelete();
            $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();


            // $table->foreign('grn_items_id')
            //     ->references('id')
            //     ->on('grn_items')
            //     ->cascadeOnDelete()->cascadeOnDelete();
                
            
        });
        // Schema::create('sales_items_details', function (Blueprint $table) {
        //     $table->increments('id');
        //     $table->unsignedInteger('sales_item_id');
        //     $table->unsignedSmallInteger('sup_org_id')->nullable();
        //     $table->unsignedSmallInteger('store_id')->nullable();
        //     $table->tinyText('barcode_details')->nullable();
        //     $table->boolean('is_active')->default(true);
        //     $table->foreign('store_id')->references('id')->on('mst_stores');
        //     $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();
        //     $table->foreign('sales_item_id')->references('id')->on('sales_items')->cascadeOnDelete()->cascadeOnUpdate();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
        Schema::dropIfExists('sales_items');
        // Schema::dropIfExists('sales_items_details');
    }
}
