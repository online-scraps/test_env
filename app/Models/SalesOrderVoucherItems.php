<?php

namespace App\Models;

use App\Base\BaseModel;
use App\Models\MstItem;
use App\Models\StockItemDetails;
use App\Models\SalesOrderVoucher;
use App\Models\ItemQuantityDetail;
use App\Models\BatchQuantityDetail;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class SalesOrderVoucherItems extends BaseModel
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'sales_order_voucher_items';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function mstItem()
    {
        return $this->belongsTo(MstItem::class,'item_id','id');
    }
    public function sales()
    {
        return $this->belongsTo(SalesOrderVoucher::class,'sales_order_voucher_id','id');
    }

  

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
