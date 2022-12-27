<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_settings', function (Blueprint $table) {
            $table->id();

            $table->unsignedSmallInteger('store_id')->nullable();
            $table->unsignedSmallInteger('sup_org_id')->nullable();

            $table->boolean('bill_by_bill')->default(false);
            $table->boolean('credit_limits')->default(false);
            $table->boolean('targets')->default(false);
            $table->boolean('cost_centers')->default(false);
            $table->boolean('ac_wise_intrest_rate')->default(false);
            $table->boolean('ledger_reconciliation')->default(false);
            $table->boolean('show_ac_current_balance')->default(false);
            $table->integer('balance_sheet_stock_updation')->nullable();
            $table->boolean('single_entry')->default(false);
            $table->boolean('posting_in_ac')->default(false);
            $table->boolean('party_dashboard')->default(false);
            $table->boolean('dashboard_after_selecting_party')->default(false);
            $table->boolean('maintain_ac_category')->default(false);
            $table->text('ac_category_caption')->nullable();
            
            $table->boolean('salesman_broker_reporting')->default(false);
            $table->boolean('budgets')->default(false);
            $table->boolean('royalty_calculation')->default(false);
            $table->boolean('company_act_depreciation')->default(false);
            $table->boolean('maintain_sub_ledgers')->default(false);
            $table->boolean('maintain_multiple_ac')->default(false);
            $table->boolean('multiple_currency')->default(false);
            $table->integer('decimal_place')->nullable();
            $table->boolean('maintain_image_note')->default(false);

            $table->boolean('bank_reconciliation')->default(false);
            $table->boolean('bank_instrument_detail')->default(false);
            $table->boolean('post_dated_cheque')->default(false);
            $table->boolean('cheque_printing')->default(false);

            $table->timestamps();

            $table->unsignedSmallInteger('created_by');
            $table->unsignedSmallInteger('updated_by')->nullable();
            $table->unsignedSmallInteger('deleted_by')->nullable();
            $table->unsignedInteger('deleted_uq_code')->default(1);
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('sup_org_id')->references('id')->on('sup_organizations')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('store_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_settings');
    }
}
