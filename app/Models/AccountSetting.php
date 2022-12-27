<?php

namespace App\Models;

use App\Base\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class AccountSetting extends BaseModel
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'account_settings';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = ['bill_by_bill','credit_limits','targets','cost_centers','ac_wise_intrest_rate','ledger_reconciliation','show_ac_current_balance',
                            'balance_sheet_stock_updation','single_entry','posting_in_ac','party_dashboard','dashboard_after_selecting_party','maintain_ac_category',
                            'ac_category_caption','salesman_broker_reporting','budgets','royalty_calculation','company_act_depreciation','maintain_sub_ledgers',
                            'maintain_multiple_ac','multiple_currency','decimal_place','maintain_image_note','bank_reconciliation','bank_instrument_detail','post_dated_cheque',
                            'cheque_printing','store_id','sup_org_id'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
