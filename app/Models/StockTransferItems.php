<?php

namespace App\Models;

use App\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransferItems extends BaseModel
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'stock_transfer_items';
    protected $guarded = ['id'];

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
        return $this->belongsTo(MstItem::class,'item_id','id');
    }

    public function stock()
    {
        return $this->belongsTo(StockTransfer::class,'stock_id','id');
    }

    public function barcodeDetails()
    {
        return $this->hasMany(StockItemDetails::class,'stock_transfer_item_id','id');
    }

    public function itemQty()
    {
        return $this->belongsTo(ItemQuantityDetail::class,'item_qty_detail_id','id');
    }

    public function batchQty()
    {
        return $this->belongsTo(BatchQuantityDetail::class,'batch_qty_detail_id','id');
    }

}
