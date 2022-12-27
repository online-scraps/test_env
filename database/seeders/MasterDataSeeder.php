<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $password = bcrypt('Admin@1234');

        DB::select(DB::raw("
            INSERT INTO public.sup_organizations (id,code,name_en,name_lc,country_id,province_id,district_id,address,email,phone_no,logo,description,multiple_barcode,is_active,created_by,updated_by,deleted_by,deleted_uq_code,deleted_at,created_at,updated_at)
            VALUES (101,'1','BIDH Management Pvt.Ltd','BIDH Management Pvt.Ltd',1,3,30,'Kupandole-10, Lalitpur','bidh@bidhgroup.com','9802323567','InventoryManagement\Logo/0bebecb1762035febcfaa44da1e96470.png',NULL,True,True,1,NULL,NULL,1,NULL,'2022-07-01 09:51:42','2022-07-01 09:51:42');
        "));

        DB::statement("SELECT SETVAL('sup_organizations_id_seq',150)");

        DB::select(DB::raw("
            INSERT INTO public.mst_stores (id,code,name_en,name_lc,sup_org_id,address,email,parent_id,phone_no,is_active,created_by,updated_by,deleted_by,deleted_uq_code,deleted_at,created_at,updated_at)
            VALUES (101,'1','BIDH Management Store','BIDH Management Store',101,'Kupandole-10','bidh@bidhgroup.com',null,'9802323567',True,1,NULL,NULL,1,NULL,'2022-07-01 09:51:42','2022-07-01 09:51:42');
        "));
        DB::statement("SELECT SETVAL('mst_stores_id_seq',150)");

        DB::select(DB::raw("
            INSERT INTO public.users (id,name,email,password,sup_org_id,user_level,store_id,is_active,is_admin,is_due_approver,is_discount_approver,is_stock_approver,is_po_approver)
            VALUES (101,'BIDH Management Pvt.Ltd','bidh@bidhgroup.com','$password',101,2,101,True,True,True,True,True,True);
        "));
        DB::statement("SELECT SETVAL('users_id_seq',150)");

        DB::select(DB::raw("
            INSERT INTO public.mst_categories (id,code,name_en,name_lc,description,is_active,sup_org_id,created_by,updated_by,deleted_by,is_deleted,deleted_at,deleted_uq_code,created_at,updated_at, is_super_data)
            VALUES (1,'1','CAMERA',NULL,NULL,True,101,101,NULL,NULL,NULL,NULL,1,'2022-07-03 04:25:38','2022-07-03 04:25:38', false),
            (2,'2','XVR/DVR',NULL,NULL,True,101,101,NULL,NULL,NULL,NULL,1,'2022-07-03 04:25:38','2022-07-03 04:25:38', false),
            (3,'3','NVR',NULL,NULL,True,101,101,NULL,NULL,NULL,NULL,1,'2022-07-03 04:25:38','2022-07-03 04:25:38', false),
            (4,'4','POE SWITCH',NULL,NULL,True,101,101,NULL,NULL,NULL,NULL,1,'2022-07-03 04:25:38','2022-07-03 04:25:38', false),
            (5,'5','STORAGE',NULL,NULL,True,101,101,NULL,NULL,NULL,NULL,1,'2022-07-03 04:25:38','2022-07-03 04:25:38', false),
            (6,'6','MONITOR',NULL,NULL,True,101,101,NULL,NULL,NULL,NULL,1,'2022-07-03 04:25:38','2022-07-03 04:25:38', false),
            (7,'7','ACCESSORIES',NULL,NULL,True,101,101,NULL,NULL,NULL,NULL,1,'2022-07-03 04:25:39','2022-07-03 04:25:39', false);
        "));
        DB::statement("SELECT SETVAL('mst_categories_id_seq',150)");

        DB::select(DB::raw("
            INSERT INTO public.mst_subcategories (id,code,name_en,name_lc,category_id,is_active,sup_org_id,created_by,updated_by,deleted_by,is_deleted,deleted_at,deleted_uq_code,created_at,updated_at, is_super_data)
            VALUES (1,'1','HDCVI CAMERA DOME',NULL,1,True,101,101,NULL,NULL,NULL,NULL,1,'2022-07-03 04:25:38','2022-07-03 04:25:38', false),
            (2,'2','HDCVI CAMERA BULLET ',NULL,1,True,101,101,NULL,NULL,NULL,NULL,1,'2022-07-03 04:25:38','2022-07-03 04:25:38', false),
            (3,'3','XVR/DVR ',NULL,2,True,101,101,NULL,NULL,NULL,NULL,1,'2022-07-03 04:25:38','2022-07-03 04:25:38', false),
            (4,'4','IP CAMERA DOME',NULL,1,True,101,101,NULL,NULL,NULL,NULL,1,'2022-07-03 04:25:38','2022-07-03 04:25:38', false),
            (5,'5','IP CAMERA BULLET',NULL,1,True,101,101,NULL,NULL,NULL,NULL,1,'2022-07-03 04:25:38','2022-07-03 04:25:38', false),
            (6,'6','PTZ CAMERA',NULL,1,True,101,101,NULL,NULL,NULL,NULL,1,'2022-07-03 04:25:38','2022-07-03 04:25:38', false),
            (7,'7','NVR ',NULL,3,True,101,101,NULL,NULL,NULL,NULL,1,'2022-07-03 04:25:38','2022-07-03 04:25:38', false),
            (8,'8','POE SWITCH',NULL,4,True,101,101,NULL,NULL,NULL,NULL,1,'2022-07-03 04:25:38','2022-07-03 04:25:38', false),
            (9,'9','HARD DISK DRIVE',NULL,5,True,101,101,NULL,NULL,NULL,NULL,1,'2022-07-03 04:25:38','2022-07-03 04:25:38', false),
            (10,'10','LCD MONITOR',NULL,6,True,101,101,NULL,NULL,NULL,NULL,1,'2022-07-03 04:25:38','2022-07-03 04:25:38', false),
            (11,'11','SSD',NULL,5,True,101,101,NULL,NULL,NULL,NULL,1,'2022-07-03 04:25:39','2022-07-03 04:25:39', false),
            (12,'12','ACCESSORIES',NULL,7,True,101,101,NULL,NULL,NULL,NULL,1,'2022-07-03 04:25:39','2022-07-03 04:25:39', false);
        "));
        DB::statement("SELECT SETVAL('mst_subcategories_id_seq',150)");

        DB::table('public.mst_suppliers')->insert([
            ['id' => 1, 'code' => 1, 'name_en' => 'Nepa Hima Trade Link', 'name_lc' => 'Nepa Hima Trade Link', 'address' => 'M8M8+3CX, Lalitpur 44600', 'email' => 'info@nepahima.com', 'contact_number' => '9851042545', 'is_active' => true, 'sup_org_id' => 101, 'created_by' => 101, 'deleted_uq_code' => 1, 'created_at' => '2022-07-01 10:35:35', 'updated_at' => '2022-07-01 10:35:35', 'is_customer' => false, 'is_super_data' => false]
        ]);

        DB::statement("SELECT SETVAL('mst_suppliers_id_seq',150)");


        DB::select(DB::raw("
            INSERT INTO public.mst_brands (id,code,name_en,name_lc,description,is_active,sup_org_id,created_at,updated_at,deleted_at,created_by,updated_by,deleted_by,deleted_uq_code, is_super_data)
            VALUES (101,'2','DAHUA','DAHUA',NULL,True,101,'2022-07-03 04:20:23','2022-07-03 04:20:23',NULL,101,NULL,NULL,1, false);
        "));
        DB::statement("SELECT SETVAL('mst_brands_id_seq',150)");

        DB::select(DB::raw("
            INSERT INTO public.mst_units (id,sup_org_id,code,name_en,name_lc,is_active,created_by,updated_by,deleted_by,is_deleted,deleted_at,deleted_uq_code,created_at,updated_at)
            VALUES (101,101,'4','PCS','PCS',True,104,NULL,NULL,NULL,NULL,1,'2022-07-01 10:35:48','2022-07-12 08:47:59'),
            (102,101,'5','MTRS','MTRS',True,104,NULL,NULL,NULL,NULL,1,'2022-07-12 08:47:39','2022-07-12 08:47:39'),
            (103,101,'6','ROLLS','ROLLS',True,104,NULL,NULL,NULL,NULL,1,'2022-07-12 08:47:46','2022-07-12 08:47:46');
        "));
        DB::statement("SELECT SETVAL('mst_units_id_seq',150)");
        DB::select(DB::raw("
            INSERT INTO public.mst_items (id,code,barcode_details,name,description,category_id,subcategory_id,supplier_id,brand_id,unit_id,item_price,stock_alert_minimum,tax_vat,discount_mode_id,is_damaged,is_taxable,is_nonclaimable,is_staffdiscount,is_price_editable,is_active,sup_org_id,created_by,updated_by,deleted_by,deleted_uq_code,deleted_at,created_at,updated_at,child_store_id, is_super_data)
            VALUES (1,'1',NULL,'DH-HAC-T1A21P',NULL,1,1,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (2,'2',NULL,'DH-HAC-HDW1200RP',NULL,1,1,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (3,'3',NULL,'DH-HAC-HDW1200TLP-A',NULL,1,1,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (4,'4',NULL,'DH-HAC-HDW1239TLQP-LED',NULL,1,1,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (5,'5',NULL,'DH-HAC-HDW1209TLQP-LED',NULL,1,1,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (6,'6',NULL,'DH-HAC-HDW1200TRQP-A',NULL,1,1,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (7,'7',NULL,'DH-HAC-HDW1509TLQP-LED',NULL,1,1,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (8,'8',NULL,'DH-HAC-B1A21P',NULL,1,2,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (9,'9',NULL,'DH-HAC-HFW1200TLP',NULL,1,2,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (10,'10',NULL,'DH-HAC-HFW1200CP',NULL,1,2,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (11,'11',NULL,'DH-HAC-HFW1239CP-A-LED',NULL,1,2,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (12,'12',NULL,'DH-HAC-HFW1209CP-LED',NULL,1,2,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (13,'13',NULL,'DH-HAC-HFW1509TP-LED',NULL,1,2,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (14,'14',NULL,'DH-HAC-HFW1200CP-A',NULL,1,2,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (15,'15',NULL,'DH-HAC-HFW1500CP',NULL,1,2,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (16,'16',NULL,'DH-XVR1B04-I',NULL,2,3,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (17,'17',NULL,'DH-XVR1B16-I',NULL,2,3,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (18,'18',NULL,'DHI-XVR5232AN-S2',NULL,2,3,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (19,'19',NULL,'DH-XVR1B08-I',NULL,2,3,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (20,'20',NULL,'DH-XVR4216AN-I',NULL,2,3,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (21,'21',NULL,'DH-IPC-HDBW1230E-S5',NULL,1,4,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (22,'22',NULL,'DH-IPC-HDW1230S-S5',NULL,1,4,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (23,'23',NULL,'DH-IPC-HDW1431T1-A-S4',NULL,1,4,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (24,'24',NULL,'DH-IPC-HDW1230T1-ZS-S5',NULL,1,4,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (25,'25',NULL,'DH-IPC-HDW1431T1P-ZS-S4',NULL,1,4,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (26,'26',NULL,'DH-IPC-HDW2531TP-AS-S2',NULL,1,4,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (27,'27',NULL,'DH-IPC-HDW1239T1-LED-S5',NULL,1,4,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (28,'28',NULL,'DH-IPC-HDW2230T-AS-S2',NULL,1,4,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (29,'29',NULL,'DH-IPC-HDW1230T1-S5',NULL,1,4,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (30,'30',NULL,'DH-IPC-HDW2239TP-AS-LED-S2',NULL,1,4,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (31,'31',NULL,'DH-IPC-HDW2439TP-AS-LED-S2',NULL,1,4,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (32,'32',NULL,'DH-IPC-HFW1230S-S5',NULL,1,5,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (33,'33',NULL,'DH-IPC-HFW1230M-A-I2-B-S5',NULL,1,5,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (34,'34',NULL,'DH-IPC-HFW1431S1-A-S4',NULL,1,5,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (35,'35',NULL,'DH-IPC-HFW2439SP-SA-LED-S2',NULL,1,5,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (36,'36',NULL,'DH-IPC-HFW2531SP-S-S2',NULL,1,5,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (37,'37',NULL,'DH-IPC-HFW2831TP-ZS-S2',NULL,1,5,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (38,'38',NULL,'DH-IPC-HFW1230S1-S5',NULL,1,5,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (39,'39',NULL,'DH-IPC-HFW1230T1-ZS-S5',NULL,1,5,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (40,'40',NULL,'DH-IPC-HFW1431T1P-ZS-S4',NULL,1,5,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (41,'41',NULL,'DH-IPC-HFW1239S1-LED-S5',NULL,1,5,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (42,'42',NULL,'DH-IPC-HFW2230S-S-S2',NULL,1,5,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (43,'43',NULL,'DH-IPC-HFW2831SP-S-S2',NULL,1,5,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (44,'44',NULL,'DH-IPC-HFW2239MP-AS-LED-B-S2',NULL,1,5,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (45,'45',NULL,'DH-IPC-HFW2439MP-AS-LED-B-S2',NULL,1,5,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (46,'46',NULL,'DH-SD49225XA-HNR-S2',NULL,1,6,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (47,'47',NULL,'DHI-NVR1104HS-S3/H',NULL,3,7,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (48,'48',NULL,'DHI-NVR2208-I',NULL,3,7,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (49,'49',NULL,'DHI-NVR4116HS-4KS2/L',NULL,3,7,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (50,'50',NULL,'DHI-NVR4232-4KS2/L',NULL,3,7,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (51,'51',NULL,'DHI-NVR4432-4KS2/I',NULL,3,7,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (52,'52',NULL,'DHI-NVR1108HS-S3/H',NULL,3,7,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (53,'53',NULL,'DHI-NVR5832-4KS2',NULL,3,7,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (54,'54',NULL,'DHI-NVR5464-4KS2',NULL,3,7,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (55,'55',NULL,'DHI-NVR5864-4KS2',NULL,3,7,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (56,'56',NULL,'DHI-NVR4416-4KS2/I',NULL,3,7,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (57,'57',NULL,'DHI-NVR5208-4KS2',NULL,3,7,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (58,'58',NULL,'DHI-NVR4216-4KS2/L',NULL,3,7,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (59,'59',NULL,'DH-PFS3006-4ET-36',NULL,4,8,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (60,'60',NULL,'DH-PFS3005-5GT-L',NULL,4,8,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (61,'61',NULL,'DH-PFS3008-8GT-L',NULL,4,8,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (62,'62',NULL,'DH-PFS3005-4ET-36',NULL,4,8,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (63,'63',NULL,'DH-PFS3010-8ET-65',NULL,4,8,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (64,'64',NULL,'DH-PFS3117-16ET-135',NULL,4,8,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (65,'65',NULL,'DH-PFS3024-24GT',NULL,4,8,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (66,'66',NULL,'DH-PFS4226-24ET-240',NULL,4,8,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (67,'67',NULL,'DH-PFS3016-16GT',NULL,4,8,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (68,'68',NULL,'DH-PFS3010-8ET-96',NULL,4,8,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (69,'69',NULL,'DH-PFS4218-16ET-240',NULL,4,8,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (70,'70',NULL,'ST1000VX008',NULL,5,9,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (71,'71',NULL,'ST2000VX007',NULL,5,9,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (72,'72',NULL,'ST2000VX012',NULL,5,9,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (73,'73',NULL,'ST4000VX005',NULL,5,9,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (74,'74',NULL,'ST6000VX001',NULL,5,9,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (75,'75',NULL,'ST8000VX004',NULL,5,9,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-08-21 08:09:14',NULL, false),
            (76,'76',NULL,'DHI-LM22-B200A',NULL,6,10,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (77,'77',NULL,'DHI-LM19-B200A',NULL,6,10,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:38','2022-07-03 04:25:38',NULL, false),
            (78,'78',NULL,'DHI-SSD-C800AS120G',NULL,5,11,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:39','2022-07-03 04:25:39',NULL, false),
            (79,'79',NULL,'DHI-SSD-C800AS240G',NULL,5,11,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:39','2022-07-03 04:25:39',NULL, false),
            (80,'80',NULL,'DHI-SSD-E900N256G',NULL,5,11,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:39','2022-07-03 04:25:39',NULL, false),
            (81,'81',NULL,'DHI-SSD-E900N512G',NULL,5,11,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:39','2022-07-03 04:25:39',NULL, false),
            (82,'82',NULL,'3+1 CABLE 90 YARD',NULL,7,12,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:39','2022-07-03 04:25:39',NULL, false),
            (83,'83',NULL,'BNC CONNECTOR',NULL,7,12,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:39','2022-07-03 04:25:39',NULL, false),
            (84,'84',NULL,'DC JACK WITH WIRE',NULL,7,12,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:39','2022-07-03 04:25:39',NULL, false),
            (85,'85',NULL,'4CH CCTV SMPS 404VBP',NULL,7,12,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:39','2022-07-03 04:25:39',NULL, false),
            (86,'86',NULL,'8CH CCTV SMPS 808VBP',NULL,7,12,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:39','2022-07-03 04:25:39',NULL, false),
            (87,'87',NULL,'CAT 6 CABLE 305MTR',NULL,7,12,1,101,101,0,'10','13',1,False,True,False,False,True,True,101,101,NULL,NULL,1,NULL,'2022-07-03 04:25:39','2022-07-03 04:25:39',NULL, false);
        "));
        DB::statement("SELECT SETVAL('mst_items_id_seq',150)");
    }
}
