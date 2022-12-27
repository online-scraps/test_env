<?php

namespace App\Models;

use App\Base\BaseModel;
use App\Models\MstItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FixedAssetItems extends BaseModel
{
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'fixed_asset_items';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];



    public function mstItem()
    {
        return $this->belongsTo(MstItem::class, 'mst_item_id', 'id');
    }

    public function fixedAsset()
    {
        return $this->belongsTo(FixedAssetEntries::class, 'fixed_asset_entry_id', 'id');
    }

    public function barcodeDetails()
    {
        return $this->hasMany(StockItemDetails::class,'fixed_asset_item_id','id');
    }
}
