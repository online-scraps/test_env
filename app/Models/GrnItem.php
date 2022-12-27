<?php

namespace App\Models;

use App\Base\BaseModel;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class GrnItem extends BaseModel
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'grn_items';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = ['purchase_qty','received_qty','invoice_qty','batch_no','free_qty','total_qty','discount',
    'purchase_price','expiry_date','sales_price','item_amount','tax_vat','mst_items_id','discount_mode_id','grn_id','sup_org_id','store_id'];
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
