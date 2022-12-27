<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryManagementTable extends Migration
{
       /**
        * Run the migrations.
        *
        * @return void
        */
       public function up()
       {
              Schema::create('purchase_order_types', function (Blueprint $table) {

                     $table->increments('id');
                     $table->string('code');
                     $table->string('name_en');
                     $table->string('name_lc')->nullable();
                     $table->string('description')->nullable();
                     $table->boolean('is_active')->default(true);
                     $table->timestamps();
                     $table->dateTime('deleted_at')->nullable();
                     $table->unsignedInteger('created_by')->nullable();
                     $table->unsignedInteger('updated_by')->nullable();
                     $table->unsignedInteger('deleted_by')->nullable();
                     $table->unsignedInteger('deleted_uq_code')->default(1);
                     $table->unsignedSmallInteger('store_id')->nullable();



                     $table->unique(['code', 'deleted_uq_code'], 'uq_purchase_order_types_code_deleted_uq_code');
                     $table->unique(['name_lc', 'deleted_uq_code'], 'uq_purchase_order_types_name_lc_deleted_uq_code');
                     $table->unique(['name_en', 'deleted_uq_code'], 'uq_purchase_order_types_name_en_deleted_uq_code');
                     $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();

              });
              Schema::create('grn_types', function (Blueprint $table) {

                     $table->increments('id');
                     $table->string('code');
                     $table->string('name_en');
                     $table->string('name_lc')->nullable();
                     $table->string('description')->nullable();
                     $table->boolean('is_active')->default(true);
                     $table->unsignedSmallInteger('store_id')->nullable();


                     $table->timestamps();

                     $table->unsignedSmallInteger('sup_org_id')->nullable();

                     $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();
                     $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();



                     $table->dateTime('deleted_at')->nullable();
                     $table->unsignedInteger('created_by')->nullable();
                     $table->unsignedInteger('updated_by')->nullable();
                     $table->unsignedInteger('deleted_by')->nullable();
                     $table->unsignedInteger('deleted_uq_code')->default(1);


                     $table->unique(['code', 'deleted_uq_code'], 'uq_grn_types_code_deleted_uq_code');
                     $table->unique(['name_lc', 'deleted_uq_code'], 'uq_grn_types_name_lc_deleted_uq_code');
                     $table->unique(['name_en', 'deleted_uq_code'], 'uq_grn_types_name_en_deleted_uq_code');
              });
              Schema::create('purchase_order_details', function (Blueprint $table) {

                     $table->increments('id');
                     $table->string('purchase_order_num')->nullable();
                     $table->string('po_date')->nullable();
                     $table->string('expected_delivery')->nullable();
                     $table->string('approved_by')->nullable();
                     $table->float('gross_amt')->nullable();
                     $table->float('discount_amt')->nullable();
                     $table->float('tax_amt')->nullable();
                     $table->float('other_charges')->nullable();
                     $table->float('net_amt')->nullable();
                     $table->string('comments')->nullable();



                     $table->timestamps();

                     $table->unsignedBigInteger('store_id')->nullable();
                     $table->unsignedBigInteger('supplier_id')->nullable();
                     $table->unsignedBigInteger('purchase_order_type_id');
                     $table->unsignedBigInteger('requested_store_id')->nullable();
                     $table->unsignedBigInteger('status_id');
                     $table->unsignedSmallInteger('sup_org_id')->nullable();



                     $table->foreign('store_id')->references('id')->on('mst_stores')
                            ->onDelete('restrict')->onUpdate('cascade');

                     $table->foreign('supplier_id')->references('id')->on('mst_suppliers')
                            ->onDelete('restrict')->onUpdate('cascade');

                     $table->foreign('purchase_order_type_id')->references('id')->on('purchase_order_types')
                            ->onDelete('restrict')->onUpdate('cascade');

                     $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();


                     $table->foreign('requested_store_id')->references('id')->on('mst_stores')
                            ->onDelete('restrict')->onUpdate('cascade');

                     $table->foreign('status_id')->references('id')->on('sup_status')
                            ->onDelete('restrict')->onUpdate('cascade');


                     $table->dateTime('deleted_at')->nullable();
                     $table->unsignedInteger('created_by')->nullable();
                     $table->unsignedInteger('updated_by')->nullable();
                     $table->unsignedInteger('deleted_by')->nullable();
                     $table->unsignedInteger('deleted_uq_code')->default(1);
              });

              Schema::create('purchase_items', function (Blueprint $table) {
                     $table->increments('id');
                     $table->unsignedBigInteger('po_id');
                     $table->unsignedSmallInteger('store_id')->nullable();


                     $table->integer('purchase_qty')->nullable();
                     $table->integer('free_qty')->nullable();
                     $table->integer('total_qty')->nullable();
                     $table->float('discount')->nullable();
                     $table->float('purchase_price')->nullable();
                     $table->float('sales_price')->nullable();
                     $table->float('item_amount')->nullable();
                     $table->float('tax_vat')->nullable();
                     $table->unsignedSmallInteger('sup_org_id')->nullable();

                     $table->unsignedBigInteger('items_id')->nullable();

                     $table->unsignedBigInteger('discount_mode_id')->nullable();


                     $table->timestamps();

                     $table->foreign('po_id')->references('id')->on('purchase_order_details')
                            ->onDelete('restrict')->onUpdate('cascade');






                     $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();

                     $table->foreign('discount_mode_id')->references('id')->on('mst_discount_modes')
                            ->onDelete('restrict')->onUpdate('cascade');

                     $table->foreign('items_id')->references('id')->on('mst_items')
                            ->onDelete('restrict')->onUpdate('cascade');

                     $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();




                     $table->dateTime('deleted_at')->nullable();
                     $table->unsignedInteger('created_by')->nullable();
                     $table->unsignedInteger('updated_by')->nullable();
                     $table->unsignedInteger('deleted_by')->nullable();
                     $table->unsignedInteger('deleted_uq_code')->default(1);
              });

              //grn migrations
              Schema::create('grns', function (Blueprint $table) {

                     $table->increments('id');
                     $table->string('purchase_order_id')->nullable();
                     $table->unsignedSmallInteger('store_id')->nullable();

                     $table->string('po_date')->nullable();
                     $table->string('dc_date')->nullable();
                     $table->integer('dc_no')->nullable();
                     $table->integer('invoice_no')->nullable();
                     $table->string('invoice_date')->nullable();
                     $table->string('grn_no')->nullable();
                     $table->string('grn_date')->nullable();
                     $table->string('approved_by')->nullable();
                     $table->float('gross_amt')->nullable();
                     $table->float('discount_amt')->nullable();
                     $table->float('tax_amt')->nullable();
                     $table->float('other_charges')->nullable();
                     $table->float('round_off')->nullable();
                     $table->float('net_amt')->nullable();
                     $table->string('comments')->nullable();


                     $table->timestamps();

                     $table->unsignedBigInteger('supplier_id');
                     $table->unsignedBigInteger('grn_type_id');
                     $table->unsignedBigInteger('status_id');
                     $table->unsignedSmallInteger('sup_org_id')->nullable();


                     $table->foreign('store_id')->references('id')->on('mst_stores')
                            ->onDelete('restrict')->onUpdate('cascade');

                     $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();


                     $table->foreign('supplier_id')->references('id')->on('mst_suppliers')
                            ->onDelete('restrict')->onUpdate('cascade');

                     $table->foreign('grn_type_id')->references('id')->on('grn_types')
                            ->onDelete('restrict')->onUpdate('cascade');

                     $table->foreign('status_id')->references('id')->on('sup_status')
                            ->onDelete('restrict')->onUpdate('cascade');


                     $table->dateTime('deleted_at')->nullable();
                     $table->unsignedInteger('created_by')->nullable();
                     $table->unsignedInteger('updated_by')->nullable();
                     $table->unsignedInteger('deleted_by')->nullable();
                     $table->unsignedInteger('deleted_uq_code')->default(1);
              });
              //end of grn migrations

              //grn details child tabel

              Schema::create('grn_items', function (Blueprint $table) {
                     $table->increments('id');
                     $table->integer('purchase_qty')->nullable();
                     $table->integer('received_qty')->nullable();
                     $table->integer('free_qty')->nullable();
                     $table->integer('invoice_qty')->nullable();
                     $table->integer('total_qty')->nullable();
                     $table->string('batch_no')->nullable();
                     $table->string('expiry_date')->nullable();
                     $table->float('discount')->nullable();
                     $table->float('purchase_price')->nullable();
                     $table->float('sales_price')->nullable();
                     $table->float('item_amount')->nullable();
                     $table->float('tax_vat')->nullable();


                     $table->timestamps();

                     $table->unsignedBigInteger('grn_id');

                     $table->unsignedBigInteger('discount_mode_id');
                     $table->unsignedBigInteger('mst_items_id');
                     $table->unsignedSmallInteger('sup_org_id')->nullable();



                     $table->foreign('discount_mode_id')->references('id')->on('mst_discount_modes')
                            ->onDelete('restrict')->onUpdate('cascade');

                     $table->foreign('mst_items_id')->references('id')->on('mst_items')
                            ->onDelete('restrict')->onUpdate('cascade');

                     $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();

                     $table->foreign('grn_id')->references('id')->on('grns')
                            ->onDelete('restrict')->onUpdate('cascade');




                     $table->dateTime('deleted_at')->nullable();
                     $table->unsignedInteger('created_by')->nullable();
                     $table->unsignedInteger('updated_by')->nullable();
                     $table->unsignedInteger('deleted_by')->nullable();
                     $table->unsignedInteger('deleted_uq_code')->default(1);
              });
              //grn details child table


              //purchase return migration

              Schema::create('purchase_returns', function (Blueprint $table) {

                     $table->increments('id');
                     $table->boolean('return_type')->nullable();
                     $table->float('gross_amt')->nullable();
                     $table->float('discount_amt')->nullable();
                     $table->float('taxable_amount')->nullable();
                     $table->float('tax_amt')->nullable();
                     $table->float('other_charges')->nullable();
                     $table->float('net_amt')->nullable();
                     $table->string('comments')->nullable();
                     $table->unsignedBigInteger('grn_id')->nullable();
                     $table->unsignedSmallInteger('sup_org_id')->nullable();
                     $table->unsignedBigInteger('store_id')->nullable();
                     $table->unsignedBigInteger('supplier_id');
                     $table->unsignedBigInteger('return_reason_id');
                     $table->string('return_no')->nullable();
                     $table->unsignedBigInteger('requested_store_id')->nullable();

                     $table->string('return_date')->nullable();
                     $table->unsignedBigInteger('approved_by')->nullable();
                     $table->unsignedBigInteger('status_id');

                     $table->timestamps();
                     $table->unsignedInteger('created_by')->nullable();
                     $table->unsignedInteger('updated_by')->nullable();
                     $table->unsignedInteger('deleted_by')->nullable();
                     $table->unsignedInteger('deleted_uq_code')->default(1);
                     $table->dateTime('deleted_at')->nullable();
                     
                     $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();
                     $table->foreign('store_id')->references('id')->on('mst_stores')->onDelete('restrict')->onUpdate('cascade');
                     $table->foreign('supplier_id')->references('id')->on('mst_suppliers')->onDelete('restrict')->onUpdate('cascade');
                     $table->foreign('return_reason_id')->references('id')->on('return_reasons')->onDelete('restrict')->onUpdate('cascade');
                     $table->foreign('requested_store_id')->references('id')->on('mst_stores')->onDelete('restrict')->onUpdate('cascade');
                     $table->foreign('grn_id')->references('id')->on('grns')->onDelete('restrict')->onUpdate('cascade');
                     $table->foreign('status_id')->references('id')->on('sup_status')->onDelete('restrict')->onUpdate('cascade');
                     $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
                     $table->foreign('approved_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
                     
              });
              //End of purchase return migration


              //return purchase items migration starts
              Schema::create('purchase_return_items', function (Blueprint $table) {
                     $table->increments('id');
                     $table->integer('purchase_qty')->nullable();
                     $table->integer('free_qty')->nullable();
                     $table->integer('return_qty')->nullable();
                     $table->integer('total_qty')->nullable();
                     $table->float('discount')->nullable();
                     $table->float('purchase_price')->nullable();
                     $table->float('sales_price')->nullable();
                     $table->float('item_amount')->nullable();
                     $table->integer('batch_qty')->nullable();
                     $table->string('batch_no')->nullable();
                     $table->float('tax_vat')->nullable();
                     
                     $table->unsignedInteger('purchase_return_id')->nullable();
                     $table->unsignedBigInteger('discount_mode_id')->nullable();
                     $table->unsignedBigInteger('mst_items_id')->nullable();
                     $table->unsignedSmallInteger('sup_org_id')->nullable();
                     $table->unsignedSmallInteger('store_id')->nullable();


                     $table->timestamps();
                     $table->unsignedInteger('created_by')->nullable();
                     $table->unsignedInteger('updated_by')->nullable();
                     $table->unsignedInteger('deleted_by')->nullable();
                     $table->dateTime('deleted_at')->nullable();
                     $table->unsignedInteger('deleted_uq_code')->default(1);

                     $table->foreign('discount_mode_id')->references('id')->on('mst_discount_modes')->onDelete('restrict')->onUpdate('cascade');
                     $table->foreign('mst_items_id')->references('id')->on('mst_items')->onDelete('restrict')->onUpdate('cascade');
                     $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();
                     $table->foreign('purchase_return_id')->references('id')->on('purchase_returns')->cascadeOnDelete()->cascadeOnUpdate();
                     $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
                     $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();

                     
              });

              //end of return purchse migration
       }

       /**
        * Reverse the migrations.
        *
        * @return void
        */
       public function down()
       {
              Schema::dropIfExists('purchase_order_types');
              Schema::dropIfExists('purchase_order_details');
              Schema::dropIfExists('purchase_items');
              Schema::dropIfExists('grns');
              Schema::dropIfExists('grn_items');
              Schema::dropIfExists('purchase_returns');
              Schema::dropIfExists('return_purchase_items');
       }
}
