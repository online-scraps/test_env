<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $now = Carbon::now()->toDateTimeString();
        // $this->time = $now;
        // $this->call(Menu::class);
        $this->call(RoleTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(DateSettingSeeder::class);
        $this->call(MasterTablesSeeder::class);
        $this->call(PrimaryMasterSeeder::class);
        $this->call(GeneralLedgerTableSeeder::class);
        $this->call(ChartsOfAccountsTableSeeder::class);
        $this->call(MstFiscalYearSeeder::class);
        // $this->call(SeriesNumberSeeder::class);
        $this->call(MasterDataSeeder::class);
        $this->call(SeriesNumberTableSeeder::class);
        $this->call(ChartsOfAccountGroupAddition::class);
//        $this->call(ItemSeeder::class);
//        $this->call(TestDataSeeder::class);

    }
}
