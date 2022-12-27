<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now()->toDateTimeString();

        DB::table('sup_organizations')->insert([
            array('id' => 1,'code'=>'sys','name_en' => 'System', 'email' => 'super@gmail.com', 'deleted_uq_code'=>1,'created_at'=>$now),

        ]);
        DB::statement("SELECT SETVAL('sup_organizations_id_seq',100)");


        DB::table('users')->insert([
            array('id' => 1,'sup_org_id'=>1, 'name' => 'System Admin', 'email' => 'super@gmail.com','user_level'=>config('users.user_level.super_user'),'password' => \Hash::make('Super@1234'),'deleted_uq_code'=>1,'created_at'=>$now),
        ]);

        DB::statement("SELECT SETVAL('users_id_seq',100)");


        //call artisan commands
        Artisan::call('generate:permissions');
        Artisan::call('disable:backpack_pro');

        $permissions = Permission::all();
        $super_admin_role = Role::find(1);

        $super_admin_role->givePermissionTo($permissions);

        //assign role for superadmin
        $user = User::findOrFail(1);
        $user->assignRoleCustom("superadmin", $user->id);








    }
}
