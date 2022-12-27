<?php

namespace App\Models;

use App\Base\BaseModel;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends BaseModel
{
    

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'purchase_return_items';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = ['purchase_return_id','return_qty','sup_org_id','purchase_qty','free_qty','total_qty','discount',
    'purchase_price','sales_price','item_amount','tax_vat','mst_items_id','return_reason_id','batch_qty','batch_no','discount_mode_id','store_id'];
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
    public function itemEntity(){
        return $this->belongsTo(MstItem::class,'mst_items_id','id');
    }
    public function childItemEntity(){
        return $this->belongsTo(MstItem::class,'mst_items_id','id');
    }

    public function discountEntity(){
        return $this->belongsTo(MstDiscMode::class,'discount_mode_id','id');
    }
    public function batchNoQuantity(){
        return $this->belongsTo(GrnItem::class,'batch_no_id','id');
    }

    public function returnReasonEntity(){
        return $this->belongsTo(ReturnReason::class,'return_reason_id','id');
    }
    public function mstItem()
    {
        return $this->belongsTo(MstItem::class,'mst_items_id','id');
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
