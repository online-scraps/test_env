<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstFiscalYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->mst_fiscal_year();
    }

    private function mst_fiscal_year(){
        DB::table('mst_fiscal_year')->insert([
            array('id' => 100,'code' => '2071/72','from_date_bs' => '2071/04/01','to_date_bs' => '2072/03/31','from_date_ad' => '2014-07-16','to_date_ad' => '2015-07-15' ),
            array('id' => 101,'code' => '2072/73','from_date_bs' => '2072/04/01','to_date_bs' => '2073/03/31','from_date_ad' => '2015-07-16','to_date_ad' => '2016-07-15' ),
            array('id' => 102,'code' => '2073/74','from_date_bs' => '2073/04/01','to_date_bs' => '2074/03/31','from_date_ad' => '2016-07-16','to_date_ad' => '2017-07-15' ),
            array('id' => 103,'code' => '2074/75','from_date_bs' => '2074/04/01','to_date_bs' => '2075/03/31','from_date_ad' => '2017-07-16','to_date_ad' => '2018-07-15' ),
            array('id' => 104,'code' => '2075/76','from_date_bs' => '2075/04/01','to_date_bs' => '2076/03/31','from_date_ad' => '2018-07-16','to_date_ad' => '2019-07-15' ),
            array('id' => 105,'code' => '2076/77','from_date_bs' => '2076/04/01','to_date_bs' => '2077/03/31','from_date_ad' => '2019-07-16','to_date_ad' => '2020-07-15' ),
            array('id' => 106,'code' => '2077/78','from_date_bs' => '2077/04/01','to_date_bs' => '2078/03/31','from_date_ad' => '2020-07-16','to_date_ad' => '2021-07-15' ),
            array('id' => 107,'code' => '2078/79','from_date_bs' => '2078/04/01','to_date_bs' => '2079/03/31','from_date_ad' => '2021-07-16','to_date_ad' => '2022-07-15' ),
            array('id' => 108,'code' => '2079/80','from_date_bs' => '2079/04/01','to_date_bs' => '2080/03/31','from_date_ad' => '2022-07-16','to_date_ad' => '2023-07-15' ),
            array('id' => 109,'code' => '2080/81','from_date_bs' => '2080/04/01','to_date_bs' => '2081/03/31','from_date_ad' => '2023-07-16','to_date_ad' => '2024-07-15' ),
            array('id' => 110,'code' => '2081/82','from_date_bs' => '2081/04/01','to_date_bs' => '2082/03/31','from_date_ad' => '2024-07-16','to_date_ad' => '2025-07-15' ),
            array('id' => 111,'code' => '2082/83','from_date_bs' => '2082/04/01','to_date_bs' => '2083/03/31','from_date_ad' => '2025-07-16','to_date_ad' => '2026-07-15' ),
            array('id' => 112,'code' => '2083/84','from_date_bs' => '2083/04/01','to_date_bs' => '2084/03/31','from_date_ad' => '2026-07-16','to_date_ad' => '2027-07-15' ),
            array('id' => 113,'code' => '2084/85','from_date_bs' => '2084/04/01','to_date_bs' => '2085/03/31','from_date_ad' => '2027-07-16','to_date_ad' => '2028-07-15' ),
            array('id' => 114,'code' => '2085/86','from_date_bs' => '2085/04/01','to_date_bs' => '2086/03/31','from_date_ad' => '2028-07-16','to_date_ad' => '2029-07-15' ),
            array('id' => 115,'code' => '2086/87','from_date_bs' => '2086/04/01','to_date_bs' => '2087/03/31','from_date_ad' => '2029-07-16','to_date_ad' => '2030-07-15' ),
            array('id' => 116,'code' => '2087/88','from_date_bs' => '2087/04/01','to_date_bs' => '2088/03/31','from_date_ad' => '2030-07-16','to_date_ad' => '2031-07-15' ),
            array('id' => 117,'code' => '2088/89','from_date_bs' => '2088/04/01','to_date_bs' => '2089/03/31','from_date_ad' => '2031-07-16','to_date_ad' => '2032-07-15' ),
            array('id' => 118,'code' => '2089/90','from_date_bs' => '2089/04/01','to_date_bs' => '2090/03/31','from_date_ad' => '2032-07-16','to_date_ad' => '2033-07-15' ),
        ]);
        DB::statement("SELECT SETVAL('mst_fiscal_year_id_seq',1000)");
    }
}
