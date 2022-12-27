<?php

namespace App\Models;

use App\Base\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReturnReason extends BaseModel
{
    protected $table='return_reasons';
 	protected $fillable=['code','sup_org_id','name_en','name_lc','description','is_active','deleted_by','deleted_at','deleted_uq_code','store_id'];

}
