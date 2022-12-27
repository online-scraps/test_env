<?php

namespace App\Models;

use App\Base\BaseModel;
use App\Models\GrnItem;
use App\Models\GrnTypes;
use App\Models\MstStore;
use App\Models\SupStatus;
use App\Models\MstSupplier;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class Grn extends BaseModel
{


    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'grns';
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

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function storeEntity(){
        return $this->belongsTo(MstStore::class,'store_id','id');
    }
   

    public function supplierEntity(){
        return $this->belongsTo(MstSupplier::class,'supplier_id','id');
    }

    public function statusEntity(){
        return $this->belongsTo(SupStatus::class,'status_id','id');
    }
    public function purchaseReturn(){
        return "<a href='".url(route('purchase-return-grn',$this->id))."' class='btn btn-sm btn-link'  data-toggle='tooltip' title='Purchase Return'><i class='fas fa-undo'></i></a>";
    }

    public function grnEntity(){
        return $this->belongsTo(GrnTypes::class,'grn_type_id','id');
    }
    public function grn_items()
    {
        return $this->hasMany(GrnItem::class,'grn_id','id');
    }
    public function approvedByEntity()
    {
        return $this->belongsTo(User::class,'approved_by','id');
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
