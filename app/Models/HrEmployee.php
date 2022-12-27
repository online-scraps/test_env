<?php

namespace App\Models;

use App\Base\BaseModel;
use App\Models\MstGender;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Intervention\Image\ImageManagerStatic as Image;



class HrEmployee extends BaseModel
{
   

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'hr_employees';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = ['full_name','gender_id','date_bs','date_ad','position_id',
    'department_id','province_id','district_id','local_level_id','address','email',
    'contact_number','photo','pan_number','pan_photo_upload','citizenship_number',
    'citizenship_file_upload','national_identity_number','national_identiy_file_upload',
    'is_active','contact_person_details','country_id','person_full_name','person_email',
     'person_contact_number','person_address','person_citizenship_number','person_citizenship_photo_upload','store_id'];
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
    public function genderEntity(){
        return $this->belongsTo(MstGender::class,'gender_id','id');
    }
    public function positionEntity(){
        return $this->belongsTo(MstPosition::class,'position_id','id');
    }
    public function departmentEntity(){
        return $this->belongsTo(MstDepartment::class,'department_id','id');
    }
   
    public function countryEntity(){
        return $this->belongsTo(MstCountry::class,'country_id','id');
    }
    public function relationEntity(){
        return $this->belongsTo(MstRelation::class,'relation_id','id');
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

    
    public function setPhotoAttribute($value)
    {
        $attribute_name = "photo";
        // or use your own disk, defined in config/filesystems.php
        $disk = "uploads";
        // destination path relative to the disk above
        $destination_path = "HrEmployees/Photos";

        // if the image was erased
        if ($value==null) {
            // delete the image from disk
            \Storage::disk($disk)->delete($this->{$attribute_name});

            // set null in the database column
            $this->attributes[$attribute_name] = null;
        }

        // if a base64 was sent, store it in the db
        if (Str::startsWith($value, 'data:image'))
        {
            // 0. Make the image
            $image = \Image::make($value)->encode('jpg', 90);

            // 1. Generate a filename.
            $filename = md5($value.time()).'.jpg';

            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path.'/'.$filename, $image->stream());

            // 3. Delete the previous image, if there was one.
            \Storage::disk($disk)->delete($this->{$attribute_name});

            // 4. Save the public path to the database
            // but first, remove "public/" from the path, since we're pointing to it
            // from the root folder; that way, what gets saved in the db
            // is the public URL (everything that comes after the domain name)
            $public_destination_path = Str::replaceFirst('public/', '', $destination_path);
            $this->attributes[$attribute_name] = $public_destination_path.'/'.$filename;
            // dd($val); 
        }
    }
    public function setNationalIdentityFileUploadAttribute($value)
    {
        // dd('ok');
        $attribute_name = "national_identity_file_upload";
        $disk = "uploads";
        $destination_path = "HrEmployees/NationalIdentity";
      
// dd($value);
     $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }


    public function setPanPhotoUploadAttribute($value)
    {
        // dd('ok');
        $attribute_name = "pan_photo_upload";
        $disk = "uploads";
        $destination_path = "HrEmployees/Pan";
      
// dd($value);
     $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
        
    }



    public function setCitizenshipFileUploadAttribute($value)
    {
        $attribute_name = "citizenship_file_upload";
        $disk = "uploads";
        $destination_path = "HrEmployees/Citizenship";
// dd($value);
        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }
    public static function boot()
    {
        parent::boot();
        static::deleted(function($obj) {
            \Storage::disk('public_folder')->delete($obj->image);
        });
    }
}
