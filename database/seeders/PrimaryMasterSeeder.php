<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class PrimaryMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $now = Carbon::now()->toDateTimeString();

        // DB::table('mst_brands')->insert([
        //     ['id' => 1, 'code' => '01', 'name_en' => 'Dahua', 'name_lc' => ' Dahua', 'is_active' => 'true', 'sup_org_id' => '1', 'created_at' => $now],
        // ]);

        DB::statement("SELECT SETVAL('mst_brands_id_seq',100)");

        DB::table('purchase_order_types')->insert([
            ['id' => 1, 'code' => '01', 'name_en' => 'Regular PO', 'name_lc' => ' Regular PO', 'is_active' => 'true', 'created_at' => $now],
            ['id' => 2, 'code' => '02', 'name_en' => 'Stock Transfer PO', 'name_lc' => ' Stock Transfer PO', 'is_active' => 'true', 'created_at' => $now],
            ['id' => 3, 'code' => '03', 'name_en' => 'Warehouse PO', 'name_lc' => 'Warehouse PO', 'is_active' => 'true', 'created_at' => $now],
        ]);
        DB::statement("SELECT SETVAL('purchase_order_types_id_seq',100)");

        DB::table('grn_types')->insert([
            ['id' => 1,'code' => '01', 'name_en' => 'Regular GRN', 'name_lc' => ' Regular GRN', 'is_active' => 'true', 'sup_org_id' => '1','created_at'=>$now,'updated_at'=>$now],
            ['id' => 2,'code' => '02', 'name_en' => 'Stock Transfer GRN', 'name_lc' => ' Stock Transfer GRN', 'is_active' => 'true', 'sup_org_id' => '1','created_at'=>$now,'updated_at'=>$now],
            ['id' => 3,'code' => '03', 'name_en' => 'Warehouse GRN', 'name_lc' => 'Warehouse GRN', 'is_active' => 'true', 'sup_org_id' => '1','created_at'=>$now,'updated_at'=>$now],
        ]);

        DB::statement("SELECT SETVAL('grn_types_id_seq',100)");

        DB::table('mst_discount_modes')->insert([
            ['id' => 1, 'code' => '01', 'name_en' => '%', 'name_lc' => ' %', 'is_active' => 'true',  'created_at' => $now, 'is_super_data' => true],
            ['id' => 2, 'code' => '02', 'name_en' => 'NRS', 'name_lc' => ' NRS', 'is_active' => 'true',  'created_at' => $now, 'is_super_data' => true],
        ]);
        DB::statement("SELECT SETVAL('mst_discount_modes_id_seq',100)");

        DB::table('mst_units')->insert([
            ['id' => 1, 'code' => '01', 'name_en' => 'pcs', 'name_lc' => ' pcs', 'is_active' => 'true', 'sup_org_id' => '1', 'created_at' => $now, 'is_super_data' => true],
            ['id' => 2, 'code' => '02', 'name_en' => 'grm', 'name_lc' => ' gm', 'is_active' => 'true', 'sup_org_id' => '1', 'created_at' => $now, 'is_super_data' => true],
            ['id' => 3, 'code' => '03', 'name_en' => 'kg', 'name_lc' => ' kg', 'is_active' => 'true', 'sup_org_id' => '1', 'created_at' => $now, 'is_super_data' => true],
        ]);
        DB::statement("SELECT SETVAL('mst_units_id_seq',100)");

        DB::table('mst_stores')->insert([
            ['id' => 1, 'code' => '01', 'name_en' => 'Bidhtore', 'name_lc' => ' BidhStore', 'is_active' => 'true', 'sup_org_id' => '1', 'created_by' => '1', 'address' => 'ktm', 'email' => 'store1@gmail.com', 'description' => 'description', 'phone_no' => '9849562321', 'created_at' => $now],
        ]);
        DB::statement("SELECT SETVAL('mst_stores_id_seq',100)");

        DB::table('sup_status')->insert([
            array('id' => 1, 'code' => '1', 'name_en' => 'created', 'created_at' => $now),
            array('id' => 2, 'code' => '2', 'name_en' => 'approved', 'created_at' => $now),
            array('id' => 3, 'code' => '3', 'name_en' => 'cancelled', 'created_at' => $now),
            array('id' => 4, 'code' => '4', 'name_en' => 'partial_return', 'created_at' => $now),
            array('id' => 5, 'code' => '5', 'name_en' => 'full_return', 'created_at' => $now),
        ]);

        DB::statement("SELECT SETVAL('sup_status_id_seq',100)");
    }
}
