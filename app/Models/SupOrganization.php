<?php

namespace App\Models;

use App\Base\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Image;

class SupOrganization extends BaseModel
{
    protected $table='sup_organizations';
 	protected $fillable=['code','name_en','name_lc','address','email','phone_no','logo','description','is_active','multiple_barcode','deleted_by','deleted_at',
                        'country_id','province_id','district_id','user_level','deleted_uq_code', 'store_id'];

     public function setLogoAttribute($value)
     {
         $attribute_name = "logo";
         // disk, defined in config/filesystems.php
         $disk = "uploads";
         // destination path relative to the disk above
         $destination_path = "InventoryManagement\Logo";

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
             // $image = \Image::make($value)->encode('jpg', 90);
             $raw_image = \Image::make($value);
             //get image extension. if its jpeg, remove e.
             $ext = str_replace('e','',substr($raw_image->mime(),6));
             $image = $raw_image->encode($ext,90);
             // 1. Generate a filename.
             $filename = md5($value.time()).'.'.$ext;
             // 2. Store the image on disk.
             \Storage::disk($disk)->put($destination_path.'/'.$filename, $image->stream());

             // 3. Delete the previous image, if there was one.
             \Storage::disk($disk)->delete($this->{$attribute_name});

             // 4. Save the public path to the database
             // but first, remove "public/" from the path, since we're pointing to it
             // from the root folder; that way, what gets saved in the db
             // is the public URL (everything that comes after the domain name)
             $this->attributes[$attribute_name] = $destination_path.'/'.$filename;
             // $public_destination_path = Str::replaceFirst('public/', '', $destination_path);
             // $this->attributes[$attribute_name] = '/'.$public_destination_path.'/'.$filename;
         }
     }

     public static function boot()
     {
         parent::boot();
         static::deleted(function ($obj) {
             Storage::disk('uploads')->delete($obj->image);
         });
     }


}
