<?php

namespace App\Console\Commands;

use App\Http\Resources\BarcodeResource;
use App\Models\StockItemDetails;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateBarcodeList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'barcode-list:generate {super_org_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to generate list of used barcodes in an organization';
    /**
     * @var StockItemDetails
     */
    private $barcodeDetails;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(StockItemDetails $barcodeDetails)
    {
        parent::__construct();
        $this->barcodeDetails = $barcodeDetails;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $superOrgId = $this->argument('super_org_id');

        $barcodeArray = $this->barcodeDetails->where('sup_org_id',$superOrgId)->get();


        $barcodeArray = $barcodeArray->map(function ($item,$key){
            return [$item->barcode_details => [
                'item_id' => $item->item_id,
                'is_active' => $item->is_active
            ]];
        })->toArray();

        $new = array();
        foreach($barcodeArray as $value) {
            $new += $value;
        }
        $barcodeArray = json_encode($new);
        $path = storage_path('barcodes');

        $this->info('Checking whether the barcode folder exists');

        if(!file_exists($path)){
            $this->info('No folder to store barcodes');
            $this->info('Creating a folder to store barcodes');

            mkdir($path, 0777, true);

            File::put($path.'/barcode-'.$superOrgId.'.json',$barcodeArray);

            $this->info("barcode-$superOrgId.json File generated successfully");

            return 0;
        }

        if(!file_exists($path."/barcode-$superOrgId")){
            File::put($path.'/barcode-'.$superOrgId.'.json',$barcodeArray);

            $this->info("barcode-$superOrgId.json File generated successfully");
        }else{
            File::put($path.'/barcode-'.$superOrgId.'-temp.json',$barcodeArray);

            unlink($path."/barcode-$superOrgId.json");

            rename($path."/barcode-$superOrgId-temp.json",$path."/barcode-$superOrgId.json");

            $this->info("barcode-$superOrgId.json File generated successfully");
        }
        return 0;
    }
}
