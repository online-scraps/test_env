<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
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
            ['id' => 2, 'code' => '02', 'name_en' => 'Bidh Management', 'name_lc' => 'Bidh Management', 'country_id' => 1, 'province_id' => 1, 'district_id' => 1, 'address' => 'Test1', 'email' => 'bidh@gmail.com', 'phone_no' => '9813938445', 'multiple_barcode' => true, 'is_active' => 'true', 'created_by' => 1, 'created_at' => $now, 'deleted_uq_code' => 1],
        ]);
        DB::statement("SELECT SETVAL('sup_organizations_id_seq',100)");

        DB::table('mst_stores')->insert([
            ['id' => 1, 'code' => '01', 'name_en' => 'RamStore', 'name_lc' => ' RamStore', 'is_active' => 'true', 'sup_org_id' => '2', 'store_user_id' => '1', 'created_by' => '2', 'address' => 'ktm', 'email' => 'store1@gmail.com', 'description' => 'asdfghjkl', 'phone_no' => '9849562321', 'created_at' => $now],
            ['id' => 2, 'code' => '02', 'name_en' => 'ShamStore', 'name_lc' => ' ShamStore', 'is_active' => 'true', 'sup_org_id' => '2', 'store_user_id' => '1', 'created_by' => '2', 'address' => 'pokhara', 'email' => 'store2@gmail.com', 'description' => 'asdfghjkl', 'phone_no' => '9856232114', 'created_at' => $now],
            ['id' => 3, 'code' => '03', 'name_en' => 'HariStore', 'name_lc' => ' HariStore', 'is_active' => 'true', 'sup_org_id' => '2', 'store_user_id' => '1', 'created_by' => '2', 'address' => 'banepa', 'email' => 'store3@gmail.com', 'description' => 'asdfghjkl', 'phone_no' => '9878456512', 'created_at' => $now],
            ['id' => 4, 'code' => '04', 'name_en' => 'Gitatore', 'name_lc' => ' Gitatore', 'is_active' => 'true', 'sup_org_id' => '2', 'store_user_id' => '1', 'created_by' => '2', 'address' => 'ktm', 'dhangadi' => 'store3@gmail.com', 'description' => 'asdfghjkl', 'phone_no' => '9860985254', 'created_at' => $now],
            ['id' => 5, 'code' => '05', 'name_en' => 'SitaStore', 'name_lc' => ' SitaStore', 'is_active' => 'true', 'sup_org_id' => '2', 'store_user_id' => '1', 'created_by' => '2', 'address' => 'dhulikhel', 'email' => 'store4@gmail.com', 'description' => 'asdfghjkl', 'phone_no' => '989903430', 'created_at' => $now],
            ['id' => 6, 'code' => '06', 'name_en' => 'SandipStore', 'name_lc' => ' SandipStore', 'is_active' => 'true', 'sup_org_id' => '2', 'store_user_id' => '1', 'created_by' => '2', 'address' => 'dhadhing', 'email' => 'store5@gmail.com', 'description' => 'asdfghjkl', 'phone_no' => '9860708090', 'created_at' => $now],
        ]);
        DB::statement("SELECT SETVAL('mst_stores_id_seq',100)");

        DB::table('users')->insert([
            ['id' => 2, 'name' => 'bidh test', 'email' => 'bidh@test.com', 'password' => \Hash::make('123456'), 'user_level' => 2, 'phone' => '1542365215', 'is_active' => 'true', 'created_by' => 1, 'created_at' => $now, 'deleted_uq_code' => 1,'store_id'=>1,'is_po_approver'=>true, 'sup_org_id' => 2],
            ['id' => 3, 'name' => 'nani', 'email' => 'nani@gmail.com', 'password' => \Hash::make('123456'), 'user_level' => 3, 'phone' => '1542365215', 'is_active' => 'true', 'created_by' => 1, 'created_at' => $now, 'deleted_uq_code' => 1,'store_id'=>1,'is_po_approver'=>true, 'sup_org_id' => 2],
        ]);
        DB::statement("SELECT SETVAL('users_id_seq',100)");

        DB::table('mst_categories')->insert([
            ['id' => 1, 'code' => '01', 'name_en' => 'Camera', 'name_lc' => ' Camera', 'is_active' => 'true', 'sup_org_id' => '2', 'created_at' => $now],
            ['id' => 2, 'code' => '02', 'name_en' => 'DVR/XVR', 'name_lc' => ' DVR/XVR', 'is_active' => 'true', 'sup_org_id' => '2', 'created_at' => $now],
            ['id' => 3, 'code' => '03', 'name_en' => 'POE Switch', 'name_lc' => ' POE Switch', 'is_active' => 'true', 'sup_org_id' => '2', 'created_at' => $now],
            ['id' => 4, 'code' => '04', 'name_en' => 'NVR', 'name_lc' => ' NVR', 'is_active' => 'true', 'sup_org_id' => '2', 'created_at' => $now],
            ['id' => 5, 'code' => '05', 'name_en' => 'Storage Device', 'name_lc' => ' Storage Device', 'is_active' => 'true', 'sup_org_id' => '2', 'created_at' => $now],
            ['id' => 6, 'code' => '06', 'name_en' => 'Monitor', 'name_lc' => ' Monitor', 'is_active' => 'true', 'sup_org_id' => '2', 'created_at' => $now],
        ]);

        DB::table('mst_subcategories')->insert([
            ['id' => 1, 'code' => '01', 'category_id' => 1, 'name_en' => 'HDCVI ANALOG CAMERA DOME', 'name_lc' => ' HDCVI ANALOG CAMERA DOME',   'sup_org_id' => '2', 'is_active' => 'true', 'created_at' => $now],
            ['id' => 2, 'code' => '01', 'category_id' => 1, 'name_en' => 'HDCVI ANALOG CAMERA BULLET', 'name_lc' => 'HDCVI ANALOG CAMERA BULLET',   'sup_org_id' => '2', 'is_active' => 'true', 'created_at' => $now],
            ['id' => 3, 'code' => '01', 'category_id' => 2, 'name_en' => 'XVR/DVR', 'name_lc' => ' XVR/DVR',   'sup_org_id' => '2', 'is_active' => 'true', 'created_at' => $now],
            ['id' => 4, 'code' => '01', 'category_id' => 1, 'name_en' => 'IP CAMERA DOME', 'name_lc' => 'IP CAMERA DOME',   'sup_org_id' => '2', 'is_active' => 'true', 'created_at' => $now],
            ['id' => 5, 'code' => '01', 'category_id' => 1, 'name_en' => 'IP CAMERA BULLET', 'name_lc' => ' IP CAMERA BULLET',   'sup_org_id' => '2', 'is_active' => 'true', 'created_at' => $now],
            ['id' => 6, 'code' => '01', 'category_id' => 1, 'name_en' => 'PTZ CAMERA', 'name_lc' => ' PTZ CAMERA',   'sup_org_id' => '2', 'is_active' => 'true', 'created_at' => $now],
            ['id' => 7, 'code' => '01', 'category_id' => 4, 'name_en' => 'NVR', 'name_lc' => 'NVR',   'sup_org_id' => '2', 'is_active' => 'true', 'created_at' => $now],
            ['id' => 8, 'code' => '01', 'category_id' => 3, 'name_en' => 'POE SWITCH', 'name_lc' => 'POE SWITCH',   'sup_org_id' => '2', 'is_active' => 'true', 'created_at' => $now],
            ['id' => 9, 'code' => '01', 'category_id' => 5, 'name_en' => 'HARD DISK DRIVE', 'name_lc' => ' HARD DISK DRIVE',   'sup_org_id' => '2', 'is_active' => 'true', 'created_at' => $now],
            ['id' => 10, 'code' => '01', 'category_id' => 6, 'name_en' => 'LCD MONITOR', 'name_lc' => ' LCD MONITOR',   'sup_org_id' => '2', 'is_active' => 'true', 'created_at' => $now],

        ]);
        DB::statement("SELECT SETVAL('mst_subcategories_id_seq',100)");


        DB::table('mst_suppliers')->insert([
            ['id' => 1, 'code' => '01', 'name_en' => 'STRSupplier', 'name_lc' => ' STRSupplier',  'country_id' => '1', 'contact_person' => '0123456789', 'contact_number' => '1234567890', 'district_id' => '1', 'province_id' => '1', 'address' => 'pepsicola', 'email' => 'admin1@gmail.com', 'is_active' => 'true', 'sup_org_id' => '2', 'created_at' => $now],
            ['id' => 2, 'code' => '02', 'name_en' => 'ABCSupplier', 'name_lc' => ' ABCSupplier',  'country_id' => '1', 'contact_person' => '9841502356', 'contact_number' => '9841502356', 'district_id' => '2', 'province_id' => '2', 'address' => 'newroad', 'email' => 'admin2@gmail.com', 'is_active' => 'true', 'sup_org_id' => '2', 'created_at' => $now],
            ['id' => 3, 'code' => '03', 'name_en' => 'PORSupplier', 'name_lc' => ' PORSupplier',  'country_id' => '1', 'contact_person' => '9841502356', 'contact_number' => '98750236956', 'district_id' => '1', 'province_id' => '3', 'address' => 'ktm', 'email' => 'admin3@gmail.com', 'is_active' => 'true', 'sup_org_id' => '2', 'created_at' => $now],
            ['id' => 4, 'code' => '04', 'name_en' => 'XYZSupplier', 'name_lc' => ' XYZSupplier',  'country_id' => '1', 'contact_person' => '9843202356', 'contact_number' => '9841502356', 'district_id' => '1', 'province_id' => '4', 'address' => 'kupandol', 'email' => 'admin4@gmail.com', 'is_active' => 'true', 'sup_org_id' => '2', 'created_at' => $now],
            ['id' => 5, 'code' => '05', 'name_en' => 'AbinSupplier', 'name_lc' => ' AbinSupplier',  'country_id' => '1', 'contact_person' => '9843202356', 'contact_number' => '9856321478', 'district_id' => '1', 'province_id' => '5', 'address' => 'Thapathali', 'email' => 'admin5@gmail.com', 'is_active' => 'true', 'sup_org_id' => '2', 'created_at' => $now],
        ]);
        DB::statement("SELECT SETVAL('mst_suppliers_id_seq',100)");

        DB::table('mst_brands')->insert([
            ['id' => 2, 'code' => '02', 'name_en' => 'TestBrand', 'name_lc' => ' TestBrand', 'is_active' => 'true', 'sup_org_id' => '2', 'created_at' => $now],
        ]);
        DB::statement("SELECT SETVAL('mst_brands_id_seq',100)");

        DB::table('mst_items')->insert([
            // Normal Asset
            ['id' => 60, 'code' => '60', 'name' => 'Item60', 'asset_type_id' => null, 'category_id' => 1, 'subcategory_id' => 1, 'supplier_id' => 1, 'brand_id' => 2, 'unit_id' => 1, 'tax_vat' => 13, 'is_active' => true, 'sup_org_id' => 2, 'is_damaged' => false, 'is_taxable' => true, 'is_nonclaimable' => false, 'is_staffdiscount' => true, 'is_price_editable' => false, 'created_by' => 2, 'created_at' => $now, 'is_fixed_asset' => false],
            ['id' => 61, 'code' => '61', 'name' => 'Item61', 'asset_type_id' => null, 'category_id' => 2, 'subcategory_id' => 1, 'supplier_id' => 1, 'brand_id' => 2, 'unit_id' => 1, 'tax_vat' => 13, 'is_active' => true, 'sup_org_id' => 2, 'is_damaged' => false, 'is_taxable' => true, 'is_nonclaimable' => false, 'is_staffdiscount' => true, 'is_price_editable' => false, 'created_by' => 2, 'created_at' => $now, 'is_fixed_asset' => false],
            ['id' => 62, 'code' => '62', 'name' => 'Item62', 'asset_type_id' => null, 'category_id' => 3, 'subcategory_id' => 1, 'supplier_id' => 1, 'brand_id' => 2, 'unit_id' => 1, 'tax_vat' => 13, 'is_active' => true, 'sup_org_id' => 2, 'is_damaged' => false, 'is_taxable' => true, 'is_nonclaimable' => false, 'is_staffdiscount' => true, 'is_price_editable' => false, 'created_by' => 2, 'created_at' => $now, 'is_fixed_asset' => false],
            ['id' => 63, 'code' => '63', 'name' => 'Item63', 'asset_type_id' => null, 'category_id' => 1, 'subcategory_id' => 1, 'supplier_id' => 1, 'brand_id' => 2, 'unit_id' => 1, 'tax_vat' => 13, 'is_active' => true, 'sup_org_id' => 2, 'is_damaged' => false, 'is_taxable' => true, 'is_nonclaimable' => false, 'is_staffdiscount' => true, 'is_price_editable' => false, 'created_by' => 2, 'created_at' => $now, 'is_fixed_asset' => false],
            ['id' => 64, 'code' => '64', 'name' => 'Item64', 'asset_type_id' => null, 'category_id' => 1, 'subcategory_id' => 1, 'supplier_id' => 1, 'brand_id' => 2, 'unit_id' => 1, 'tax_vat' => 13, 'is_active' => true, 'sup_org_id' => 2, 'is_damaged' => false, 'is_taxable' => true, 'is_nonclaimable' => false, 'is_staffdiscount' => true, 'is_price_editable' => false, 'created_by' => 2, 'created_at' => $now, 'is_fixed_asset' => false],
            ['id' => 65, 'code' => '65', 'name' => 'Item65', 'asset_type_id' => null, 'category_id' => 4, 'subcategory_id' => 1, 'supplier_id' => 1, 'brand_id' => 2, 'unit_id' => 1, 'tax_vat' => 13, 'is_active' => true, 'sup_org_id' => 2, 'is_damaged' => false, 'is_taxable' => true, 'is_nonclaimable' => false, 'is_staffdiscount' => true, 'is_price_editable' => false, 'created_by' => 2, 'created_at' => $now, 'is_fixed_asset' => false],
            ['id' => 66, 'code' => '66', 'name' => 'Item66', 'asset_type_id' => null, 'category_id' => 1, 'subcategory_id' => 1, 'supplier_id' => 1, 'brand_id' => 2, 'unit_id' => 1, 'tax_vat' => 13, 'is_active' => true, 'sup_org_id' => 2, 'is_damaged' => false, 'is_taxable' => true, 'is_nonclaimable' => false, 'is_staffdiscount' => true, 'is_price_editable' => false, 'created_by' => 2, 'created_at' => $now, 'is_fixed_asset' => false],
            ['id' => 67, 'code' => '67', 'name' => 'Item67', 'asset_type_id' => null, 'category_id' => 1, 'subcategory_id' => 1, 'supplier_id' => 1, 'brand_id' => 2, 'unit_id' => 1,  'tax_vat' => 13, 'is_active' => true, 'sup_org_id' => 2, 'is_damaged' => false, 'is_taxable' => true, 'is_nonclaimable' => false, 'is_staffdiscount' => true, 'is_price_editable' => false, 'created_by' => 2, 'created_at' => $now, 'is_fixed_asset' => false],
            ['id' => 68, 'code' => '68', 'name' => 'Item68', 'asset_type_id' => 1,'category_id' => null, 'subcategory_id' => null, 'supplier_id' => 1, 'brand_id' => 2, 'unit_id' => 1,  'tax_vat' => 13, 'is_active' => true, 'sup_org_id' => 2, 'is_damaged' => false, 'is_taxable' => true, 'is_nonclaimable' => false, 'is_staffdiscount' => true, 'is_price_editable' => false, 'created_by' => 2, 'created_at' => $now, 'is_fixed_asset' => true],
            ['id' => 69, 'code' => '69', 'name' => 'Item69', 'asset_type_id' => 1,'category_id' => null, 'subcategory_id' => null, 'supplier_id' => 1, 'brand_id' => 2, 'unit_id' => 1,  'tax_vat' => 13, 'is_active' => true, 'sup_org_id' => 2, 'is_damaged' => false, 'is_taxable' => true, 'is_nonclaimable' => false, 'is_staffdiscount' => true, 'is_price_editable' => false, 'created_by' => 2, 'created_at' => $now, 'is_fixed_asset' => true],
        ]);
        DB::statement("SELECT SETVAL('mst_items_id_seq',100)");

        DB::table('mst_item_stores')->insert([
           ['id'=>1,'store_id'=>1,'item_id'=>60],
           ['id'=>2,'store_id'=>1,'item_id'=>61],
           ['id'=>3,'store_id'=>1,'item_id'=>62],
           ['id'=>4,'store_id'=>1,'item_id'=>63],
           ['id'=>5,'store_id'=>1,'item_id'=>64],
           ['id'=>6,'store_id'=>1,'item_id'=>65],
           ['id'=>7,'store_id'=>1,'item_id'=>66],
           ['id'=>8,'store_id'=>1,'item_id'=>67],
        ]);
        DB::statement("SELECT SETVAL('mst_item_stores_id_seq',100)");

        DB::table('invoice_sequences')->insert([
           ['id'=>10,'code'=>10,'name_en'=>'test inv','sequence_code'=>'INV','sup_org_id'=>2,'created_by'=>2],
        ]);
        DB::statement("SELECT SETVAL('invoice_sequences_id_seq',100)");

        DB::table('purchase_return_sequences')->insert([
           ['id'=>10,'code'=>10,'name_en'=>'test prs','sequence_code'=>'PR','sup_org_id'=>2,'created_by'=>2],
        ]);
        DB::statement("SELECT SETVAL('purchase_return_sequences_id_seq',100)");

        DB::table('po_sequences')->insert([
           ['id'=>10,'code'=>10,'name_en'=>'test po','sequence_code'=>'PO','sup_org_id'=>2,'created_by'=>2],
        ]);
        DB::statement("SELECT SETVAL('po_sequences_id_seq',100)");

        DB::table('grn_sequences')->insert([
           ['id'=>10,'code'=>10,'name_en'=>'test grn','sequence_code'=>'GRN','sup_org_id'=>2,'created_by'=>2],
        ]);
        DB::statement("SELECT SETVAL('grn_sequences_id_seq',100)");

        DB::table('stock_adjustment_no')->insert([
           ['id'=>10,'code'=>10,'name_en'=>'test STCADJ','sequence_code'=>'STCADJ','sup_org_id'=>2,'created_by'=>2],
        ]);
        DB::statement("SELECT SETVAL('stock_adjustment_no_id_seq',100)");

        DB::table('purchase_order_details')->insert([
            ['id' => 60, 'purchase_order_num' => 'Po-0', 'po_date' => $now, 'expected_delivery' => $now, 'supplier_id' => 1, 'approved_by' => 3, 'gross_amt' => 1000, 'discount_amt' => 22, 'tax_amt' => 132, 'other_charges' => 1, 'net_amt' => 879, 'sup_org_id' => 2, 'comments' => "asd", 'created_by' => 3, 'purchase_order_type_id' => 1, 'store_id'=>1,'status_id' => 1, 'created_at' => $now],
            ['id' => 61, 'purchase_order_num' => 'Po-1', 'po_date' => $now, 'expected_delivery' => $now, 'supplier_id' => 1, 'approved_by' => 3, 'gross_amt' => 1000, 'discount_amt' => 122, 'tax_amt' => 132, 'other_charges' => 1, 'net_amt' => 879, 'sup_org_id' => 2, 'comments' => "sfsdfsdf", 'created_by' => 3, 'purchase_order_type_id' => 1, 'store_id'=>1,'status_id' => 2, 'created_at' => $now],
            ['id' => 62, 'purchase_order_num' => 'Po-2', 'po_date' => $now, 'expected_delivery' => $now, 'supplier_id' => 1, 'approved_by' => 3, 'gross_amt' => 1000, 'discount_amt' => 2, 'tax_amt' => 132, 'other_charges' => 1, 'net_amt' => 879, 'sup_org_id' => 2, 'comments' => "sdfwsfsfs", 'created_by' => 3, 'purchase_order_type_id' => 1, 'store_id'=>1,'status_id' => 2, 'created_at' => $now],
        ]);
        DB::statement("SELECT SETVAL('purchase_order_details_id_seq',100)");
        DB::table('purchase_items')->insert([
            ['id' => 60, 'po_id' => 60, 'purchase_qty' => 55, 'free_qty' => 15, 'total_qty' => 70, 'discount' => 10, 'purchase_price' => 56, 'tax_vat' => 13, 'item_amount' => 2000, 'items_id' => 61, 'sup_org_id' => 2, 'created_by' => 3,  'created_at' => $now],
            ['id' => 61, 'po_id' => 60, 'purchase_qty' => 12, 'free_qty' => 8, 'total_qty' => 20, 'discount' => 20, 'purchase_price' => 343, 'tax_vat' => 13, 'item_amount' => 2000, 'items_id' => 62, 'sup_org_id' => 2, 'created_by' => 3,  'created_at' => $now],
            ['id' => 62, 'po_id' => 60, 'purchase_qty' => 70, 'free_qty' => 10, 'total_qty' => 80,  'discount' => 1000, 'purchase_price' => 223, 'tax_vat' => 13, 'item_amount' => 2000, 'items_id' => 63, 'sup_org_id' => 2,  'created_by' => 3, 'created_at' => $now],
            ['id' => 63, 'po_id' => 61, 'purchase_qty' => 123, 'free_qty' => 83, 'total_qty' => 203, 'discount' => 20, 'purchase_price' => 343, 'tax_vat' => 13, 'item_amount' => 2000, 'items_id' => 62, 'sup_org_id' => 2, 'created_by' => 3,  'created_at' => $now],
            ['id' => 64, 'po_id' => 62, 'purchase_qty' => 70, 'free_qty' => 10, 'total_qty' => 80,  'discount' => 1000, 'purchase_price' => 223, 'tax_vat' => 13, 'item_amount' => 2000, 'items_id' => 63, 'sup_org_id' => 2,  'created_by' => 3, 'created_at' => $now],
        ]);
        DB::statement("SELECT SETVAL('purchase_items_id_seq',100)");

        DB::table('batch_no')->insert([
            ['id' => 60, 'code' => 60, 'name_en' => 'b1', 'created_by' => 3, 'deleted_uq_code' => 1, 'sup_org_id' => 2,'is_active'=>true, 'created_at' => $now],
            ['id' => 61, 'code' => 61, 'name_en' => 'b2', 'created_by' => 3, 'deleted_uq_code' => 1, 'sup_org_id' => 2,'is_active'=>true, 'created_at' => $now],
        ]);
        DB::statement("SELECT SETVAL('batch_no_id_seq',100)");
        DB::table('grns')->insert([
            ['id' => 60, 'purchase_order_id' => 60, 'invoice_no' => '60', 'created_by' => 3, 'deleted_uq_code' => 1, 'sup_org_id' => 2, 'created_at' => $now, 'grn_no' => 23, 'approved_by' => 3, 'gross_amt' => 1000, 'discount_amt' => 40, 'tax_amt' => 60, 'other_charges' => 40, 'net_amt' => 1200, 'comments' => 'hello comments', 'store_id' => 1, 'supplier_id' => 1, 'grn_type_id' => 1, 'status_id' => 2],
            ['id' => 61, 'purchase_order_id' => 61, 'invoice_no' => '61', 'created_by' => 3, 'deleted_uq_code' => 1, 'sup_org_id' => 2, 'created_at' => $now, 'grn_no' => 223, 'approved_by' => 3, 'gross_amt' => 10200, 'discount_amt' => 40, 'tax_amt' => 620, 'other_charges' => 420, 'net_amt' => 12200, 'comments' => 'hello comments61', 'store_id' => 1, 'supplier_id' => 1, 'grn_type_id' => 1, 'status_id' => 1],
        ]);
        DB::statement("SELECT SETVAL('grns_id_seq',100)");

        // DB::table('grn_items')->insert([
        //     ['id' => 1, 'purchase_qty' => 10, 'free_qty' => 60, 'mst_items_id' => 61, 'received_qty' => 70, 'deleted_uq_code' => 1, 'sup_org_id' => 2, 'created_at' => $now, 'invoice_qty' => 70, 'total_qty' => 100, 'batch_no' => 'b1', 'discount' => 40, 'purchase_price' => 60, 'sales_price' => 40, 'item_amount' => 1200, 'tax_vat' => 13, 'grn_id' => 60],
        //     ['id' => 2, 'purchase_qty' => 10, 'free_qty' => 60, 'mst_items_id' => 62, 'received_qty' => 720, 'deleted_uq_code' => 1, 'sup_org_id' => 2, 'created_at' => $now, 'invoice_qty' => 70, 'total_qty' => 100, 'batch_no' => 'b1', 'discount' => 40, 'purchase_price' => 60, 'sales_price' => 40, 'item_amount' => 1200, 'tax_vat' => 13, 'grn_id' => 60],
        //     ['id' => 3, 'purchase_qty' => 103, 'free_qty' => 6023, 'mst_items_id' => 66, 'received_qty' => 370, 'deleted_uq_code' => 1, 'sup_org_id' => 2, 'created_at' => $now, 'invoice_qty' => 70, 'total_qty' => 100, 'batch_no' => 'b2', 'discount' => 40, 'purchase_price' => 60, 'sales_price' => 40, 'item_amount' => 1200, 'tax_vat' => 13, 'grn_id' => 61],
        // ]);
        // DB::statement("SELECT SETVAL('grn_items_id_seq',100)");


        DB::table('batch_qty_detail')->insert([
            ['id' => 1, 'item_id' => 61, 'batch_no' => 'b1', 'store_id'=>1,'batch_from'=>'grn','batch_qty' => 70, 'batch_price' => 200, 'sup_org_id' => 2, 'created_by' => 3, 'created_at' => $now],
            ['id' => 2, 'item_id' => 62, 'batch_no' => 'b1', 'store_id'=>1,'batch_from'=>'stock_mgmt','batch_qty' => 720, 'batch_price' => 150, 'sup_org_id' => 2, 'created_by' => 3, 'created_at' => $now],
            ['id' => 3, 'item_id' => 63, 'batch_no' => 'b2', 'store_id'=>1,'batch_from'=>'grn','batch_qty' => 370, 'batch_price' => 300, 'sup_org_id' => 2, 'created_by' => 3, 'created_at' => $now],
        ]);
        DB::statement("SELECT SETVAL('batch_qty_detail_id_seq',100)");

        DB::table('item_qty_detail')->insert([
            ['id' => 1, 'item_id' => 61,'store_id'=>1, 'item_qty' => 550, 'created_by' => 3, 'deleted_uq_code' => 1, 'sup_org_id' => 2, 'created_at' => $now],
            ['id' => 2, 'item_id' => 62,'store_id'=>1, 'item_qty' => 620, 'created_by' => 3, 'deleted_uq_code' => 1, 'sup_org_id' => 2, 'created_at' => $now],
            ['id' => 3, 'item_id' => 63,'store_id'=>1, 'item_qty' => 980, 'created_by' => 3, 'deleted_uq_code' => 1, 'sup_org_id' => 2, 'created_at' => $now],
        ]);
        DB::statement("SELECT SETVAL('item_qty_detail_id_seq',100)");



        DB::table('return_reasons')->insert([
            ['id' => 1, 'code' => 1, 'name_en' =>'Reason 1', 'deleted_uq_code' => 1, 'sup_org_id' => 2, 'created_at' => $now, 'is_active'=>true,'created_by'=>1],
            ['id' => 2, 'code' => 2, 'name_en' =>'Reason 2', 'deleted_uq_code' => 1, 'sup_org_id' => 2, 'created_at' => $now, 'is_active'=>true,'created_by'=>1],
        ]);
        DB::statement("SELECT SETVAL('return_reasons_id_seq',100)");

        DB::table('mst_asset_types')->insert([
            ['id' => 7, 'code' => 7, 'name_lc' => 'PPE (Property, Plant, and Equipment)', 'name_en' => 'PPE (Property, Plant, and Equipment)', 'description' => 'PPE (Property, Plant, and Equipment)', 'is_active' => true, 'sup_org_id' => 1],
            ['id' => 8, 'code' => 8, 'name_lc' => 'Land', 'name_en' => 'Land', 'description' => 'Land', 'is_active' => true, 'sup_org_id' => 2],
            ['id' => 9, 'code' => 9, 'name_lc' => 'Buildings', 'name_en' => 'Buildings', 'description' => 'Buildings', 'is_active' => true, 'sup_org_id' => 2],
            ['id' => 10, 'code' => 10, 'name_lc' => 'Vehicles', 'name_en' => 'Vehicles', 'description' => 'Vehicles', 'is_active' => true, 'sup_org_id' => 2],
            ['id' => 11, 'code' => 11, 'name_lc' => 'Furniture', 'name_en' => 'Furniture', 'description' => 'Furniture', 'is_active' => true, 'sup_org_id' => 2],
            ['id' => 12, 'code' => 12, 'name_lc' => 'Machinery', 'name_en' => 'Machinery', 'description' => 'Machinery', 'is_active' => true, 'sup_org_id' => 2],
        ]);

        DB::statement("SELECT SETVAL('mst_asset_types_id_seq',100)");

        $permissions = Permission::all();
        $organization_admin_role = Role::find(2);
        $organization_admin_role->givePermissionTo($permissions);

        //assign role for superadmin
        $orgUser = User::findOrFail(2);
        $orgUser->assignRoleCustom("organizationadmin", $orgUser->id);




    }
}
