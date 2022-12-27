<?php

namespace App\Models;

use App\Base\BaseModel;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class StockItems extends BaseModel
{

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'stock_items';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
     protected $fillable = [
         'stock_id',
         'mst_item_id',
         'available_total_qty',
         'add_qty',
         'total_qty',
         'batch_no',
         'free_item',
         'discount',
         'unit_cost_price',
         'unit_sales_price',
         'tax_vat',
         'item_total',
         'expiry_date',
         'sup_org_id',
         'store_id'
     ];
    // protected $hidden = [];
//     protected $dates = [
//     ];

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

    public function mstItem()
    {
        return $this->belongsTo(MstItem::class,'mst_item_id','id');
    }

    public function stock()
    {
        return $this->belongsTo(StockEntries::class,'stock_id','id');
    }

    public function barcodeDetails()
    {
        return $this->hasMany(StockItemDetails::class,'stock_item_id','id');
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
