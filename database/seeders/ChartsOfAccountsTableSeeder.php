<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChartsOfAccountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('charts_of_accounts')->insert([
                ['id' => 1, 'name' => 'Asset', 'group_id' => NULL, 'is_group' => true, 'sup_org_id' => 1, 'created_by' => 1],
                ['id' => 2, 'name' => 'Liabilities', 'group_id' => NULL, 'is_group' => true, 'sup_org_id' => 1, 'created_by' => 1],
                ['id' => 3, 'name' => 'Equity', 'group_id' => NULL, 'is_group' => true, 'sup_org_id' => 1, 'created_by' => 1],
                ['id' => 4, 'name' => 'Income', 'group_id' => NULL, 'is_group' => true, 'sup_org_id' => 1, 'created_by' => 1],
                ['id' => 5, 'name' => 'Expences', 'group_id' => NULL, 'is_group' => true, 'sup_org_id' => 1, 'created_by' => 1],
        ]);
        DB::statement("SELECT SETVAL('charts_of_accounts_id_seq',100)");
    }
}
