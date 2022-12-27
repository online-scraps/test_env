<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeriesNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->terminal_list();
    }

    public function terminal_list()
    {
        // DB::table('terminals')->insert([
        //     ['id' => 1, 'code' => 1, 'name_lc' => 'Opening Balance Voucher', 'name_en' => 'Opening Balance Voucher'],
        //     ['id' => 2, 'code' => 2, 'name_lc' => 'Journal Voucher', 'name_en' => 'Journal Voucher'],
        //     ['id' => 3, 'code' => 3, 'name_lc' => 'Receipt Voucher', 'name_en' => 'Receipt Voucher'],
        //     ['id' => 4, 'code' => 4, 'name_lc' => 'Payment Voucher', 'name_en' => 'Payment Voucher'],
        //     ['id' => 5, 'code' => 5, 'name_lc' => 'Purchase Voucher', 'name_en' => 'Purchase Voucher'],
        //     ['id' => 6, 'code' => 6, 'name_lc' => 'Sales Voucher', 'name_en' => 'Sales Voucher'],
        // ]);
    }
}
