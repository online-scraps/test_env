<?php

namespace App\Models;

use App\Base\BaseModel;
use App\Models\MstStore;
use App\Models\SaleItems;
use App\Models\SupStatus;
use App\Models\MstCustomer;
use App\Models\SalesOrderVoucherItems;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class SalesOrderVoucher extends BaseModel
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'sales_order_voucher';
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

    public function storeEntity()
    {
        return $this->belongsTo(MstStore::class, 'store_id', 'id');
    }
    public function gender()
    {
        return $this->belongsTo('App\Models\MstGender', 'gender_id', 'id');
    }

    public function saleItems()
    {
        return $this->hasMany(SalesOrderVoucherItems::class, 'sales_order_voucher_id', 'id');
    }

    

    public function supStatus()
    {
        return $this->belongsTo(SupStatus::class, 'status_id', 'id');
    }


    public function customerEntity()
    {
        return $this->belongsTo(MstCustomer::class, 'customer_id', 'id');
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

    public function printInvoice()
    {
        if ($this->status_id == SupStatus::APPROVED) {
            return '<a href=' . '"sales-order-voucher/' . $this->id . '/Invoice" target=' . '"_blank' . '" class=' . '"btn btn-sm btn-link btn-primary show-btn' . '" title=' . '"Print the Sales Order Invoice Receipt with Header' . '"><i class=' . '"fa fa-print' . '" aria-hidden=' . '"true' . '"></i></a>';
        }
    }


    // public function printInvoiceNoHeader()
    // {
    //     if ($this->status_id == SupStatus::APPROVED) {
    //         return '<a href=' . '"sales/' . $this->id . '/InvoiceNoHeader" target=' . '"_blank' . '" class=' . '"btn btn-sm btn-link btn-primary show-btn' . '" title=' . '"Print the Invoice Receipt Without Header' . '"><i class=' . '"fa fa-file-text' . '" aria-hidden=' . '"true' . '"></i></a>';
    //     }
    // }

    public function getBill()
    {
        if (($this->status_id == SupStatus::APPROVED) || ($this->status_id == SupStatus::PARTIAL_RETURN) || ($this->status_id == SupStatus::FULL_RETURN)) {
            return '<b><a href=' . '"sales-order-voucher/' . $this->id . '/edit" title="Click to Edit">' . $this->bill_no . '</a></b>';
        }
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
