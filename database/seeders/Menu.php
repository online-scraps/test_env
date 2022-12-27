<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Menu extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('menu_items')->insert([
        //     ['id' => 1001,'name_lc' => 'Organization', 'name_en' => 'Organization', 'icon_picker'=> 'fas fa-building'],
        //     ['id' => 1002,'name_lc' => 'Super Master', 'name_en' => 'Super Master', 'icon_picker'=> 'fas fa-user-cog'],
        //     ['id' => 1003,'name_lc' => 'Primary Master', 'name_en' => 'Primary Master', 'icon_picker'=> 'fas fa-users-cog'],
        //     ['id' => 1004,'name_lc' => 'Meta', 'name_en' => 'Meta', 'icon_picker'=> 'fas fa-file-invoice'],
        //     ['id' => 1005,'name_lc' => 'Sales', 'name_en' => 'Sales', 'icon_picker'=> 'fas fa-file-invoice-dollar'],
        //     ['id' => 1006,'name_lc' => 'Inventory Management', 'name_en' => 'Inventory Management', 'icon_picker'=> 'fas fa-truck-moving'],
        //     ['id' => 1007,'name_lc' => 'Stock Management', 'name_en' => 'Stock Management', 'icon_picker'=> 'fas fa-boxes'],
        //     ['id' => 1008,'name_lc' => 'HR', 'name_en' => 'HR', 'icon_picker'=> 'fas fa-user-tie'],
        //     ['id' => 1009,'name_lc' => 'Menu Management', 'name_en' => 'Menu Management', 'icon_picker'=> 'fas fa-bars'],
        //     ['id' => 1010,'name_lc' => 'Settings', 'name_en' => 'Settings', 'icon_picker'=> 'fas fa-cog'],
        //     ['id' => 1011,'name_lc' => 'Users Management', 'name_en' => 'Users Management', 'icon_picker'=> 'fas fa-users'],
        //     ['id' => 1012,'name_lc' => 'Reports', 'name_en' => 'Reports', 'icon_picker'=> 'fas fa-book'],
        // ]);

        // DB::statement("SELECT SETVAL('menu_items_id_seq',12)");
    }
}
