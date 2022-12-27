<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChartsOfAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charts_of_accounts', function (Blueprint $table) {
            $table->id();

            $table->unsignedSmallInteger('store_id')->nullable();
            $table->unsignedSmallInteger('sup_org_id')->nullable();

            $table->string('name', 250);
            $table->string('alias', 250)->nullable();
            $table->string('print_name', 250)->nullable();
            $table->unsignedSmallInteger('group_id')->nullable();
            $table->float('opening_balance')->nullable();
            $table->float('closing_balance')->nullable();
            $table->integer('dr_cr')->nullable();
            $table->string('address', 250)->nullable();
            $table->unsignedSmallInteger('country_id')->nullable();
            $table->string('email', 250)->nullable();
            $table->string('pan', 250)->nullable();
            $table->string('mobile_no', 250)->nullable();
            $table->string('tel_no', 250)->nullable();
            $table->string('fax', 250)->nullable();
            $table->string('contact_person', 250)->nullable();
            $table->integer('maintain_bill_by_bill_balance')->nullable();
            $table->float('credit_day_for_sales')->nullable();
            $table->float('credit_day_for_purchase')->nullable();
            $table->integer('specify_default_sales_type')->nullable();
            $table->integer('default_sales_type')->nullable();
            $table->integer('specify_default_purchase_type')->nullable();
            $table->integer('default_purchase_type')->nullable();
            $table->integer('freeze_sale_type')->nullable();
            $table->integer('freeze_purchase_type')->nullable();
            $table->string('bank_details', 500)->nullable();
            $table->string('beneficary_name', 250)->nullable();
            $table->string('bank_name', 250)->nullable();
            $table->string('bank_ac_no', 250)->nullable();
            $table->string('ifsc_code', 250)->nullable();
            $table->integer('enable_email_query')->nullable();
            $table->integer('enable_sms_query')->nullable();
            $table->string('remarks', 500)->nullable();
            $table->boolean('is_ledger')->default(false);
            $table->boolean('is_group')->default(false);

            $table->integer('primary_group')->nullable();
            $table->unsignedSmallInteger('under')->nullable();
            
            $table->timestamps();

            $table->unsignedSmallInteger('created_by');
            $table->unsignedSmallInteger('updated_by')->nullable();
            $table->unsignedSmallInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('store_id')->references('id')->on('mst_stores')->cascadeOnDelete()->cascadeOnUpdate();

            $table->foreign('group_id')->references('id')->on('charts_of_accounts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('under')->references('id')->on('charts_of_accounts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('country_id')->references('id')->on('mst_countries')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('charts_of_accounts');
    }
}
