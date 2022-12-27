<?php
namespace App\Base;

use App\Models\User;
use App\Models\MstStore;
use App\Models\AppClient;
use App\Models\MstCountry;
use App\Models\MstDiscMode;
use App\Models\MstDistrict;
use App\Models\MstProvince;
use App\Base\Traits\ComboField;
use App\Models\SupOrganization;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Backpack\CRUD\app\Models\Traits\CrudTrait;


class BaseModel extends Model
{
    use CrudTrait;
    use ComboField;
    // use LogsActivity;

    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $guarded = ['id','created_at','created_by'];

    const JOURNALVOUCHER = 1;
    const CONTRAVOUCHER = 2;
    const PAYMENTVOUCHER = 3;
    const RECEIPTVOUCHER = 4;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $columns = Schema::getColumnListing($model->getTable());

            if(in_array('code', $columns)){
                $code = self::generateCode($model);
                $model->code = $code;
            }

            if(in_array('created_by', $columns)){
                $model->created_by =  !is_null(backpack_user()) ? backpack_user()->id : 1;
            }

            if(in_array('sup_org_id', $columns) && in_array('created_by', $columns)){
                if(!backpack_user()->hasRole('superadmin'))
                {
                    $model->created_by =  !is_null(backpack_user()) ? backpack_user()->id : 1;
                }
            }
        });

        static::updating(function ($model){
            $columns = Schema::getColumnListing($model->getTable());
            if(in_array('updated_by', $columns)){
                $model->created_by =  !is_null(backpack_user()) ? backpack_user()->id : 1;
            }
            if(in_array('sup_org_id', $columns)){
                if(!backpack_user()->hasRole('superadmin'))
                {
                    $model->sup_org_id =  backpack_user()->sup_org_id;
                }
            }
        });
    }

    public static function generateCode($model)
    {
        $table = $model->getTable();
        $qu = DB::table($table)
                    ->selectRaw('COALESCE(max(code::NUMERIC),0)+1 as code')
                    ->whereRaw("(code ~ '^([0-9]+[.]?[0-9]*|[.][0-9]+)$') = true");
                    // ->where('deleted_uq_code',1);
                if(in_array('office_id',$model->getFillable())){
                    $qu->where('office_id', backpack_user()->office_id);
                }
                $rec = $qu->first();
                if(isset($rec)){
                    $code = $rec->code;
                }
                else{
                    $code = 1;
                }
                return $code;
    }

    //Relation
    public function countryEntity(){
        return $this->belongsTo(MstCountry::class,'country_id','id');
    }

    public function provinceEntity(){
        return $this->belongsTo(MstProvince::class,'province_id','id');
    }

    public function districtEntity(){
        return $this->belongsTo(MstDistrict::class,'district_id','id');
    }

    public function localLevelEntity(){
        return $this->belongsTo(MstFedLocalLevel::class,'local_level_id','id');
    }
    public function superOrganizationEntity(){
        return $this->belongsTo(SupOrganization::class,'sup_org_id','id');
    }


    public function createdByEntity(){
        return $this->belongsTo(User::class,'created_by','id');
    }
    public function updatedByEntity(){
        return $this->belongsTo(User::class,'updated_by','id');
    }

    public function approvedByEntity(){
        return $this->belongsTo(User::class,'approved_by','id');
    }

   public function mstStoreEntity()
     {
         return $this->belongsTo(MstStore::class,'store_id','id');
     }
   public function discountModeEntity()
     {
         return $this->belongsTo(MstDiscMode::class,'discount_mode_id','id');
     }


}
