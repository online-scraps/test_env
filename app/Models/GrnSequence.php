<?php

namespace App\Models;

use App\Base\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GrnSequence extends BaseModel
{
    protected $table='grn_sequences';
 	protected $fillable=['code','name_en','name_lc','sup_org_id','sequence_code','is_active','deleted_by','deleted_at','deleted_uq_code','store_id'];

}
