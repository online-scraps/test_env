<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralLedgerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('general_ledgers')->insert([
            // level 1
            ['id' => 1, 'name' => 'Asset', 'levels' => '0', 'category' => 1, 'gl_code' => 10000000001,'parent_id' => NULL, 'created_by' => 1],
            ['id' => 2, 'name' => 'Liabilities', 'levels' => '0', 'category' => 2, 'gl_code' => 10000000002,'parent_id' => NULL, 'created_by' => 1],
            ['id' => 3, 'name' => 'Equity', 'levels' => '0', 'category' => 3, 'gl_code' => 10000000003,'parent_id' => NULL, 'created_by' => 1],
            ['id' => 4, 'name' => 'Income', 'levels' => '0', 'category' => 4, 'gl_code' => 10000000004,'parent_id' => NULL, 'created_by' => 1],
            ['id' => 5, 'name' => 'Expences', 'levels' => '0', 'category' => 5, 'gl_code' => 10000000005,'parent_id' => NULL, 'created_by' => 1],

            // // level 2
            // ['id' => 6, 'name' => 'Asset Child', 'parent_id' => 5,'created_by' => 1],
            // ['id' => 7, 'name' => 'Asset Child', 'parent_id' => 5,'created_by' => 1],
            // ['id' => 8, 'name' => 'Asset Child', 'parent_id' => 5,'created_by' => 1],

            // // level 3
            // ['id' => 9, 'name' => 'Asset Child Child', 'parent_id' => 6,'created_by' => 1],
            // ['id' => 10, 'name' => 'Asset Child Child', 'parent_id' => 6,'created_by' => 1],
            // ['id' => 11, 'name' => 'Asset Child Child', 'parent_id' => 6,'created_by' => 1],

            // // level 4
            // ['id' => 12, 'name' => 'Asset Child Child', 'parent_id' => 11,'created_by' => 1],

            // // level 5
            // ['id' => 13, 'name' => 'Asset Child Child', 'parent_id' => 12,'created_by' => 1],

            // // level 6
            // ['id' => 14, 'name' => 'Asset Child Child', 'parent_id' => 13,'created_by' => 1],
            
            // // level7
            // ['id' => 15, 'name' => 'Asset Child Child', 'parent_id' => 14,'created_by' => 1],


        ]);
        DB::statement("SELECT SETVAL('general_ledgers_id_seq',100)");
    }
}
