<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\MstProvince;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now()->toDateTimeString();
        $this->time = $now;
        $this->clean_tables();
        $this->mstCountries();
        $this->mstProvinces();
        $this->mstDistricts();
        $this->mstGenders();
        $this->mstSequence();
    }


    private function clean_tables(){
        DB::table('mst_countries')->delete();
        DB::table('mst_provinces')->delete();
        DB::table('mst_districts')->delete();
        DB::table('mst_genders')->delete();
        DB::table('mst_sequences')->delete();
    }


    private function mstCountries()
    {

        DB::table('mst_countries')->insert([
            array('id' => 1,'code' => 'nep','name_en' => 'Nepal','name_lc' => 'नेपाल','deleted_uq_code'=>1,'created_at'=>$this->time),
        ]);
        DB::statement("SELECT SETVAL('mst_provinces_id_seq',100)");
    }

    private function mstProvinces()
    {
        DB::table('mst_provinces')->insert([
            array('id' => 1,'code' => '1','country_id'=>1,'name_en' => 'Province 1','name_lc' => 'प्रदेश १','deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 2,'code' => '2','country_id'=>1,'name_en' => 'Madesh Province','name_lc' => 'मधेस प्रदेश','deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 3,'code' => '3','country_id'=>1,'name_en' => 'Bagmati Province','name_lc' => 'बागमती प्रदेश','deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 4,'code' => '4','country_id'=>1,'name_en' => 'Gandaki Province','name_lc' => 'गण्डकी प्रदेश','deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 5,'code' => '5','country_id'=>1,'name_en' => 'Lumbini Province','name_lc' => 'लुम्बिनी प्रदेश','deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 6,'code' => '6','country_id'=>1,'name_en' => 'Karnali Province','name_lc' => 'कर्णाली प्रदेश','deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 7,'code' => '7','country_id'=>1,'name_en' => 'SudurPashchim Province','name_lc' => 'सुदूरपश्चिम प्रदेश','deleted_uq_code'=>1,'created_at'=>$this->time),
        ]);
        DB::statement("SELECT SETVAL('mst_provinces_id_seq',100)");
    }

    public function mstDistricts()
    {
        DB::table('mst_districts')->insert([
            array('id' => 1,'code' => '101','name_en' => 'TAPLEJUNG','name_lc' => 'ताप्लेजुङ','province_id' => 1,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 2,'code' => '102','name_en' => 'SANKHUWASABHA','name_lc' => 'संखुवासभा','province_id' => 1,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 3,'code' => '103','name_en' => 'SOLUKHUMBU','name_lc' => 'सोलुखुम्बु','province_id' => 1,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 4,'code' => '104','name_en' => 'OKHALDHUNGA','name_lc' => 'ओखलढुङ्गा','province_id' => 1,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 5,'code' => '105','name_en' => 'KHOTANG','name_lc' => 'खोटाङ','province_id' => 1,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 6,'code' => '106','name_en' => 'BHOJPUR','name_lc' => 'भोजपुर','province_id' => 1,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 7,'code' => '107','name_en' => 'DHANKUTA','name_lc' => 'धनकुटा','province_id' => 1,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 8,'code' => '108','name_en' => 'TERHATHUM','name_lc' => 'तेह्रथुम','province_id' => 1,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 9,'code' => '109','name_en' => 'PANCHTHAR','name_lc' => 'पाँचथर','province_id' => 1,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 10,'code' => '110','name_en' => 'ILAM','name_lc' => 'इलाम','province_id' => 1,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 11,'code' => '111','name_en' => 'JHAPA','name_lc' => 'झापा','province_id' => 1,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 12,'code' => '112','name_en' => 'MORANG','name_lc' => 'मोरङ','province_id' => 1,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 13,'code' => '113','name_en' => 'SUNSARI','name_lc' => 'सुनसरी','province_id' => 1,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 14,'code' => '114','name_en' => 'UDAYAPUR','name_lc' => 'उदयपुर','province_id' => 1,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 15,'code' => '201','name_en' => 'SAPTARI','name_lc' => 'सप्तरी','province_id' => 2,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 16,'code' => '202','name_en' => 'SIRAHA','name_lc' => 'सिरहा','province_id' => 2,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 17,'code' => '203','name_en' => 'DHANUSA','name_lc' => 'धनुषा','province_id' => 2,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 18,'code' => '204','name_en' => 'MAHOTTARI','name_lc' => 'महोत्तरी','province_id' => 2,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 19,'code' => '205','name_en' => 'SARLAHI','name_lc' => 'सर्लाही','province_id' => 2,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 20,'code' => '206','name_en' => 'RAUTAHAT','name_lc' => 'रौतहट','province_id' => 2,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 21,'code' => '207','name_en' => 'BARA','name_lc' => 'बारा','province_id' => 2,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 22,'code' => '208','name_en' => 'PARSA','name_lc' => 'पर्सा','province_id' => 2,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 23,'code' => '301','name_en' => 'DOLAKHA','name_lc' => 'दोलखा','province_id' => 3,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 24,'code' => '302','name_en' => 'SINDHUPALCHOK','name_lc' => 'सिन्धुपाल्चोक','province_id' => 3,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 25,'code' => '303','name_en' => 'RASUWA','name_lc' => 'रसुवा','province_id' => 3,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 26,'code' => '304','name_en' => 'DHADING','name_lc' => 'धादिङ','province_id' => 3,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 27,'code' => '305','name_en' => 'NUWAKOT','name_lc' => 'नुवाकोट','province_id' => 3,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 28,'code' => '306','name_en' => 'KATHMANDU','name_lc' => 'काठमाडौँ','province_id' => 3,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 29,'code' => '307','name_en' => 'BHAKTAPUR','name_lc' => 'भक्तपुर','province_id' => 3,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 30,'code' => '308','name_en' => 'LALITPUR','name_lc' => 'ललितपुर','province_id' => 3,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 31,'code' => '309','name_en' => 'KAVREPALANCHOK','name_lc' => 'काभ्रेपलाञ्चोक','province_id' => 3,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 32,'code' => '310','name_en' => 'RAMECHHAP','name_lc' => 'रामेछाप','province_id' => 3,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 33,'code' => '311','name_en' => 'SINDHULI','name_lc' => 'सिन्धुली','province_id' => 3,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 34,'code' => '312','name_en' => 'MAKWANPUR','name_lc' => 'मकवानपुर','province_id' => 3,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 35,'code' => '313','name_en' => 'CHITAWAN','name_lc' => 'चितवन','province_id' => 3,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 36,'code' => '401','name_en' => 'GORKHA','name_lc' => 'गोरखा','province_id' => 4,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 37,'code' => '402','name_en' => 'MANANG','name_lc' => 'मनाङ','province_id' => 4,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 38,'code' => '403','name_en' => 'MUSTANG','name_lc' => 'मुस्ताङ','province_id' => 4,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 39,'code' => '404','name_en' => 'MYAGDI','name_lc' => 'म्याग्दी','province_id' => 4,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 40,'code' => '405','name_en' => 'KASKI','name_lc' => 'कास्की','province_id' => 4,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 41,'code' => '406','name_en' => 'LAMJUNG','name_lc' => 'लमजुङ','province_id' => 4,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 42,'code' => '407','name_en' => 'TANAHU','name_lc' => 'तनहुँ','province_id' => 4,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 43,'code' => '408','name_en' => 'NAWALPARASI EAST','name_lc' => 'नवलपरासी ((बर्दघाट सुस्ता पूूर्व)','province_id' => 4,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 44,'code' => '409','name_en' => 'SYANGJA','name_lc' => 'स्याङजा','province_id' => 4,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 45,'code' => '410','name_en' => 'PARBAT','name_lc' => 'पर्वत','province_id' => 4,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 46,'code' => '411','name_en' => 'BAGLUNG','name_lc' => 'बागलुङ','province_id' => 4,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 47,'code' => '501','name_en' => 'RUKUM EAST','name_lc' => 'रुकुम (पूर्वी भाग)','province_id' => 5,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 48,'code' => '502','name_en' => 'ROLPA','name_lc' => 'रोल्पा','province_id' => 5,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 49,'code' => '503','name_en' => 'PYUTHAN','name_lc' => 'प्यूठान','province_id' => 5,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 50,'code' => '504','name_en' => 'GULMI','name_lc' => 'गुल्मी','province_id' => 5,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 51,'code' => '505','name_en' => 'ARGHAKHANCHI','name_lc' => 'अर्घाखाँची','province_id' => 5,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 52,'code' => '506','name_en' => 'PALPA','name_lc' => 'पाल्पा','province_id' => 5,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 53,'code' => '507','name_en' => 'NAWALPARASI WEST','name_lc' => 'नवलपरासी (बर्दघाट सुस्ता पश्चिम)','province_id' => 5,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 54,'code' => '508','name_en' => 'RUPANDEHI','name_lc' => 'रुपन्देही','province_id' => 5,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 55,'code' => '509','name_en' => 'KAPILBASTU','name_lc' => 'कपिलवस्तु','province_id' => 5,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 56,'code' => '510','name_en' => 'DANG','name_lc' => 'दाङ','province_id' => 5,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 57,'code' => '511','name_en' => 'BANKE','name_lc' => 'बाँके','province_id' => 5,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 58,'code' => '512','name_en' => 'BARDIYA','name_lc' => 'बर्दिया','province_id' => 5,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 59,'code' => '601','name_en' => 'DOLPA','name_lc' => 'डोल्पा','province_id' => 6,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 60,'code' => '602','name_en' => 'MUGU','name_lc' => 'मुगु','province_id' => 6,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 61,'code' => '603','name_en' => 'HUMLA','name_lc' => 'हुम्ला','province_id' => 6,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 62,'code' => '604','name_en' => 'JUMLA','name_lc' => 'जुम्ला','province_id' => 6,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 63,'code' => '605','name_en' => 'KALIKOT','name_lc' => 'कालिकोट','province_id' => 6,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 64,'code' => '606','name_en' => 'DAILEKH','name_lc' => 'दैलेख','province_id' => 6,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 65,'code' => '607','name_en' => 'JAJARKOT','name_lc' => 'जाजरकोट','province_id' => 6,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 66,'code' => '608','name_en' => 'RUKUM WEST','name_lc' => 'रुकुम (पश्चिम भाग)','province_id' => 6,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 67,'code' => '609','name_en' => 'SALYAN','name_lc' => 'सल्यान','province_id' => 6,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 68,'code' => '610','name_en' => 'SURKHET','name_lc' => 'सुर्खेत','province_id' => 6,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 69,'code' => '701','name_en' => 'BAJURA','name_lc' => 'बाजुरा','province_id' => 7,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 70,'code' => '702','name_en' => 'BAJHANG','name_lc' => 'बझाङ','province_id' => 7,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 71,'code' => '703','name_en' => 'DARCHULA','name_lc' => 'दार्चुला','province_id' => 7,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 72,'code' => '704','name_en' => 'BAITADI','name_lc' => 'बैतडी','province_id' => 7,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 73,'code' => '705','name_en' => 'DADELDHURA','name_lc' => 'डँडेलधुरा','province_id' => 7,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 74,'code' => '706','name_en' => 'DOTI','name_lc' => 'डोटी','province_id' => 7,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 75,'code' => '707','name_en' => 'ACHHAM','name_lc' => 'अछाम','province_id' => 7,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 76,'code' => '708','name_en' => 'KAILALI','name_lc' => 'कैलाली','province_id' => 7,'deleted_uq_code'=>1,'created_at'=>$this->time),
            array('id' => 77,'code' => '709','name_en' => 'KANCHANPUR','name_lc' => 'कञ्चनपुर','province_id' => 7,'deleted_uq_code'=>1,'created_at'=>$this->time),

        ]);
        DB::statement("SELECT SETVAL('mst_districts_id_seq',100)");
    }

    private function mstGenders()
    {
        DB::table('mst_genders')->insert(
            [
                array('id' => 1, 'code' => 'm', 'name_en' => 'Male', 'name_lc' => 'पुरुष','deleted_uq_code'=>1,'created_at'=>$this->time ),
                array('id' => 2, 'code' => 'f', 'name_en' => 'Female', 'name_lc' => 'महिला','deleted_uq_code'=>1,'created_at'=>$this->time),
                array('id' => 3, 'code' => 't', 'name_en' => 'Third Gender', 'name_lc' => 'तेश्रो लिंगी','deleted_uq_code'=>1,'created_at'=>$this->time),
                array('id' => 4, 'code' => 'o', 'name_en' => 'Others', 'name_lc' => 'अन्य','deleted_uq_code'=>1,'created_at'=>$this->time),
            ]
        );
        DB::statement("SELECT SETVAL('mst_genders_id_seq',1000)");
    }

    private function mstSequence()
    {
        DB::table('mst_sequences')->insert(
            [
                array('id' => 1, 'code' => 1, 'name_en' => 'Batch No', 'name_lc' => 'Batch No', 'sequence_type' => 1, 'sequence_code' => 'BTCH', 'is_active' =>true, 'deleted_uq_code'=>1,'created_at'=>$this->time, 'is_super_data' => true),
                array('id' => 2, 'code' => 2, 'name_en' => 'Goods Received Note Sequence', 'name_lc' => 'Goods Received Note Sequence', 'sequence_type' => 2, 'sequence_code' => 'GRN',  'is_active' =>true, 'deleted_uq_code'=>1,'created_at'=>$this->time, 'is_super_data' => true),
                array('id' => 3, 'code' => 3, 'name_en' => 'Invoice Sequence', 'name_lc' => 'Invoice Sequence', 'sequence_type' => 3, 'sequence_code' => 'INV', 'is_active' =>true,   'deleted_uq_code'=>1,'created_at'=>$this->time, 'is_super_data' => true),
                array('id' => 4, 'code' => 4, 'name_en' => 'Purchase order Sequence', 'name_lc' => 'Purchase order Sequence', 'sequence_type' => 4, 'sequence_code' => 'PO', 'is_active' =>true,  'deleted_uq_code'=>1,'created_at'=>$this->time, 'is_super_data' => true),
                array('id' => 5, 'code' => 5, 'name_en' => 'Purchase Return Sequence', 'name_lc' => 'Purchase Return Sequence', 'sequence_type' => 5, 'sequence_code' => 'PR', 'is_active' =>true,  'deleted_uq_code'=>1,'created_at'=>$this->time, 'is_super_data' => true),
                array('id' => 6, 'code' => 6, 'name_en' => 'Stock Adjustment Sequence', 'name_lc' => 'Stock Adjustment Sequence', 'sequence_type' => 6, 'sequence_code' => 'SAS', 'is_active' =>true,  'deleted_uq_code'=>1,'created_at'=>$this->time, 'is_super_data' => true),
                array('id' => 7, 'code' => 7, 'name_en' => 'Sales Return Sequence', 'name_lc' => 'Sales Return Sequence', 'sequence_type' => 7, 'sequence_code' => 'SR', 'is_active' =>true,  'deleted_uq_code'=>1,'created_at'=>$this->time,  'is_super_data' => true),
                array('id' => 8, 'code' => 8, 'name_en' => 'Chalan Entry Sequence', 'name_lc' => 'Chalan Entry Sequence', 'sequence_type' => 8, 'sequence_code' => 'CN', 'is_active' =>true,  'deleted_uq_code'=>1,'created_at'=>$this->time,  'is_super_data' => true),
            ]
        );
        DB::statement("SELECT SETVAL('mst_sequences_id_seq',100)");
    }
}
