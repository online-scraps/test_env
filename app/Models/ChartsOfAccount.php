<?php

namespace App\Models;

use App\Base\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class ChartsOfAccount extends BaseModel
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'charts_of_accounts';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = ['name','alias','print_name','group_id','opening_balance','dr_cr','address','country_id','email','pan','mobile_no','tel_no','fax','contact_person','maintain_bill_by_bill_balance',
                            'credit_day_for_sales','credit_day_for_for_purchase','specify_default_sales_type','default_sales_type','specify_default_purchase_type','default_purchase_type','freeze_sale_type',
                            'freeze_purchase_type','bank_details','beneficary_name','bank_name','bank_ac_no','ifsc_code','enable_email_query','enable_sms_query','primary_group','under','is_group','is_ledger',
                            'store_id','sup_org_id','ledger_type','ledger_id'];
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
    public function childs(){
        // if(backpack_user()->isStoreUser() ){
        //     return $this->hasMany(ChartsOfAccount::class, 'group_id', 'id')->where(function ($q) {
        //         $q->where('store_id', backpack_user()->store_id);
        //         $q->orWhere('sup_org_id', 1);
        //     });
        // }else 
        if(backpack_user()->isOrganizationUser() || backpack_user()->isStoreUser()){
            return $this->hasMany(ChartsOfAccount::class, 'group_id', 'id')->where(function ($q) {
                $q->where('sup_org_id', backpack_user()->sup_org_id);
                $q->orWhere('sup_org_id', 1);
            });
        }else{
            return $this->hasMany(ChartsOfAccount::class, 'group_id', 'id');
        }
    }

    public function subLedgers(){
        if(backpack_user()->isOrganizationUser() || backpack_user()->isStoreUser()){
            return $this->hasMany(ChartsOfAccount::class, 'ledger_id', 'id')->where(function ($q) {
                $q->where('sup_org_id', backpack_user()->sup_org_id);
                $q->orWhere('sup_org_id', 1);
            });
        }else{
            return $this->hasMany(ChartsOfAccount::class, 'ledger_id', 'id');
        }
    }

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
