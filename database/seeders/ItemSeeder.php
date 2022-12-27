<?php

namespace Database\Seeders;

use App\Models\MstItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedData();
    }
    public function seedData()
    {
        $filename = __DIR__.'/item_lists.csv';
        $the_big_array = [];
        // Open the file for reading
        if (($h = fopen("{$filename}", "r")) !== false) {
            // Each line in the file is converted into an individual array that we call $data
            // The items of the array are comma separated
            // dd(fgetcsv($h, 1000, ","));
            while(($data = fgetcsv($h, 1000, ",")) !== false) {
                // Each individual array is being pushed into the nested array
                $the_big_array[] = $data;
            }
            // Close the file
            fclose($h);
            // dd($the_big_array);
        }
        // Display the code in a readable format
        foreach ($the_big_array as $key => $value) {
            // dd($value);
            $res['name'] = $value[0];
            $res['category'] = $value[1];
            $res['sub_category'] = $value[2];
            $res['description'] = $value[3];
            $res['supplier'] = $value[4];
            // $res['store'] = $value[5];
            $res['brand'] = $value[6];
            $res['unit'] = $value[7];
            // $res['item-price'] = $value[8];
            $res['sup_org_id'] = $value[9];
            $res['created_by'] = $value[10];
            // dd($res);
            MstItem::insert([
                'name' => $res['name'],
                'category_id' => $res['category'],
                'subcategory_id' => $res['sub_category'] ,
                'description' => $res['description'],
                'supplier_id' =>  $res['supplier'],
                // 'store_id' =>$res['store'],
                'brand_id' =>$res['brand'],
                'unit_id' => $res['unit'] ,
                // 'item_price' => $res['item-price'],
                'sup_org_id' => $res['sup_org_id'],
                'created_by' =>  $res['created_by'] ,
                'code' =>  $res['name']
            ]);
        }
        DB::statement("SELECT SETVAL('mst_items_id_seq',1000)");

    }
}
