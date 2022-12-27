<?php

namespace App\Models;

use App\Base\BaseModel;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class StockEntries extends BaseModel
{

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'stock_entries';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = [
        'store_id',
        'created_by',
        'approved_by',
        'adjustment_no',
        'comments',
        'gross_total',
        'total_discount',
        'flat_discount',
        'taxable_amount',
        'tax_total',
        'net_amount',
        'sup_status_id',
        'entry_date_ad',
        'entry_date_bs',
        'sup_org_id',
        'is_chalan'
    ];


    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function getBatchNo()
    {
        if($this->sup_status_id == Supstatus::APPROVED){
            $batchId = StockItems::where('stock_id', $this->id)
                    ->where('batch_no', '!=', null)
                    ->first()->batch_no ?? 'n/a';
            return MstSequence::find($batchId)->sequence_code;
        }else{
            return 'N/A';
        }
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
        return $this->hasMany(StockItems::class, 'stock_id', 'id');
    }

    public function adjustmentSequence()
    {
        return $this->belongsTo(MstSequence::class,'adjustment_no', 'id');
    }

    // public function stockEntriesExcel()
    // {
    //     // return '<button class=' . '"btn btn-secondary' . '" title=' . '"Return the Sales' . '" onclick=' . '"INVENTORY.openModelForStock()' . '"><i class=' . '"fa fa-file-excel-o' . '" aria-hidden=' . '"true' . '"></i> Import Excel</button>';
    //     return view('excelModelForStock');
    // }

    public function stockEntriesSampleExcel()
    {
        return '<a href=' . '" /storage/uploads/sampleFiles/stock-entries.xlsx' . '" target=' . '"_blank' . '" class=' . '"btn btn-success btn-sm' . '" title=' . '"Download Excel Sample for Stock Entries' . '" ><i class=' . '"fa fa-download' . '" aria-hidden=' . '"true' . '"></i> &nbsp;Sample</a>';
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
