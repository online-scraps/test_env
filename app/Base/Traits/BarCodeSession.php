<?php
namespace  App\Base\Traits;

use ReflectionClass;
use App\Base\DataAccessPermission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


/**
 *  CheckPermission
 */
trait BarCodeSession{

    public function barcodeSessionStoreTrait($request, $itemId, $batchId)
    {
        try {
            $count = 0;
            $barcodeDetails = array();
            $barcodeDetails_inside = array();
            if($request->barcode_details == null){
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Current barcode is not found in this Batch!!!'
                ]);
            }
            foreach($request->barcode_details as $barcode_dtl){
                $first_four_char = substr($barcode_dtl,0,4);
                $count = substr_count($barcode_dtl, $first_four_char);
                if($count > 3){
                    $sub_bar_code_details = explode($first_four_char,$barcode_dtl);
                    $sub_bar_code_details = array_values(array_filter($sub_bar_code_details));
                    foreach($sub_bar_code_details as $barcodeDetail){
                        $barcode[] = $first_four_char.$barcodeDetail;
                    }
                    $barcodeDetails_inside = $barcode;
                }else{
                    $barcodeDetails[] = $barcode_dtl;
                }
            }
            $barcodeDetails = array_merge($barcodeDetails, $barcodeDetails_inside);
            $db_exist_barcode = DB::table('stock_items_details')->select('barcode_details')->where('batch_no',$batchId)->where('item_id',$itemId)->get()->toArray();
            $db_exist_barcode_arr = [];
            foreach($db_exist_barcode as $barcode){
                $db_exist_barcode_arr[] = $barcode->barcode_details;
            }

            $barcodeDetails = array_intersect($db_exist_barcode_arr, $barcodeDetails);

            $count = count($barcodeDetails);
            if($count == 0){
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Failed to save barcodes. '
                ]);

            }
            $barcodeArrayWithId = [];
            foreach ($barcodeDetails as $detail)
                $barcodeArrayWithId['barcode-' . $itemId][$detail] = $itemId;

            if ($request->session()->has('barcode')) {
                $barcodeArray = $request->session()->pull('barcode');
                $barcodeArrayWithId = array_merge($barcodeArrayWithId, $barcodeArray);
                $request->session()->put('barcode', $barcodeArrayWithId);
            } else {
                $barcodeArray = ['barcode' => $barcodeArrayWithId];
                $request->session()->put($barcodeArray);
            }

            return response()->json([
                'status' => 'success',
                'count' => $count,
                'barcodeList' => getBarcodeJson($this->user->sup_org_id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to save barcodes. ' . $e->getMessage()
            ]);
        }
    }
}
