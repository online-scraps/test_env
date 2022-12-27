<?php

namespace App\Models;

use App\Base\BaseModel;
use App\Models\MstStore;
use App\Models\Currencies;
use App\Models\SupOrganization;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class CurrencyConversion extends BaseModel
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'currency_conversion';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = ['store_id','sup_org_id','currency_type_id','standard_rate','buying_rate','selling_rate','is_active','deleted_by','updated_by','deleted_at','deleted_uq_code','created_by'];
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
    public function superOrganizationEntity(){
        return $this->belongsTo(SupOrganization::class,'sup_org_id', 'id');
    }

    public function mstStoreEntity(){
        return $this->belongsTo(MstStore::class,'store_id', 'id');
    }
    public function currency(){
        return $this->belongsTo(Currencies::class,'currency_type_id', 'id');
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
