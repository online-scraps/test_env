<?php

namespace App\Models;

use App\Base\BaseModel;
use App\Models\RoleHasMenuAccess;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class MenuItem extends BaseModel
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'menu_items';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = ['name_en', 'name_lc', 'model_name', 'type', 'link', 'parent_id', 'sup_org_id','display_name','icon_picker','store_id'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    /**
     * Get all menu items, in a hierarchical collection.
     * Only supports 2 levels of indentation.
     */
    public static function getTree($model_names)
    {
        $menu = self::menulist($model_names);

        if (count($menu) > 0) {
            foreach ($menu as $menu_item) {
                $menu_item->children = collect([]);

                foreach ($menu as $i => $menu_subitem) {
                    if ($menu_subitem->parent_id == $menu_item->id) {
                        $menu_item->children->push($menu_subitem);
                        unset($menu[$i]);
                    }
                }

                $menu_item->children = $menu_item->children->sortBy('lft');
            }
        }
        $menu = $menu->sortBy('lft');
        return $menu;
    }

    public static function menulist($model_names)
    {
        $menu = [];

        $user_id = backpack_user()->id;
        $menu_from_role = RoleHasMenuAccess::where('user_id', '=', $user_id)->orderBy('menu_item_id')->get();
        $menu_item_id = array_column($menu_from_role->toArray(), 'menu_item_id');

        if (!$menu_from_role->isEmpty()) {
            $menu = self::whereIn('id', $menu_item_id)->get();
            foreach ($menu as $key => $m) {
                $m->lft = $menu_from_role[$key]->display_order;
                $m->parent_id = $menu_from_role[$key]->parent_id;
                $m->depth = $menu_from_role[$key]->depth;
            }
        } else {
            $menu = self::whereIn('model_name', $model_names)->orderBy('lft')->get();

            $parent_ids = $menu->pluck('parent_id')->toArray();
            $parents = self::whereIn('id', array_unique($parent_ids))->get();

            $menu = $menu->merge($parents);
        }
        return $menu;
    }

    public function url()
    {
        switch ($this->type) {
            case 'external_link':
                return $this->link;
                break;

            default: //internal link
                return is_null($this->link) ? '#' : url($this->link);
                break;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id', 'id');
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
