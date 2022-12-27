<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillSundriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_sundries', function (Blueprint $table) {
            $table->id();

            // General
            $table->string('name')->nullable();
            $table->string('alias')->nullable();
            $table->string('print_name')->nullable();
            $table->integer('sundry_type')->nullable();
            $table->integer('sundry_nature')->nullable();
            $table->integer('default_value')->nullable();
            $table->string('sub_total_heading')->nullable();
            $table->boolean('account_sale')->nullable();
            $table->boolean('account_purchase')->nullable();
            $table->boolean('affects_good_sales')->nullable();
            $table->boolean('affects_good_purchase')->nullable();
            $table->boolean('affects_good_material_issue')->nullable();
            $table->boolean('affects_good_material_receipt')->nullable();
            $table->boolean('affects_good_stock_transfer')->nullable();

            // Accounting in Sale
            $table->boolean('affects_accounting_sale')->nullable();
            $table->boolean('adjust_amount_sale')->nullable();
            $table->string('account_head_sale')->nullable();
            $table->boolean('adjust_party_amount_sale')->nullable();
            $table->string('account_head_party_sale')->nullable();
            $table->boolean('post_over_sale')->nullable();
            $table->boolean('impact_zero_tax_sale')->nullable();

            // Accounting in Purchase
            $table->boolean('affects_accounting_purchase')->nullable();
            $table->boolean('adjust_amount_purchase')->nullable();
            $table->string('account_head_purchase')->nullable();
            $table->boolean('adjust_party_amount_purchase')->nullable();
            $table->string('account_head_party_purchase')->nullable();
            $table->boolean('post_over_purchase')->nullable();
            $table->boolean('impact_zero_tax_purchase')->nullable();

            // Accounting in Material Issue/ Receipt/ Stock Transfer
            $table->integer('accounting_material')->nullable();

            // Remaining

            // Amount of Bill Sundry to be Fed as
            $table->integer('bill_sundry_fed')->nullable();

            // OF
            $table->integer('percentage_of')->nullable();
            $table->boolean('selective_calc')->nullable();

            // Previous Bill Sundry(s) Details
            $table->integer('no_bill_sundry')->nullable();
            $table->boolean('consolidate_amount')->nullable();

            // Bill Sundry to be Calculated On
            $table->integer('cal_type')->nullable();

            // Bill Sundry Amount Round Off
            $table->boolean('round_off')->nullable();
            $table->integer('round_off_nearest')->nullable();

            //Default Columns (Must Add)
			$table->unsignedInteger('deleted_uq_code')->default(1);
			$table->timestamp('deleted_at')->nullable();
            $table->foreignId('sup_org_id')->nullable()->constrained('sup_organizations')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('store_id')->nullable()->constrained('mst_stores')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('bill_sundries');
    }
}
