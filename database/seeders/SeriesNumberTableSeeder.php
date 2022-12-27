<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeriesNumberTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('series_number')->insert([
            // 1. opening balance voucher
            ['id' => 1, 'description' => 'Opening Balance', 'terminal_id' => 1, 'starting_word' => 'OBV', 'starting_no' => 1, 'sup_org_id' => 1],
            ['id' => 2, 'description' => 'OBV', 'terminal_id' => 1, 'starting_word' => null, 'starting_no' => 1, 'sup_org_id' => 1],

            // 2. journal voucher
            ['id' => 3, 'description' => 'Journal Voucher', 'terminal_id' => 2, 'starting_word' => 'JV', 'starting_no' => 1, 'sup_org_id' => 1],
            ['id' => 4, 'description' => 'JV', 'terminal_id' => 2, 'starting_word' => null, 'starting_no' => 1, 'sup_org_id' => 1],

            // 3. receipt voucher
            ['id' => 5, 'description' => 'Receipt Voucher', 'terminal_id' => 3, 'starting_word' => 'RV', 'starting_no' => 1, 'sup_org_id' => 1],
            ['id' => 6, 'description' => 'RV', 'terminal_id' => 3, 'starting_word' => null, 'starting_no' => 1, 'sup_org_id' => 1],

            // 4. payment voucher
            ['id' => 7, 'description' => 'Payment Voucher', 'terminal_id' => 4, 'starting_word' => 'PV', 'starting_no' => 1, 'sup_org_id' => 1],
            ['id' => 8, 'description' => 'PV', 'terminal_id' => 4, 'starting_word' => null, 'starting_no' => 1, 'sup_org_id' => 1],

            // 5. contra voucher
            ['id' => 9, 'description' => 'Contra Voucher', 'terminal_id' => 5, 'starting_word' => 'CV', 'starting_no' => 1, 'sup_org_id' => 1],
            ['id' => 10, 'description' => 'CV', 'terminal_id' => 5, 'starting_word' => null, 'starting_no' => 1, 'sup_org_id' => 1],

        ]);
        DB::statement("SELECT SETVAL('series_number_id_seq',100)");
    }
}
