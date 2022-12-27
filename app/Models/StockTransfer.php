<?php

namespace App\Models;

use App\Base\BaseModel;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class StockTransfer extends BaseModel
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'stock_transfer_entries';
    protected $guarded = ['id'];
    protected $dates = ['entry_date_ad', 'entry_date_bs'];


    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function getBatchNo()
    {
        return StockTransferItems::where('stock_transfer_id', $this->id)
            ->where('batch_no', '!=', null)
            ->first()->batch_no ?? 'n/a';
    }

    public function getStockStatus()
    {
        return ucfirst(SupStatus::find($this->sup_status_id)->name_en) ?? 'n/a';
    }

    public function getDateString()
    {
        return $this->entry_date_bs ? dateToString($this->entry_date_bs) : '';
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function fromStoreEntity()
    {
        return $this->belongsTo(MstStore::class, 'from_store_id', 'id');
    }

    public function toStoreEntity()
    {
        return $this->belongsTo(MstStore::class, 'to_store_id', 'id');
    }

    public function mstStore()
    {
        return $this->belongsTo(MstStore::class, 'store_id', 'id');
    }

    public function supStatus()
    {
        return $this->belongsTo(SupStatus::class, 'sup_status_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function items()
    {
        return $this->hasMany(StockTransferItems::class, 'stock_transfer_id', 'id');
    }

    public function adjustmentSequence()
    {
        return $this->belongsTo(MstSequence::class,'adjustment_no', 'id');
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
