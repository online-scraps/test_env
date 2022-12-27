<?php

namespace App\Models;

use App\Models\Role;
use App\Models\MstStore;
use App\Models\HrEmployee;
use App\Models\SupOrganization;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, CrudTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'sup_org_id',
        'employee_id',
        'phone',
        'is_active',
        'store_id',
        'is_due_approver',
        'is_discount_approver',
        'is_stock_approver',
        'is_po_approver',
        'user_level',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function employeeEntity()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id', 'id');
    }
    public function storeEntity()
    {
        return $this->belongsTo(MstStore::class, 'store_id', 'id');
    }
    public function superOrganizationEntity(){
        return $this->belongsTo(SupOrganization::class,'sup_org_id','id');
    }

    public static function getSystemUserId(){
        return SupOrganization::where('code','sys')->pluck('id')->first();
    }

    public function isSystemUser()
    {
        if(isset($this->sup_org_id) && ($this->superOrganizationEntity->code == 'sys')){
            return true;
        }
        else {
            return false;
        }
    }


    public function isStoreUser()
    {
        if(isset($this->store_id)){
            return true;
        }
        else {
            return false;
        }
    }

    public function isOrganizationUser(){
        if(isset($this->sup_org_id) && $this->sup_org_id != 1){
            return true;
        }else{
            return false;
        }
    }


    public function isOrgUser()
    {
        if(isset($this->sup_org_id) && ($this->sup_org_id != 1) && ($this->store_id == null)){
            return true;
        }else{
            return false;
        }
    }
    //assign role to user
    public function assignRoleCustom($role_name, $model_id){
        $roleModel = Role::where('name', $role_name)->first();
        if(!$roleModel){
            return "role doesnot exists";
        }else{
            DB::table('model_has_roles')->insert([
                'role_id' => $roleModel->id,
                'model_type' => 'App\Models\User',
                'model_id' => $model_id,
            ]);
        }

    }


}
