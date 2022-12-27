<?php

namespace App\Models;

use App\Base\BaseModel;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class RoleHasMenuAccess extends BaseModel
{
    use CrudTrait;

    protected $table = 'role_has_menu_access';
    protected $primaryKey = ['user_id'];
    protected $guarded = ['created_at','updated_at'];
    protected $fillable = ['role_id','user_id','menu_item_id','display_order','parent_id','depth'];
    public $incrementing = false;

    protected function setKeysForSaveQuery($query)
    {
        $query
            ->where('user_id', '=', $this->getAttribute('user_id'))
            ->where('menu_item_id', '=', $this->getAttribute('menu_item_id'));
        return $query;
    }
}
