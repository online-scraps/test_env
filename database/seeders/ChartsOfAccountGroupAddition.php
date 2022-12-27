<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChartsOfAccountGroupAddition extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('charts_of_accounts')->insert([
            ['id' => 6, 'name' => 'Fixed Asset', 'group_id' => 1, 'is_group' => true, 'sup_org_id' => 1, 'primary_group' => 1, 'created_by' => 1],
            ['id' => 7, 'name' => 'Current Asset', 'group_id' => 1, 'is_group' => true, 'sup_org_id' => 1, 'primary_group' => 1, 'created_by' => 1],
            ['id' => 8, 'name' => 'Cash', 'group_id' => 7, 'is_group' => true, 'sup_org_id' => 1, 'primary_group' => 0, 'created_by' => 1],
            ['id' => 9, 'name' => 'Bank', 'group_id' => 7, 'is_group' => true, 'sup_org_id' => 1, 'primary_group' => 0, 'created_by' => 1],

            ['id' => 10, 'name' => 'Current Liabilities', 'group_id' => 2, 'is_group' => true, 'sup_org_id' => 1, 'primary_group' => 0, 'created_by' => 1],
            ['id' => 11, 'name' => 'Long Term Liabilities', 'group_id' => 2, 'is_group' => true, 'sup_org_id' => 1, 'primary_group' => 0, 'created_by' => 1],
        ]);
        DB::statement("SELECT SETVAL('charts_of_accounts_id_seq',100)");
    }
}
