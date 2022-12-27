<?php

namespace  App\Base\Traits;

use ReflectionClass;
use App\Models\MstStore;
use App\Models\StockEntries;
use App\Base\DataAccessPermission;
use Illuminate\Support\Facades\DB;
use App\Models\BatchQuantityDetail;
use Illuminate\Support\Facades\Schema;


/**
 *  CheckPermission
 */
trait UserLevelFilter
{
    public function filterListByUserLevel()
    {

        // dd(backpack_user()->user_level,config('users.user_level.store_admin'),isset($this->crud->store_flag));
        switch (backpack_user()->user_level) {
            case config('users.user_level.organization_user'):
                $this->crud->query->whereSupOrgId(backpack_user()->sup_org_id);    
                break;
            case config('users.user_level.store_user'):
                if(!isset($this->crud->store_flag)){
                    $this->crud->query->whereSupOrgId(backpack_user()->sup_org_id);
                }else{
                    $this->crud->query->whereSupOrgId(backpack_user()->sup_org_id)->whereStoreId(backpack_user()->store_id);
                }
                break;
            case config('users.user_level.store_admin'):
                $ids = $this->getChildStoreIds();
                if(empty($ids)){
                    if(!isset($this->crud->store_flag)){
                        $this->crud->query->whereSupOrgId(backpack_user()->sup_org_id);
                    }else{
                        $this->crud->query->whereSupOrgId(backpack_user()->sup_org_id)->whereStoreId(backpack_user()->store_id);
                    }

                }else{
                    if(!isset($this->crud->store_flag)){
                        $this->crud->query->whereSupOrgId(backpack_user()->sup_org_id)->whereIn('store_id',$ids);                    
                    }else{
                        $this->crud->query->whereSupOrgId(backpack_user()->sup_org_id);                    
                    }
                }

                break;
            default:
        }
    }

    public function getChildStoreIds(){
        // Get Child Store
        $ids = [];
        $child_store = DB::select(DB::raw("with recursive f_data as(
                select * from mst_stores
                where id = ?
                union all
                select mst_stores.* from mst_stores
                join f_data on mst_stores.parent_id = f_data.id
            )select id
            from f_data
                order by id"),[backpack_user()->store_id]);
            foreach($child_store as $id){
                $ids[] = $id->id;
            }

        return $ids;
    }

    public function getFilteredStoreList()
    {

        switch (backpack_user()->user_level)
        {
            case config('users.user_level.super_user'):
                return MstStore::where('is_active', true)
                    ->pluck('name_en','id');

            case config('users.user_level.organization_user'):
                return MstStore::where('is_active', true)
                    ->whereSupOrgId(backpack_user()->sup_org_id)
                    ->pluck('name_en','id');
            case config('users.user_level.store_admin'):
                $store_list = MstStore::where('is_active', true)
                    ->whereSupOrgId(backpack_user()->sup_org_id)
                    ->pluck('name_en','id');

                return $store_list;

            case config('users.user_level.store_user'):
                return MstStore::where('is_active', true)
                    ->whereId(backpack_user()->store_id)
                    ->pluck('name_en','id');

        }

    }

    public function getFilteredBatchList()
    {

        switch (backpack_user()->user_level)
        {
            case config('users.user_level.super_user'):
                return BatchQuantityDetail::where('batch_from','stock-mgmt')
                    ->pluck('batch_no','batch_no');

            case config('users.user_level.organization_user'):
                return BatchQuantityDetail::where('batch_from','stock-mgmt')
                    ->whereSupOrgId(backpack_user()->sup_org_id)
                    ->pluck('batch_no','batch_no');
            case config('users.user_level.store_admin'):
                return BatchQuantityDetail::where('batch_from','stock-mgmt')
                    ->whereSupOrgId(backpack_user()->sup_org_id)
                    ->whereStoreId(backpack_user()->store_id)
                    ->pluck('batch_no','batch_no');

            case config('users.user_level.store_user'):
                return BatchQuantityDetail::where('batch_from','stock-mgmt')
                    ->whereStoreId(backpack_user()->store_id)
                    ->pluck('batch_no','batch_no');

        }

    }
}
