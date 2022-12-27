<?php

namespace App\Models;

use App\Base\BaseModel;
use App\Http\Controllers\Admin\GeneralLedgerCrudController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MstItem extends BaseModel
{
    protected $table = 'mst_items';

    protected $guarded = ['id'];

    // protected $fillable = [
    //     'code', 'barcode_details', 'sup_org_id', 'store_id', 'batch_no', 'name', 'description', 'category_id', 'subcategory_id', 'supplier_id', 'brand_id', 'unit_id', 'stock_alert_minimum',
    //     'tax_vat', 'discount_mode_id', 'is_damaged', 'is_taxable', 'is_nonclaimable', 'is_staffdicount', 'is_active', 'deleted_by', 'deleted_at', 'deleted_uq_code', 'item_price',
    //     'is_price_editable', 'is_super_data', 'is_fixed_asset', 'asset_type_id',
    //     'op_stock_val', 'op_stock_qty'
    // ];

    public function mstStoreEntity()
    {
        return $this->belongsTo(MstStore::class, 'store_id', 'id');
    }
    public function manySubStoresEntity()
    {
        return $this->belongsToMany(MstStore::class, 'child_item_stores', 'item_id', 'store_id');
    }
    public function category()
    {
        return $this->belongsTo(MstCategory::class, 'category_id', 'id');
    }
    public function mstSubCategory()
    {
        return $this->belongsTo(MstSubcategory::class, 'subcategory_id', 'id');
    }
    public function mstSupplierEntity()
    {
        return $this->belongsTo(MstSupplier::class, 'supplier_id', 'id');
    }
    public function mstBrandEntity()
    {
        return $this->belongsTo(MstBrand::class, 'brand_id', 'id');
    }
    public function brand()
    {
        return $this->belongsTo(MstBrand::class, 'brand_id', 'id');
    }
    public function mstUnitEntity()
    {
        return $this->belongsTo(MstUnit::class, 'unit_id', 'id');
    }

    public function mstDiscModeEntity()
    {
        return $this->belongsTo(MstDiscMode::class, 'discount_mode_id', 'id');
    }

    public function parentDepartment()
    {
        return $this->belongsTo(MstDepartment::class, 'department_id ', 'id');
    }

    public function batchQtyDetails()
    {
        return $this->hasMany(BatchQuantityDetail::class, 'item_id', 'id');
    }
    public function itemQtyDetail()
    {
        return $this->hasOne(ItemQuantityDetail::class, 'item_id', 'id')->where('store_id', backpack_user()->store_id)->where('sup_org_id', backpack_user()->sup_org_id);
    }

    public function mstItemStores()
    {
        return $this->belongsToMany(MstStore::class, 'mst_item_stores', 'item_id', 'store_id');
    }
    public function childItemStores()
    {
        return $this->belongsToMany(MstStore::class, 'child_item_stores', 'item_id', 'store_id');
    }

    public function itemsSampleExcel()
    {
        return '<a href=' . '" /storage/uploads/sampleFiles/products.xlsx' . '" target=' . '"_blank' . '" class=' . '"btn btn-success btn-sm' . '" title=' . '"Download Excel Sample for uploading Bulk Items' . '" ><i class=' . '"fa fa-download' . '" aria-hidden=' . '"true' . '"></i> &nbsp; Sample</a>';
    }

    public function mstFixedAssettTypeEntity()
    {
        return $this->belongsTo(MstAssetType::class, 'asset_type_id', 'id');
    }

    public function generalLedgerSalesEntity()
    {
        return $this->belongsTo(GeneralLedger::CLASS, 'sales_acount_ledger_id', 'id');
    }

    public function generalLedgerPurchaseEntity()
    {
        return $this->belongsTo(GeneralLedger::CLASS, 'purchase_acount_ledger_id', 'id');
    }
    //sales
    public function salesEntity()
    {
        return $this->hasMany(SaleItems::class, 'item_id', 'id');
    }
}
